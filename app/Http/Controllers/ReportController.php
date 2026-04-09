<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\RiskRegister;
use App\Models\ActionItem;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function generate()
    {
        $audits = Audit::with(['creator', 'findings'])->get();
        $data = [
            'audits' => $audits,
            'findingsBySeverity' => Finding::select('severity', DB::raw('count(*) as count'))->groupBy('severity')->pluck('count', 'severity'),
            'auditsByStatus' => Audit::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status'),
            'risksByCategory' => RiskRegister::where('status', 'active')->select('category', DB::raw('count(*) as count'))->groupBy('category')->pluck('count', 'category'),
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
        return $pdf->download("Audit_Report_{$audit->audit_code}.pdf");
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
        $totalRisks = RiskRegister::count();
        if ($totalRisks === 0) return 0;
        
        $coveredRisks = DB::table('audit_risks')->distinct('risk_id')->count('risk_id');
        return round(($coveredRisks / $totalRisks) * 100);
    }
}
