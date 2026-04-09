@extends('layouts.app')
@section('title', 'Finding Details')
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $finding->severity == 'critical' ? 'bg-red-100 text-red-700' : ($finding->severity == 'high' ? 'bg-orange-100 text-orange-700' : ($finding->severity == 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">{{ ucfirst($finding->severity) }}</span>
                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ ucwords(str_replace('_', ' ', $finding->status)) }}</span>
                    @if($finding->escalation_level > 1)<span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Escalation Level {{ $finding->escalation_level }}</span>@endif
                </div>
                <h2 class="text-xl font-bold text-[#333]">{{ $finding->title }}</h2>
                <p class="text-xs text-gray-400 mt-1">Audit: <a href="{{ route('audits.show', $finding->audit) }}" class="text-[#004ea1] hover:underline">{{ $finding->audit?->audit_code }}</a></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('findings.edit', $finding) }}" class="px-4 py-2 bg-[#ffcc00] text-[#333] text-sm rounded-lg"><i class="fas fa-edit mr-1"></i>Edit</a>
                @if($finding->requiresEscalation())
                <button onclick="document.getElementById('escalateModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg"><i class="fas fa-arrow-up mr-1"></i>Escalate</button>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t">
            <div><h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Description</h4><p class="text-sm text-gray-700">{{ $finding->description }}</p></div>
            <div><h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Root Cause</h4><p class="text-sm text-gray-700">{{ $finding->root_cause }}</p></div>
            <div><h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Impact</h4><p class="text-sm text-gray-700">{{ $finding->impact }}</p></div>
            <div><h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Recommendation</h4><p class="text-sm text-gray-700">{{ $finding->recommendation }}</p></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t">
            <div><p class="text-xs text-gray-500">Created By</p><p class="text-sm font-medium">{{ $finding->creator?->name }}</p></div>
            <div><p class="text-xs text-gray-500">Assigned To</p><p class="text-sm font-medium">{{ $finding->assignee?->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Created</p><p class="text-sm font-medium">{{ $finding->created_at->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-500">Age</p><p class="text-sm font-medium">{{ $finding->created_at->diffInDays(now()) }} days</p></div>
        </div>
    </div>

    {{-- Action Items --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700"><i class="fas fa-tasks text-[#004ea1] mr-2"></i>Action Items ({{ $finding->actionItems->count() }})</h3>
            <a href="{{ route('action-items.create') }}?finding_id={{ $finding->id }}" class="text-xs text-[#004ea1] hover:underline">+ Add Action</a>
        </div>
        @forelse($finding->actionItems as $action)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
            <div>
                <p class="text-sm text-gray-800">{{ Str::limit($action->action_description, 60) }}</p>
                <p class="text-xs text-gray-400">{{ $action->assignee?->name }} · Due {{ $action->due_date->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 text-xs rounded-full {{ $action->status == 'completed' ? 'bg-green-100 text-green-700' : ($action->status == 'overdue' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($action->status) }}</span>
                @if($action->status !== 'completed')
                <form method="POST" action="{{ route('action-items.complete', $action) }}">@csrf<button type="submit" class="text-green-600 hover:text-green-800 text-xs"><i class="fas fa-check"></i></button></form>
                @endif
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-3">No action items yet.</p>
        @endforelse
    </div>

    {{-- Escalation History --}}
    @if($finding->escalations->count())
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-arrow-up text-red-500 mr-2"></i>Escalation History</h3>
        @foreach($finding->escalations as $esc)
        <div class="p-3 bg-red-50 rounded-lg mb-2">
            <p class="text-sm font-medium">Level {{ $esc->escalated_from_level }} → {{ $esc->escalated_to_level }}</p>
            <p class="text-xs text-gray-600">To: {{ $esc->escalatedToUser?->name }} · By: {{ $esc->escalatedByUser?->name }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $esc->reason }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Escalation Modal --}}
<div id="escalateModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Escalate Finding</h3>
        <form method="POST" action="{{ route('findings.escalate', $finding) }}" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-medium mb-1">Escalate To *</label>
                <select name="escalated_to" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    @foreach(\App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Audit Manager', 'Executive', 'Audit Committee']))->get() as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->roles->first()?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Reason *</label><textarea name="reason" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm"></textarea></div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg">Escalate</button>
                <button type="button" onclick="document.getElementById('escalateModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

