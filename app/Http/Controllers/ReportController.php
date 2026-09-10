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

    public function downloadSystemManual()
    {
        $pdf = Pdf::loadView('reports.system_manual_pdf');
        $pdf->setPaper('a4');
        return $pdf->stream("MSU_Internal_Audit_System_Manual_and_Workflows.pdf");
    }

    public function downloadSystemManualWord()
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\ComplexType\ProofState());

        // ── Document defaults ──────────────────────────────────────────────
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);
        $phpWord->setDefaultParagraphStyle(['spaceAfter' => 120, 'lineHeight' => 1.15]);

        // ── Named styles ───────────────────────────────────────────────────
        $phpWord->addTitleStyle(1, [
            'name' => 'Calibri', 'size' => 16, 'bold' => true,
            'color' => 'FFFFFF',
        ], [
            'shading' => ['type' => 'clear', 'color' => 'auto', 'fill' => '004EA1'],
            'spaceAfter' => 200, 'spaceBefore' => 0,
        ]);
        $phpWord->addTitleStyle(2, [
            'name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => '004EA1',
        ], [
            'borderBottomSize' => 6, 'borderBottomColor' => '004EA1',
            'spaceBefore' => 280, 'spaceAfter' => 120,
        ]);
        $phpWord->addTitleStyle(3, [
            'name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => '2B6CB0',
        ], ['spaceBefore' => 180, 'spaceAfter' => 80]);

        // Convenience style constants
        $bodyText   = ['name' => 'Calibri', 'size' => 10];
        $boldBody   = ['name' => 'Calibri', 'size' => 10, 'bold' => true];
        $smallGray  = ['name' => 'Calibri', 'size' => 9,  'color' => '718096'];
        $goldBold   = ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => 'B7791F'];
        $paraStyle  = ['spaceAfter' => 100, 'lineHeight' => 1.2];

        // ── Table cell helper colours ──────────────────────────────────────
        $headerCell = ['bgColor' => 'EBF4FF', 'borderSize' => 6, 'borderColor' => 'BEE3F8'];
        $bodyCell   = ['borderSize' => 6, 'borderColor' => 'E2E8F0'];

        // ══════════════════════════════════════════════════════════════════
        //  SECTION — Cover / Title Block
        // ══════════════════════════════════════════════════════════════════
        $section = $phpWord->addSection([
            'marginTop' => 700, 'marginBottom' => 700,
            'marginLeft' => 900, 'marginRight' => 900,
        ]);

        // Blue header block
        $section->addTitle('MIDLANDS STATE UNIVERSITY', 1);

        $sub = $section->addTextRun(['alignment' => 'center', 'spaceAfter' => 60]);
        $sub->addText('Internal Audit Directorate', ['name' => 'Calibri', 'size' => 13, 'bold' => true, 'color' => 'FFCC00']);

        $sub2 = $section->addTextRun(['alignment' => 'center', 'spaceAfter' => 200]);
        $sub2->addText('System Operations & Workflow Manual — Official User Guide', $smallGray);

        $section->addTextRun(['spaceAfter' => 60])->addText(
            'Document Reference:  MSU-IAD-SYS-001', $smallGray
        );
        $section->addTextRun(['spaceAfter' => 60])->addText(
            'Issued:  ' . now()->format('d F Y'), $smallGray
        );
        $section->addTextRun(['spaceAfter' => 60])->addText(
            'Classification:  INTERNAL — CONFIDENTIAL', $boldBody
        );

        $section->addTextBreak(2);

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 1 — Executive Overview
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('1. Executive Overview & System Architecture', 2);

        $section->addText(
            'The MSU Internal Audit Management System is an enterprise governance, risk, and audit execution platform tailored for Midlands State University. Built to uphold the standards of the Institute of Internal Auditors (IIA), the system digitalises the end-to-end audit lifecycle from strategic annual planning down to finding remediation.',
            $bodyText, $paraStyle
        );

        $section->addTitle('Core Modules & Capabilities', 3);
        $modules = [
            'Audit Universe'               => 'Annual rolling audit planning, engagement scheduling, and multi-auditor team allocation.',
            'Risk Management'              => 'Integrated Risk Register, Inherent & Residual risk scoring, and interactive 5×5 Heat Map.',
            'Working Papers & Fieldwork'   => 'Electronic audit testing papers, methodology templates, and evidentiary document storage.',
            'Finding Tracker'              => 'Comprehensive deficiency cataloguing, 5-level risk escalation, and root-cause mapping.',
            'Action Items'                 => 'Management corrective action tracking, automated overdue alerts, and remediation follow-up.',
            'Reports Centre'               => '4-stage review and approval governance, transmittal tracking, and official PDF / Word generation.',
            'Executive Governance'         => 'Role-tailored dashboards for the Vice Chancellor, Audit Committee, and University Council.',
        ];
        foreach ($modules as $title => $desc) {
            $li = $section->addListItemRun(0, 'bullet');
            $li->addText($title . ': ', $boldBody);
            $li->addText($desc, $bodyText);
        }

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 2 — Approval Levels
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('2. Sign-off Authority & Approval Levels (1–5)', 2);
        $section->addText(
            'The system enforces a dual-layer security model comprising System Roles (interface permissions) and Approval Levels (hierarchical sign-off authority and delegation limits):',
            $bodyText, $paraStyle
        );

        $levelTable = $section->addTable([
            'borderSize' => 6, 'borderColor' => 'CBD5E0',
            'cellMargin' => 80, 'width' => 9200, 'unit' => 'dxa',
        ]);

        // Header row
        $levelTable->addRow();
        foreach (['Level', 'Position / Tier', 'Sign-off Authority & Operational Scope'] as $h) {
            $cell = $levelTable->addCell(null, $headerCell);
            $cell->addText($h, ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => '2D3748']);
        }

        $levels = [
            ['Level 1', 'Field / Operational Auditor (Junior Auditor, Audit Assistant)',
             "• Executes test procedures and records fieldwork data.\n• Prepares working papers and uploads supporting audit evidence.\n• Drafts initial audit observations, root causes, and draft reports.\n• Does NOT have authority to approve plans or issue final reports."],
            ['Level 2', 'Supervisory Reviewer (Senior Auditor, Risk Officer)',
             "• Performs first-line quality review on working papers and audit evidence.\n• Reviews draft audit reports prepared by Level 1 auditors.\n• Logs required corrections or endorses reports for Chief Auditor approval.\n• Validates enterprise risk ratings and escalates critical findings."],
            ['Level 3', 'Management / Chief Auditor (Audit Manager, Chief Internal Auditor)',
             "• Approves Audit Universe engagements and authorises audit commencement.\n• Conducts executive reviews on draft reports and endorses quality compliance.\n• Formally authorises and issues Official Final Audit Reports.\n• Verifies management remediation and formally closes resolved findings."],
            ['Level 4', 'Executive & Governance (Audit Committee, Vice Chancellor, Council)',
             "• High-level university governance oversight.\n• Accesses Executive & Governance Dashboards and Quarterly Meeting Packs.\n• Evaluates university-wide strategic risk posture, audit cycle times, and unresolved high-risk exposures."],
            ['Level 5', 'System Administrator (System Admin, IT Directorate)',
             "• Root configuration, staff account provisioning, and role assignment.\n• System integration management (Google SSO, ERP budget linkage, Risk sync).\n• Monitors immutable system audit logs and application health."],
        ];

        foreach ($levels as $row) {
            $levelTable->addRow();
            $levelTable->addCell(900, $bodyCell)->addText($row[0], $boldBody);
            $levelTable->addCell(2600, $bodyCell)->addText($row[1], $bodyText);
            $levelTable->addCell(5700, $bodyCell)->addText($row[2], $bodyText);
        }

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 3 — Role Workflows
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('3. Operational Workflow Guide by Role & Level', 2);

        $workflows = [
            'Level 1: Field Auditor Workflow' => [
                'Receives allocation to an active audit engagement in the Audit Universe.',
                'Navigates to Working Papers, creates working papers associated with linked university risks, and attaches test evidence files.',
                'Identifies internal control gaps and creates records in the Finding Tracker (noting criteria, condition, cause, effect, and recommendation).',
                'Upon conclusion of testing, opens Reports Centre → Prepare New Report, fills the executive summary, methodology, and transmittal comments.',
                'Submits the draft report to the Senior Internal Auditor (Status: pending_senior_review; Draft Issue Date is captured).',
                'If returned with comments, addresses the requested corrections and resubmits.',
            ],
            'Level 2: Senior Internal Auditor Workflow' => [
                'Accesses assigned engagements and reviews submitted working papers and test sampling.',
                'Opens the draft report in Reports Centre. Evaluates completeness and accuracy.',
                'If corrections required: Clicks "Request Corrections", enters specific observations. Report status → returned_for_revision.',
                'If quality standards met: Clicks "Endorse & Forward to Chief" with senior endorsement notes. Report → pending_chief_approval.',
                'Evaluates finding risk ratings and triggers formal escalation levels if management response is overdue.',
            ],
            'Level 3: Audit Manager / Chief Internal Auditor Workflow' => [
                'Creates audit engagements in the Audit Universe, links ERP budget codes, and defines planned dates.',
                'Uses the "Assign Audit Team" tool to allocate Lead Auditors and team members to engagements.',
                'Reviews engagement charters and approves draft plans (Status: planned → in_progress).',
                'Reviews reports forwarded by Senior Auditors.',
                'Clicks "Authorize & Issue Final Report" — captures Final Issue Date, locks the report as final_issued, marks the audit completed, and watermarks the official PDF.',
                'Reviews verified action items and formally marks findings as resolved/closed.',
            ],
            'Level 4: Executive Management, Audit Committee & Council' => [
                'Reviews real-time KPIs covering university risk coverage %, audit cycle times, and finding resolution velocity.',
                'Downloads pre-compiled executive meeting packs containing summarised finding aging and high-risk exposures.',
                'Analyses dynamic risk distribution across faculties, departments, and compliance mandates.',
            ],
            'Level 5: System Administrator & IT Lead' => [
                'Creates user profiles with mandatory @staff.msu.ac.zw emails, positions, and approval levels (1–5).',
                'Enforces secure passwordless Google Workspace authentication.',
                'Reviews immutable system logs in System Logs to trace every login, record creation, update, and deletion.',
            ],
        ];

        foreach ($workflows as $heading => $steps) {
            $section->addTitle($heading, 3);
            foreach ($steps as $i => $step) {
                $li = $section->addListItemRun(0, 'multilevel');
                $li->addText(($i + 1) . '.  ' . $step, $bodyText);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 4 — Lifecycle Table
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('4. Comprehensive Audit Engagement Lifecycle', 2);

        $lcTable = $section->addTable([
            'borderSize' => 6, 'borderColor' => 'CBD5E0',
            'cellMargin' => 80, 'width' => 9200, 'unit' => 'dxa',
        ]);
        $lcTable->addRow();
        foreach (['Stage', 'Key Activities', 'Primary Actors', 'System Output / Deliverable'] as $h) {
            $lcTable->addCell(null, $headerCell)->addText($h, ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => '2D3748']);
        }

        $lifecycle = [
            ['1. Planning',       'Engagement creation, risk linkage, ERP budget coding',       'Audit Manager (L3), System Admin (L5)', 'Approved Audit Universe Plan (Code: AUD-YYYY-XXXX)'],
            ['2. Allocation',     'Assigning auditors & field team members',                     'Chief Auditor / Audit Manager (L3)',    'Engagement Team roster with avatar display'],
            ['3. Fieldwork',      'Executing tests, collecting evidence, sampling',              'Auditors / Field Team (L1)',            'Versioned Working Papers & uploaded evidence'],
            ['4. Findings',       'Identifying deficiencies & root causes',                     'Auditors (L1), Senior Reviewer (L2)',   'Finding records with severity & action items'],
            ['5. Draft Report',   'Drafting summary, methodology & initial issue',              'Audit Team Preparer (L1)',              'Draft Report (Draft Issue Date captured)'],
            ['6. Senior Review',  'Quality testing, correction logs, endorsement',              'Senior Internal Auditor (L2)',          'Endorsed Report or Correction Tracker items'],
            ['7. Chief Approval', 'Executive review, authorisation, final issue',               'Chief Internal Auditor (L3)',           'Official Final Report (Final Issue Date captured)'],
            ['8. Remediation',    'Action plan follow-up, evidence review, closure',            'Action Owners, Audit Manager (L3)',     'Closed findings & Governance Analytics'],
        ];

        foreach ($lifecycle as $row) {
            $lcTable->addRow();
            foreach ($row as $i => $cell) {
                $style = ($i === 0) ? $boldBody : $bodyText;
                $lcTable->addCell(null, $bodyCell)->addText($cell, $style);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 5 — Reports Centre Workflow Detail
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('5. Reports Centre — Review & Approval Workflow Detail', 2);

        $reportStages = [
            ['Draft Preparation',     'draft',                  'Audit Team (L1)',              'Report created with title, executive summary, scope, and draft issue date.'],
            ['Pending Senior Review', 'pending_senior_review',  'Senior Auditor (L2)',          'Draft submitted; Senior Auditor reviews quality and completeness.'],
            ['Pending Chief Approval','pending_chief_approval', 'Chief Internal Auditor (L3)',  'Senior-endorsed report; Chief Auditor performs executive review.'],
            ['Chief Approved',        'chief_approved',         'Chief Internal Auditor (L3)',  'Report approved; ready for official issuance.'],
            ['Returned for Revision', 'returned_for_revision',  'Any Reviewer',                 'Corrections required; audit team addresses comments and resubmits.'],
            ['Final Report Issued',   'final_issued',           'Chief Internal Auditor (L3)',  'Official Final Report released; Final Issue Date recorded; Audit marked Completed.'],
        ];

        $rfTable = $section->addTable([
            'borderSize' => 6, 'borderColor' => 'CBD5E0',
            'cellMargin' => 80, 'width' => 9200, 'unit' => 'dxa',
        ]);
        $rfTable->addRow();
        foreach (['Workflow Stage', 'System Status', 'Responsible Actor', 'Description'] as $h) {
            $rfTable->addCell(null, $headerCell)->addText($h, ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => '2D3748']);
        }
        foreach ($reportStages as $row) {
            $rfTable->addRow();
            $rfTable->addCell(1900, $bodyCell)->addText($row[0], $boldBody);
            $rfTable->addCell(2000, $bodyCell)->addText($row[1], ['name' => 'Courier New', 'size' => 9, 'color' => '2B6CB0']);
            $rfTable->addCell(2100, $bodyCell)->addText($row[2], $bodyText);
            $rfTable->addCell(3200, $bodyCell)->addText($row[3], $bodyText);
        }

        // ══════════════════════════════════════════════════════════════════
        //  SECTION 6 — Authentication & Security
        // ══════════════════════════════════════════════════════════════════
        $section->addTitle('6. Authentication & Security Policy', 2);

        $secPolicies = [
            'Single Sign-On (Google Workspace)' =>
                'All university staff authenticate securely via Google OAuth2 with their official @staff.msu.ac.zw credentials. No passwords are stored in the system.',
            'Granular Role-Based Access Control (RBAC)' =>
                'Managed through Spatie Permissions to guarantee separation of duties. Each permission is explicitly assigned per module and action.',
            'Tamper-Proof Audit Trails' =>
                'Every critical model action (create, update, delete, approve, issue) is logged with the user ID, timestamp, and before/after state diff in the immutable System Audit Log.',
            'Domain Enforcement' =>
                'User registration and account creation enforce @staff.msu.ac.zw email domain validation at both form and server level.',
        ];

        foreach ($secPolicies as $title => $desc) {
            $section->addTitle($title, 3);
            $section->addText($desc, $bodyText, $paraStyle);
        }

        // ── Footer ────────────────────────────────────────────────────────
        $footer = $section->addFooter();
        $footer->addPreserveText(
            'Midlands State University  ·  Internal Audit Directorate  ·  System Operations Manual  ·  Page {PAGE} of {NUMPAGES}',
            $smallGray, ['alignment' => 'center']
        );

        // ── Stream as .docx ───────────────────────────────────────────────
        $filename = 'MSU_Internal_Audit_System_Manual_and_Workflows.docx';
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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
