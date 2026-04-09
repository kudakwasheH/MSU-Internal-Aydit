<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\Audit;
use App\Models\AuditLog;
use App\Models\Escalation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        $query = Finding::with(['audit', 'assignee', 'creator']);

        if ($request->filled('severity')) $query->where('severity', $request->severity);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('audit_id')) $query->where('audit_id', $request->audit_id);
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $findings = $query->latest()->paginate(10);
        return view('findings.index', compact('findings'));
    }

    public function create()
    {
        $audits = Audit::whereIn('status', ['in_progress'])->get();
        $users = User::all();
        return view('findings.create', compact('audits', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'root_cause' => 'required|string',
            'impact' => 'required|string',
            'recommendation' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open';
        $validated['escalation_level'] = 1;

        $finding = Finding::create($validated);
        AuditLog::log('create', 'finding', $finding->id, null, $finding->toArray());

        return redirect()->route('findings.show', $finding)->with('success', 'Finding recorded successfully.');
    }

    public function show(Finding $finding)
    {
        $finding->load(['audit', 'creator', 'assignee', 'actionItems.assignee', 'escalations.escalatedToUser']);
        return view('findings.show', compact('finding'));
    }

    public function edit(Finding $finding)
    {
        $audits = Audit::whereIn('status', ['in_progress', 'completed'])->get();
        $users = User::all();
        return view('findings.edit', compact('finding', 'audits', 'users'));
    }

    public function update(Request $request, Finding $finding)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'root_cause' => 'required|string',
            'impact' => 'required|string',
            'recommendation' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $oldData = $finding->toArray();
        $finding->update($validated);
        AuditLog::log('update', 'finding', $finding->id, $oldData, $finding->fresh()->toArray());

        return redirect()->route('findings.show', $finding)->with('success', 'Finding updated successfully.');
    }

    public function destroy(Finding $finding)
    {
        AuditLog::log('delete', 'finding', $finding->id, $finding->toArray(), null);
        $finding->delete();
        return redirect()->route('findings.index')->with('success', 'Finding deleted.');
    }

    public function escalate(Request $request, Finding $finding)
    {
        $request->validate([
            'reason' => 'required|string',
            'escalated_to' => 'required|exists:users,id',
        ]);

        $oldLevel = $finding->escalation_level;
        $newLevel = $oldLevel + 1;

        Escalation::create([
            'finding_id' => $finding->id,
            'escalated_from_level' => $oldLevel,
            'escalated_to_level' => $newLevel,
            'escalated_to' => $request->escalated_to,
            'reason' => $request->reason,
            'status' => 'pending',
            'escalated_by' => Auth::id(),
        ]);

        $finding->update(['escalation_level' => $newLevel]);
        AuditLog::log('escalate', 'finding', $finding->id, ['escalation_level' => $oldLevel], ['escalation_level' => $newLevel]);

        return redirect()->route('findings.show', $finding)->with('success', 'Finding escalated successfully.');
    }
}
