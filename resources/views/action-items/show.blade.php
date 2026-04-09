@extends('layouts.app')
@section('title', 'Action Item Details')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $actionItem->status == 'completed' ? 'bg-green-100 text-green-700' : ($actionItem->status == 'overdue' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($actionItem->status) }}</span>
                <h2 class="text-lg font-bold text-[#333] mt-2">Action Item</h2>
            </div>
            @if($actionItem->status !== 'completed')
            <form method="POST" action="{{ route('action-items.complete', $actionItem) }}">@csrf<button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg"><i class="fas fa-check mr-1"></i>Complete</button></form>
            @endif
        </div>
        <p class="text-sm text-gray-700 mb-4">{{ $actionItem->action_description }}</p>
        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
            <div><p class="text-xs text-gray-500">Finding</p><a href="{{ route('findings.show', $actionItem->finding) }}" class="text-sm font-medium text-[#004ea1] hover:underline">{{ $actionItem->finding?->title }}</a></div>
            <div><p class="text-xs text-gray-500">Audit</p><p class="text-sm font-medium">{{ $actionItem->finding?->audit?->audit_code }}</p></div>
            <div><p class="text-xs text-gray-500">Assigned To</p><p class="text-sm font-medium">{{ $actionItem->assignee?->name }}</p></div>
            <div><p class="text-xs text-gray-500">Due Date</p><p class="text-sm font-medium {{ $actionItem->isOverdue() ? 'text-red-600' : '' }}">{{ $actionItem->due_date->format('d M Y') }}</p></div>
            @if($actionItem->completed_at)<div><p class="text-xs text-gray-500">Completed At</p><p class="text-sm font-medium text-green-600">{{ $actionItem->completed_at->format('d M Y H:i') }}</p></div>@endif
            @if($actionItem->comments)<div class="col-span-2"><p class="text-xs text-gray-500">Comments</p><p class="text-sm text-gray-700">{{ $actionItem->comments }}</p></div>@endif
        </div>
    </div>
</div>
@endsection

