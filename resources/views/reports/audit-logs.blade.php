@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
<div class="mb-4">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-[0.2em]">Reports & Analytics</h2>
    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Immutable System Audit Trail & Event Logging</p>
</div>

@include('reports._tabs')
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <select name="module" class="px-3 py-2 border rounded-lg text-sm"><option value="">All Modules</option>@foreach(['audit','risk','finding','action'] as $m)<option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>@endforeach</select>
        <select name="action" class="px-3 py-2 border rounded-lg text-sm"><option value="">All Actions</option>@foreach(['create','update','delete','approve','escalate'] as $a)<option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>@endforeach</select>
        <button type="submit" class="px-4 py-2 bg-[#333] text-white text-sm rounded-lg"><i class="fas fa-filter mr-1"></i>Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record ID</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
        </tr></thead>
        <tbody class="divide-y">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                <td class="px-5 py-3 text-sm">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $log->action == 'create' ? 'bg-green-100 text-green-700' : ($log->action == 'delete' ? 'bg-red-100 text-red-700' : ($log->action == 'approve' ? 'bg-blue-100 text-blue-700' : ($log->action == 'escalate' ? 'bg-purple-100 text-purple-700' : 'bg-yellow-100 text-yellow-700'))) }}">{{ ucfirst($log->action) }}</span></td>
                <td class="px-5 py-3 text-sm">{{ ucfirst($log->module) }}</td>
                <td class="px-5 py-3 font-mono text-xs">#{{ $log->record_id }}</td>
                <td class="px-5 py-3 font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No audit logs yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 border-t">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
