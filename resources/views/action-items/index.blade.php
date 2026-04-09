@extends('layouts.app')
@section('title', 'Action Items')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h2 class="text-xl font-bold text-[#333]">Action Items</h2><p class="text-sm text-gray-500">Track corrective actions and follow-ups</p></div>
    <a href="{{ route('action-items.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-plus mr-2"></i>New Action</a>
</div>
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full px-3 py-2 border rounded-lg text-sm"></div>
        <select name="status" class="px-3 py-2 border rounded-lg text-sm"><option value="">All</option>@foreach(['pending','in_progress','completed','overdue'] as $s)<option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select>
        <button type="submit" class="px-4 py-2 bg-[#333] text-white text-sm rounded-lg"><i class="fas fa-filter mr-1"></i>Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Finding</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($actionItems as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3"><a href="{{ route('action-items.show', $item) }}" class="text-sm font-medium text-gray-800 hover:text-[#004ea1]">{{ Str::limit($item->action_description, 40) }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ Str::limit($item->finding?->title, 25) }}</td>
                <td class="px-5 py-3 text-xs">{{ $item->assignee?->name }}</td>
                <td class="px-5 py-3 text-xs {{ $item->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">{{ $item->due_date->format('d M Y') }}</td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $item->status == 'completed' ? 'bg-green-100 text-green-700' : ($item->status == 'overdue' ? 'bg-red-100 text-red-700' : ($item->status == 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">{{ ucfirst($item->status) }}</span></td>
                <td class="px-5 py-3 flex items-center gap-2">
                    <a href="{{ route('action-items.show', $item) }}" class="text-blue-600"><i class="fas fa-eye"></i></a>
                    @if($item->status !== 'completed')
                    <form method="POST" action="{{ route('action-items.complete', $item) }}">@csrf<button type="submit" class="text-green-600" title="Complete"><i class="fas fa-check-circle"></i></button></form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No action items found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 border-t">{{ $actionItems->withQueryString()->links() }}</div>
</div>
@endsection

