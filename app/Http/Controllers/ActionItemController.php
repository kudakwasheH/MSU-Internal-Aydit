<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use App\Models\AuditLog;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionItemController extends Controller
{
    public function index(Request $request)
    {
        $query = ActionItem::with(['finding.audit', 'assignee']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->where('action_description', 'like', "%{$request->search}%");
        }

        $actionItems = $query->latest()->paginate(10);
        return view('action-items.index', compact('actionItems'));
    }

    public function create()
    {
        $findings = Finding::whereIn('status', ['open', 'in_progress'])->get();
        $users = User::all();
        return view('action-items.create', compact('findings', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'finding_id' => 'required|exists:findings,id',
            'action_description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
        ]);

        $validated['status'] = 'pending';
        $item = ActionItem::create($validated);
        AuditLog::log('create', 'action', $item->id, null, $item->toArray());

        return redirect()->route('action-items.show', $item)->with('success', 'Action item created.');
    }

    public function show(ActionItem $actionItem)
    {
        $actionItem->load(['finding.audit', 'assignee']);
        return view('action-items.show', compact('actionItem'));
    }

    public function edit(ActionItem $actionItem)
    {
        $findings = Finding::all();
        $users = User::all();
        return view('action-items.edit', compact('actionItem', 'findings', 'users'));
    }

    public function update(Request $request, ActionItem $actionItem)
    {
        $validated = $request->validate([
            'action_description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed,overdue',
            'comments' => 'nullable|string',
        ]);

        $oldData = $actionItem->toArray();
        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $actionItem->update($validated);
        AuditLog::log('update', 'action', $actionItem->id, $oldData, $actionItem->fresh()->toArray());

        return redirect()->route('action-items.show', $actionItem)->with('success', 'Action item updated.');
    }

    public function destroy(ActionItem $actionItem)
    {
        AuditLog::log('delete', 'action', $actionItem->id, $actionItem->toArray(), null);
        $actionItem->delete();
        return redirect()->route('action-items.index')->with('success', 'Action item deleted.');
    }

    public function complete(ActionItem $actionItem)
    {
        $oldData = $actionItem->toArray();
        $actionItem->update(['status' => 'completed', 'completed_at' => now()]);
        AuditLog::log('update', 'action', $actionItem->id, $oldData, $actionItem->fresh()->toArray());

        return redirect()->route('action-items.show', $actionItem)->with('success', 'Action item marked complete.');
    }
}
