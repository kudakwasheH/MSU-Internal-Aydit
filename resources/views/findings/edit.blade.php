@extends('layouts.app')
@section('title', 'Edit Finding')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6">Edit Finding</h2>
        <form method="POST" action="{{ route('findings.update', $finding) }}" class="space-y-5">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium mb-1">Title *</label><input type="text" name="title" value="{{ old('title', $finding->title) }}" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">Description *</label><textarea name="description" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('description', $finding->description) }}</textarea></div>
            <div><label class="block text-sm font-medium mb-1">Root Cause *</label><textarea name="root_cause" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('root_cause', $finding->root_cause) }}</textarea></div>
            <div><label class="block text-sm font-medium mb-1">Impact *</label><textarea name="impact" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('impact', $finding->impact) }}</textarea></div>
            <div><label class="block text-sm font-medium mb-1">Recommendation *</label><textarea name="recommendation" rows="2" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('recommendation', $finding->recommendation) }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium mb-1">Severity *</label><select name="severity" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['critical','high','medium','low'] as $s)<option value="{{ $s }}" {{ old('severity', $finding->severity) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['open','in_progress','resolved','closed'] as $s)<option value="{{ $s }}" {{ old('status', $finding->status) == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 border rounded-lg text-sm"><option value="">Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to', $finding->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
            </div>
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-save mr-2"></i>Update</button>
                <a href="{{ route('findings.show', $finding) }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

