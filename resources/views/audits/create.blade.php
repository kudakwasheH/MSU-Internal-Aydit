@extends('layouts.app')
@section('title', 'Create Audit')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6"><i class="fas fa-plus-circle text-[#004ea1] mr-2"></i>Create New Audit Engagement</h2>
        <form method="POST" action="{{ route('audits.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type *</label>
                    <select name="audit_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        <option value="">Select Type</option>
                        @foreach(['internal','external','it','compliance','performance'] as $t)
                        <option value="{{ $t }}" {{ old('audit_type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                        @foreach(['high','medium','low'] as $p)
                        <option value="{{ $p }}" {{ old('priority', 'medium') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planned Start Date *</label>
                    <input type="date" name="planned_start_date" value="{{ old('planned_start_date') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planned End Date *</label>
                    <input type="date" name="planned_end_date" value="{{ old('planned_end_date') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Linked Risks * <span class="text-xs text-gray-400">(select at least one)</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                    @foreach($risks as $risk)
                    <label class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="risk_ids[]" value="{{ $risk->id }}" class="mt-0.5 rounded text-[#004ea1] focus:ring-[#004ea1]" {{ in_array($risk->id, old('risk_ids', [])) ? 'checked' : '' }}>
                        <span>
                            <span class="font-medium">{{ $risk->risk_code }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $risk->title }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('risk_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition">
                    <i class="fas fa-save mr-2"></i>Create Audit
                </button>
                <a href="{{ route('audits.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

