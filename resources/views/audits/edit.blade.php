@extends('layouts.app')
@section('title', 'Edit Audit')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6"><i class="fas fa-edit text-[#004ea1] mr-2"></i>Edit: {{ $audit->audit_code }}</h2>
        <form method="POST" action="{{ route('audits.update', $audit) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" value="{{ old('title', $audit->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('description', $audit->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type *</label>
                    <select name="audit_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        @foreach(['internal','external','it','compliance','performance'] as $t)
                        <option value="{{ $t }}" {{ old('audit_type', $audit->audit_type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        @foreach(['high','medium','low'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $audit->priority) == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        @foreach(['draft','planned','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $audit->status) == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget Code (ERP Link)</label>
                    <select name="budget_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        <option value="">Select Budget Code</option>
                        @foreach($budgetCodes as $bc)
                        <option value="{{ $bc['code'] }}" {{ old('budget_code', $audit->budget_code) == $bc['code'] ? 'selected' : '' }}>{{ $bc['code'] }} - {{ $bc['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Compliance Reference</label>
                    <select name="compliance_ref" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        <option value="">Select Reference</option>
                        @foreach($complianceRefs as $cr)
                        <option value="{{ $cr['ref'] }}" {{ old('compliance_ref', $audit->compliance_ref) == $cr['ref'] ? 'selected' : '' }}>{{ $cr['ref'] }} - {{ $cr['title'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planned Start *</label>
                    <input type="date" name="planned_start_date" value="{{ old('planned_start_date', $audit->planned_start_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planned End *</label>
                    <input type="date" name="planned_end_date" value="{{ old('planned_end_date', $audit->planned_end_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Team Members <span class="text-xs text-gray-400">(Optional)</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                    @foreach($users as $user)
                    <label class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="team_members[]" value="{{ $user->id }}" class="mt-0.5 rounded text-[#004ea1] focus:ring-[#004ea1]"
                            {{ in_array($user->id, old('team_members', $audit->teamMembers->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span>{{ $user->name }} — {{ $user->position }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Linked Risks *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                    @foreach($risks as $risk)
                    <label class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="risk_ids[]" value="{{ $risk->id }}" class="mt-0.5 rounded text-[#004ea1] focus:ring-[#004ea1]"
                            {{ in_array($risk->id, old('risk_ids', $audit->risks->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span>{{ $risk->risk_code }} — {{ $risk->title }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition"><i class="fas fa-save mr-2"></i>Update</button>
                <a href="{{ route('audits.show', $audit) }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

