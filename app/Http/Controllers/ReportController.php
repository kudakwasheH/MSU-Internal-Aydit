<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\RiskRegister;
use App\Models\ActionItem;
use App\Models\Report;
use App\Models\AuditLog;
use App\Services\RiskApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $apiService;

    public function __construct(RiskApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function generate()
    {
        $risks = collect($this->apiService->fetchRisks());
        $audits = Audit::all();
        $data = [
            'audits' => $audits,
            'findingsBySeverity' => Finding::select('severity', DB::raw('count(*) as count'))->groupBy('severity')->pluck('count', 'severity'),
            'auditsByStatus' => Audit::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status'),
            'risksByCategory' => $risks->where('status', 'active')->groupBy('category')->map->count(),
            'overdueActions' => ActionItem::where('status', 'overdue')->count(),
            'overdueActionsList' => ActionItem::with(['finding', 'assignee'])->where('status', 'overdue')->latest()->take(5)->get(),
            'totalFindings' => Finding::count(),
            'resolvedFindings' => Finding::where('status', 'resolved')->count(),
            'auditsByType' => Audit::select('audit_type', DB::raw('count(*) as count'))->groupBy('audit_type')->pluck('count', 'audit_type'),
        ];
        return view('reports.generate', $data);
    }

    public function download(Audit $audit)
    {
        $audit->load(['creator', 'approver', 'risks', 'findings.actionItems', 'workingPapers', 'qualityAssessments']);

        $pdf = Pdf::loadView('reports.pdf', compact('audit'));
        $pdf->setPaper('a4');
        return $pdf->stream("Audit_Report_{$audit->audit_code}.pdf");
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('module')) $query->where('module', $request->module);
        if ($request->filled('action')) $query->where('action', $request->action);

        $logs = $query->paginate(20);
        return view('reports.audit-logs', compact('logs'));
    }

    public function meetingPack()
    {
        $data = [
            'totalAudits' => Audit::count(),
            'completedAudits' => Audit::where('status', 'completed')->count(),
            'inProgressAudits' => Audit::where('status', 'in_progress')->count(),
            'highRiskFindings' => Finding::whereIn('severity', ['high', 'critical'])->whereNotIn('status', ['closed', 'resolved'])->count(),
            'agingFindings' => Finding::whereNotIn('status', ['closed', 'resolved'])
                ->where('created_at', '<', now()->subDays(90))
                ->count(),
            'riskCoverage' => $this->calculateRiskCoverage(),
            'recentEscalations' => Finding::where('escalation_level', '>', 0)->latest()->take(5)->get(),
            'auditsByType' => Audit::select('audit_type', DB::raw('count(*) as count'))->groupBy('audit_type')->pluck('count', 'audit_type'),
        ];

        return view('reports.meeting_pack', $data);
    }

    public function rollingPlan()
    {
        $audits = Audit::orderBy('planned_start_date')->get();
        return view('reports.rolling_plan', compact('audits'));
    }

    private function calculateRiskCoverage()
    {
        $risks = collect($this->apiService->fetchRisks());
        $totalRisks = $risks->count();
        if ($totalRisks === 0) return 0;
        
        $coveredRisks = DB::table('audit_risks')->distinct('risk_id')->count('risk_id');
        return round(($coveredRisks / $totalRisks) * 100);
    }

    public function index()
    {
        $reports = Report::with(['audit', 'preparer', 'seniorReviewer', 'chiefApprover'])->latest()->paginate(10);
        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $audit = Audit::findOrFail($request->audit_id);
        return view('reports.create', compact('audit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'comments' => 'nullable|string',
        ]);

        if (Report::where('audit_id', $validated['audit_id'])->exists()) {
            return redirect()->route('reports.index')->with('error', 'A report for this audit already exists.');
        }

        $report = Report::create([
            'audit_id' => $validated['audit_id'],
            'status' => 'draft',
            'prepared_by' => Auth::id(),
            'comments' => $validated['comments'],
            'draft_issue_date' => now(),
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Draft report created.');
    }

    public function show(Report $report)
    {
        $report->load(['audit', 'preparer', 'seniorReviewer', 'chiefApprover']);
        return view('reports.show', compact('report'));
    }

    public function submitForSeniorReview(Report $report)
    {
        $report->update(['status' => 'pending_senior_review']);
        return redirect()->route('reports.show', $report)->with('success', 'Report submitted for Senior Review.');
    }

    public function submitForChiefApproval(Report $report)
    {
        $report->update([
            'status' => 'pending_chief_approval',
            'senior_reviewer_id' => Auth::id(),
        ]);
        return redirect()->route('reports.show', $report)->with('success', 'Report forwarded to Chief Internal Auditor.');
    }

    public function issueFinalReport(Report $report)
    {
        $report->update([
            'status' => 'final_issued',
            'chief_approver_id' => Auth::id(),
            'final_issue_date' => now(),
        ]);
        return redirect()->route('reports.show', $report)->with('success', 'Final Report Issued successfully.');
    }

    public function rejectReport(Request $request, Report $report)
    {
        $request->validate(['comments' => 'required|string']);
        $report->update([
            'status' => 'draft',
            'comments' => $request->comments,
        ]);
        return redirect()->route('reports.show', $report)->with('error', 'Report rejected and returned to draft status.');
    }
}
