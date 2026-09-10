<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditLog;
use App\Models\RiskRegister;
use App\Models\User;
use App\Services\RiskApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    protected $apiService;
    protected $erpService;

    public function __construct(RiskApiService $apiService, \App\Services\ErpService $erpService)
    {
        $this->apiService = $apiService;
        $this->erpService = $erpService;
    }

    public function index(Request $request)
    {
        $query = Audit::with(['creator', 'approver', 'risks', 'teamMembers', 'report']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('audit_type', $request->type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('audit_code', 'like', "%{$request->search}%");
            });
        }

        $audits = $query->latest()->paginate(10);
        $allUsers = User::orderBy('name')->get();
        return view('audits.index', compact('audits', 'allUsers'));
    }

    public function assignTeam(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
        ]);

        $audit->teamMembers()->sync($validated['team_members'] ?? []);

        return back()->with('success', 'Audit team members updated successfully for ' . $audit->audit_code);
    }

    public function create()
    {
        \Illuminate\Support\Facades\Artisan::call('risk:sync');
        $risks = \App\Models\RiskRegister::where('status', 'active')->get();
        $users = User::all();
        $budgetCodes = $this->erpService->fetchBudgetCodes();
        $complianceRefs = $this->erpService->fetchComplianceReferences();
        return view('audits.create', compact('risks', 'users', 'budgetCodes', 'complianceRefs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audit_type' => 'required|in:internal,external,it,compliance,performance',
            'priority' => 'required|in:high,medium,low',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after:planned_start_date',
            'risk_ids' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->input('status', 'draft'), ['planned', 'in_progress', 'completed']) && empty($value)) {
                        $fail('You must link at least one risk to this audit.');
                    }
                },
            ],
            'risk_ids.*' => 'exists:risk_registers,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'budget_code' => 'nullable|string',
            'compliance_ref' => 'nullable|string',
        ]);

        $year = date('Y');
        $lastCode = Audit::where('audit_code', 'like', "AUD-$year-%")->orderBy('audit_code', 'desc')->first();
        $nextNum = $lastCode ? intval(substr($lastCode->audit_code, -4)) + 1 : 1;
        $auditCode = "AUD-$year-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $audit = Audit::create([
            'audit_code' => $auditCode,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'audit_type' => $validated['audit_type'],
            'priority' => $validated['priority'],
            'status' => 'draft',
            'planned_start_date' => $validated['planned_start_date'],
            'planned_end_date' => $validated['planned_end_date'],
            'created_by' => Auth::id(),
            'budget_code' => $validated['budget_code'] ?? null,
            'compliance_ref' => $validated['compliance_ref'] ?? null,
        ]);

        $audit->risks()->attach($validated['risk_ids'] ?? []);
        if (!empty($validated['team_members'])) {
            $audit->teamMembers()->attach($validated['team_members']);
        }

        return redirect()->route('audits.show', $audit)->with('success', 'Audit created successfully.');
    }

    public function show(Audit $audit)
    {
        $audit->load(['creator', 'approver', 'risks', 'findings.assignee', 'workingPapers', 'qualityAssessments', 'teamMembers', 'report.preparer', 'report.seniorReviewer', 'report.chiefApprover']);
        $allUsers = User::orderBy('name')->get();
        return view('audits.show', compact('audit', 'allUsers'));
    }

    public function edit(Audit $audit)
    {
        \Illuminate\Support\Facades\Artisan::call('risk:sync');
        $risks = \App\Models\RiskRegister::where('status', 'active')->get();
        $users = User::all();
        $budgetCodes = $this->erpService->fetchBudgetCodes();
        $complianceRefs = $this->erpService->fetchComplianceReferences();
        $audit->load(['risks', 'teamMembers']);
        return view('audits.edit', compact('audit', 'risks', 'users', 'budgetCodes', 'complianceRefs'));
    }

    public function update(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audit_type' => 'required|in:internal,external,it,compliance,performance',
            'priority' => 'required|in:high,medium,low',
            'status' => 'required|in:draft,planned,in_progress,completed,cancelled',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after:planned_start_date',
            'risk_ids' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request, $audit) {
                    if (in_array($request->input('status', $audit->status), ['planned', 'in_progress', 'completed']) && empty($value)) {
                        $fail('You must link at least one risk to this audit.');
                    }
                },
            ],
            'risk_ids.*' => 'exists:risk_registers,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'budget_code' => 'nullable|string',
            'compliance_ref' => 'nullable|string',
        ]);

        $oldData = $audit->toArray();

        if ($validated['status'] === 'in_progress' && !$audit->actual_start_date) {
            $validated['actual_start_date'] = now();
        }
        if ($validated['status'] === 'completed' && !$audit->actual_end_date) {
            $validated['actual_end_date'] = now();
        }

        $riskIds = $validated['risk_ids'] ?? [];
        unset($validated['risk_ids']);
        
        $teamMembers = $validated['team_members'] ?? [];
        unset($validated['team_members']);

        $audit->update($validated);
        $audit->risks()->sync($riskIds);
        $audit->teamMembers()->sync($teamMembers);

        return redirect()->route('audits.show', $audit)->with('success', 'Audit updated successfully.');
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('audits.index')->with('success', 'Audit deleted.');
    }

    public function approve(Audit $audit)
    {
        $audit->update(['status' => 'planned', 'approved_by' => Auth::id()]);
        return redirect()->route('audits.show', $audit)->with('success', 'Audit approved.');
    }

    public function submit(Audit $audit)
    {
        $audit->update(['status' => 'in_progress', 'actual_start_date' => now()]);
        return redirect()->route('audits.show', $audit)->with('success', 'Audit submitted for execution.');
    }
}
