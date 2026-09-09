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
        $audit->load(['creator', 'approver', 'risks', 'findings.actionItems', 'workingPapers', 'qualityAssessments', 'teamMembers', 'report.preparer', 'report.seniorReviewer', 'report.chiefApprover']);

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

    public function index(Request $request)
    {
        $query = Report::with(['audit.teamMembers', 'preparer', 'seniorReviewer', 'chiefApprover']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comments', 'like', "%{$search}%")
                  ->orWhereHas('audit', function($aq) use ($search) {
                      $aq->where('audit_code', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->latest()->paginate(10);

        $counts = [
            'all' => Report::count(),
            'draft' => Report::whereIn('status', ['draft', 'returned_for_revision'])->count(),
            'pending_senior' => Report::where('status', 'pending_senior_review')->count(),
            'pending_chief' => Report::where('status', 'pending_chief_approval')->count(),
            'chief_approved' => Report::where('status', 'chief_approved')->count(),
            'final_issued' => Report::where('status', 'final_issued')->count(),
        ];

        return view('reports.index', compact('reports', 'counts'));
    }

    public function create(Request $request)
    {
        $selectedAuditId = $request->audit_id;
        $audit = $selectedAuditId ? Audit::with(['findings', 'workingPapers', 'risks', 'teamMembers'])->findOrFail($selectedAuditId) : null;
        
        // Audits without existing reports or eligible for reports
        $existingReportAuditIds = Report::pluck('audit_id')->toArray();
        $availableAudits = Audit::whereNotIn('id', $existingReportAuditIds)->orderBy('audit_code', 'desc')->get();

        return view('reports.create', compact('audit', 'availableAudits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audit_id' => 'required|exists:audits,id|unique:reports,audit_id',
            'title' => 'nullable|string|max:255',
            'executive_summary' => 'nullable|string',
            'scope' => 'nullable|string',
            'comments' => 'nullable|string',
            'draft_issue_date' => 'nullable|date',
            'submit_for_review' => 'nullable|boolean',
        ]);

        $audit = Audit::findOrFail($validated['audit_id']);
        $isSubmitting = $request->boolean('submit_for_review');

        $status = $isSubmitting ? 'pending_senior_review' : 'draft';
        $draftIssueDate = $validated['draft_issue_date'] ? \Carbon\Carbon::parse($validated['draft_issue_date']) : ($isSubmitting ? now() : now());

        $report = Report::create([
            'audit_id' => $validated['audit_id'],
            'title' => $validated['title'] ?? ('Audit Report: ' . $audit->title),
            'executive_summary' => $validated['executive_summary'] ?? null,
            'scope' => $validated['scope'] ?? null,
            'status' => $status,
            'prepared_by' => Auth::id(),
            'comments' => $validated['comments'] ?? null,
            'draft_issue_date' => $draftIssueDate,
            'review_notes' => [],
        ]);

        $message = $isSubmitting 
            ? 'Draft report prepared and submitted to Senior Internal Auditor for review.' 
            : 'Draft report created successfully.';

        return redirect()->route('reports.show', $report)->with('success', $message);
    }

    public function show(Report $report)
    {
        $report->load([
            'audit.creator',
            'audit.approver',
            'audit.risks',
            'audit.findings.actionItems.assignee',
            'audit.workingPapers.creator',
            'audit.teamMembers',
            'preparer',
            'seniorReviewer',
            'chiefApprover',
        ]);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $report->load('audit.teamMembers');
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'executive_summary' => 'nullable|string',
            'scope' => 'nullable|string',
            'comments' => 'nullable|string',
            'draft_issue_date' => 'nullable|date',
            'final_issue_date' => 'nullable|date',
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report)->with('success', 'Report updated successfully.');
    }

    public function submitForSeniorReview(Request $request, Report $report)
    {
        $draftIssueDate = $request->draft_issue_date ? \Carbon\Carbon::parse($request->draft_issue_date) : ($report->draft_issue_date ?: now());
        
        $report->update([
            'status' => 'pending_senior_review',
            'draft_issue_date' => $draftIssueDate,
        ]);

        if ($request->filled('note')) {
            $notes = $report->review_notes ?? [];
            $notes[] = [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'role' => 'Auditor / Preparer',
                'action' => 'Submitted Draft for Senior Review',
                'comment' => $request->note,
                'addressed' => true,
                'created_at' => now()->toDateTimeString(),
            ];
            $report->update(['review_notes' => $notes]);
        }

        return redirect()->route('reports.show', $report)->with('success', 'Draft report submitted to Senior Internal Auditor for review.');
    }

    public function submitForChiefApproval(Request $request, Report $report)
    {
        $notes = $report->review_notes ?? [];
        if ($request->filled('senior_comments')) {
            $notes[] = [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'role' => 'Senior Internal Auditor',
                'action' => 'Senior Review Sign-off',
                'comment' => $request->senior_comments,
                'addressed' => true,
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $report->update([
            'status' => 'pending_chief_approval',
            'senior_reviewer_id' => Auth::id(),
            'review_notes' => $notes,
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Senior review completed and forwarded to Chief Internal Auditor for approval.');
    }

    public function approveChief(Request $request, Report $report)
    {
        $notes = $report->review_notes ?? [];
        if ($request->filled('chief_comments')) {
            $notes[] = [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'role' => 'Chief Internal Auditor',
                'action' => 'Chief Approval',
                'comment' => $request->chief_comments,
                'addressed' => true,
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $report->update([
            'status' => 'chief_approved',
            'chief_approver_id' => Auth::id(),
            'review_notes' => $notes,
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Report approved by Chief Internal Auditor. It is now ready for Final Issuance.');
    }

    public function issueFinalReport(Request $request, Report $report)
    {
        $finalIssueDate = $request->final_issue_date ? \Carbon\Carbon::parse($request->final_issue_date) : now();

        $notes = $report->review_notes ?? [];
        $notes[] = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'role' => 'Chief Internal Auditor / Issuing Authority',
            'action' => 'Final Report Issued',
            'comment' => $request->issuance_notes ?? 'Final report officially issued.',
            'addressed' => true,
            'created_at' => now()->toDateTimeString(),
        ];

        $report->update([
            'status' => 'final_issued',
            'chief_approver_id' => $report->chief_approver_id ?: Auth::id(),
            'final_issue_date' => $finalIssueDate,
            'review_notes' => $notes,
        ]);

        // Also mark linked audit completed if not already
        if ($report->audit && $report->audit->status !== 'completed') {
            $report->audit->update([
                'status' => 'completed',
                'actual_end_date' => $report->audit->actual_end_date ?: now(),
            ]);
        }

        return redirect()->route('reports.show', $report)->with('success', 'Final Audit Report has been officially issued!');
    }

    public function returnForRevision(Request $request, Report $report)
    {
        $request->validate([
            'comments' => 'required|string',
        ]);

        $notes = $report->review_notes ?? [];
        $roleName = Auth::user()->hasRole('Audit Manager') || Auth::user()->position == 'Chief Internal Auditor' 
            ? 'Chief Internal Auditor' 
            : 'Senior Internal Auditor';

        $notes[] = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'role' => $roleName,
            'action' => 'Returned for Corrections',
            'comment' => $request->comments,
            'addressed' => false,
            'created_at' => now()->toDateTimeString(),
        ];

        $report->update([
            'status' => 'returned_for_revision',
            'comments' => $request->comments,
            'review_notes' => $notes,
        ]);

        return redirect()->route('reports.show', $report)->with('error', 'Report returned to audit team with comments and required corrections.');
    }

    public function rejectReport(Request $request, Report $report)
    {
        return $this->returnForRevision($request, $report);
    }

    public function addComment(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string',
            'type' => 'nullable|string|in:observation,correction,feedback,response',
        ]);

        $notes = $report->review_notes ?? [];
        $notes[] = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->position ?? 'Auditor',
            'type' => $request->type ?? 'feedback',
            'action' => 'Review Comment Added',
            'comment' => $request->comment,
            'addressed' => $request->type === 'response',
            'created_at' => now()->toDateTimeString(),
        ];

        $report->update(['review_notes' => $notes]);

        return back()->with('success', 'Review comment recorded.');
    }

    public function toggleCommentStatus(Request $request, Report $report, $index)
    {
        $notes = $report->review_notes ?? [];
        if (isset($notes[$index])) {
            $notes[$index]['addressed'] = !($notes[$index]['addressed'] ?? false);
            $notes[$index]['addressed_by'] = Auth::user()->name;
            $notes[$index]['addressed_at'] = now()->toDateTimeString();
            $report->update(['review_notes' => $notes]);
            return back()->with('success', 'Correction status updated.');
        }

        return back()->with('error', 'Comment index not found.');
    }

    public function updateDates(Request $request, Report $report)
    {
        $validated = $request->validate([
            'draft_issue_date' => 'nullable|date',
            'final_issue_date' => 'nullable|date',
        ]);

        $report->update($validated);

        return back()->with('success', 'Report issue dates updated.');
    }
}
