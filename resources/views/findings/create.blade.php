@extends('layouts.app')
@section('title', 'Create Finding')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6"><i class="fas fa-plus-circle text-[#004ea1] mr-2"></i>Record New Finding</h2>
        <form method="POST" action="{{ route('findings.store') }}" class="space-y-5">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Audit *</label>
                <select name="audit_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">Select Audit</option>
                    @foreach($audits as $a)<option value="{{ $a->id }}" {{ old('audit_id', request('audit_id')) == $a->id ? 'selected' : '' }}>{{ $a->audit_code }} — {{ $a->title }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title *</label><input type="text" name="title" value="{{ old('title') }}" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label><textarea name="description" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('description') }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Root Cause * <span class="text-xs text-gray-400">(required for report finalization)</span></label><textarea name="root_cause" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('root_cause') }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Impact *</label><textarea name="impact" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('impact') }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Recommendation *</label><textarea name="recommendation" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('recommendation') }}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Severity *</label><select name="severity" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['critical','high','medium','low'] as $s)<option value="{{ $s }}" {{ old('severity') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 border rounded-lg text-sm"><option value="">Select</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
            </div>
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-save mr-2"></i>Record Finding</button>
                <a href="{{ route('findings.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

