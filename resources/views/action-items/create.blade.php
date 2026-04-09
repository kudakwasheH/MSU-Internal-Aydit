@extends('layouts.app')
@section('title', 'Create Action Item')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6"><i class="fas fa-plus-circle text-[#004ea1] mr-2"></i>Create Action Item</h2>
        <form method="POST" action="{{ route('action-items.store') }}" class="space-y-5">
            @csrf
            <div><label class="block text-sm font-medium mb-1">Finding *</label>
                <select name="finding_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    @foreach($findings as $f)<option value="{{ $f->id }}" {{ old('finding_id', request('finding_id')) == $f->id ? 'selected' : '' }}>{{ $f->title }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Action Description *</label><textarea name="action_description" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('action_description') }}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Assigned To *</label><select name="assigned_to" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">Due Date *</label><input type="date" name="due_date" value="{{ old('due_date') }}" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
            </div>
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-save mr-2"></i>Create</button>
                <a href="{{ route('action-items.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

