@extends('layouts.app')
@section('title', 'Edit Action Item')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6">Edit Action Item</h2>
        <form method="POST" action="{{ route('action-items.update', $actionItem) }}" class="space-y-5">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium mb-1">Action Description *</label><textarea name="action_description" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('action_description', $actionItem->action_description) }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium mb-1">Assigned To *</label><select name="assigned_to" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to', $actionItem->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">Due Date *</label><input type="date" name="due_date" value="{{ old('due_date', $actionItem->due_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['pending','in_progress','completed','overdue'] as $s)<option value="{{ $s }}" {{ old('status', $actionItem->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Comments</label><textarea name="comments" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('comments', $actionItem->comments) }}</textarea></div>
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-save mr-2"></i>Update</button>
                <a href="{{ route('action-items.show', $actionItem) }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

