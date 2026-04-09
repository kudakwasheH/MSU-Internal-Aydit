<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditLog;
use App\Models\RiskRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['creator', 'approver', 'risks']);

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
        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        $risks = RiskRegister::where('status', 'active')->get();
        $users = User::all();
        return view('audits.create', compact('risks', 'users'));
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
            'risk_ids' => 'required|array|min:1',
            'risk_ids.*' => 'exists:risk_registers,id',
        ], [
            'risk_ids.required' => 'You must link at least one risk to this audit.',
            'risk_ids.min' => 'You must link at least one risk to this audit.',
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
        ]);

        $audit->risks()->attach($validated['risk_ids']);

        AuditLog::log('create', 'audit', $audit->id, null, $audit->toArray());

        return redirect()->route('audits.show', $audit)->with('success', 'Audit created successfully.');
    }

    public function show(Audit $audit)
    {
        $audit->load(['creator', 'approver', 'risks', 'findings.assignee', 'workingPapers', 'qualityAssessments']);
        return view('audits.show', compact('audit'));
    }

    public function edit(Audit $audit)
    {
        $risks = RiskRegister::where('status', 'active')->get();
        $users = User::all();
        $audit->load('risks');
        return view('audits.edit', compact('audit', 'risks', 'users'));
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
            'risk_ids' => 'required|array|min:1',
            'risk_ids.*' => 'exists:risk_registers,id',
        ]);

        $oldData = $audit->toArray();

        if ($validated['status'] === 'in_progress' && !$audit->actual_start_date) {
            $validated['actual_start_date'] = now();
        }
        if ($validated['status'] === 'completed' && !$audit->actual_end_date) {
            $validated['actual_end_date'] = now();
        }

        $riskIds = $validated['risk_ids'];
        unset($validated['risk_ids']);
        $audit->update($validated);
        $audit->risks()->sync($riskIds);

        AuditLog::log('update', 'audit', $audit->id, $oldData, $audit->fresh()->toArray());

        return redirect()->route('audits.show', $audit)->with('success', 'Audit updated successfully.');
    }

    public function destroy(Audit $audit)
    {
        AuditLog::log('delete', 'audit', $audit->id, $audit->toArray(), null);
        $audit->delete();
        return redirect()->route('audits.index')->with('success', 'Audit deleted.');
    }

    public function approve(Audit $audit)
    {
        $oldData = $audit->toArray();
        $audit->update(['status' => 'planned', 'approved_by' => Auth::id()]);
        AuditLog::log('approve', 'audit', $audit->id, $oldData, $audit->fresh()->toArray());
        return redirect()->route('audits.show', $audit)->with('success', 'Audit approved.');
    }

    public function submit(Audit $audit)
    {
        $oldData = $audit->toArray();
        $audit->update(['status' => 'in_progress', 'actual_start_date' => now()]);
        AuditLog::log('update', 'audit', $audit->id, $oldData, $audit->fresh()->toArray());
        return redirect()->route('audits.show', $audit)->with('success', 'Audit submitted for execution.');
    }
}
