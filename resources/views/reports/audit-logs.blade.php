@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
<style>
    #audit-trail-page {
        background-color: #050a15 !important;
        color: #e2e8f0 !important;
        margin: -1.5rem !important;
        padding: 2.5rem !important;
        min-height: 100vh !important;
        font-family: 'Inter', system-ui, sans-serif !important;
    }
    #audit-trail-page .bg-card { background-color: #10192d !important; border: 1px solid #1e293b !important; }
    #audit-trail-page .text-accent { color: #00f2fe !important; }
    #audit-trail-page .table-head { background-color: #0f172a !important; border-bottom: 2px solid #1e293b !important; }
    #audit-trail-page .row-hover:hover { background-color: rgba(255, 255, 255, 0.03) !important; }
    #audit-trail-page select, #audit-trail-page input { 
        background-color: #1a2236 !important; 
        color: #ffffff !important; 
        border: 1px solid #334155 !important;
    }
</style>

<div id="audit-trail-page">
    <div class="mb-10">
        <h2 class="text-5xl font-black text-white tracking-tighter mb-2">Audit Trail</h2>
        <p class="text-slate-500 text-sm font-medium">Immutable System Logging <span class="mx-2 text-slate-700">|</span> <span class="text-accent">Event Monitoring</span></p>
    </div>

    @include('reports._tabs')

    {{-- Filter Bar --}}
    <div class="bg-card p-6 rounded-2xl mb-10 flex items-center gap-8">
        <div class="flex items-center gap-3 text-slate-500">
            <i class="fas fa-filter text-xs"></i>
            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Quick Filter</span>
        </div>
        <form method="GET" class="flex flex-1 gap-6">
            <div class="flex-1">
                <select name="module" class="w-full rounded-lg px-4 py-2.5 text-xs outline-none cursor-pointer">
                    <option value="">All Modules</option>
                    @foreach(['audit','risk','finding','action'] as $m)
                        <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <select name="action" class="w-full rounded-lg px-4 py-2.5 text-xs outline-none cursor-pointer">
                    <option value="">All Actions</option>
                    @foreach(['create','update','delete','approve','escalate'] as $a)
                        <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                Filter Logs
            </button>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-card rounded-3xl overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead class="table-head">
                <tr>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-[#4facfe]">Timestamp</th>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">User</th>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Action</th>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Module</th>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Record ID</th>
                    <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($logs as $log)
                <tr class="row-hover transition-colors">
                    <td class="px-8 py-6 text-xs font-bold text-slate-400">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-300">
                                {{ substr($log->user?->name ?? 'S', 0, 1) }}
                            </div>
                            <span class="text-sm font-bold text-white">{{ $log->user?->name ?? 'System' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg {{ $log->action == 'create' ? 'bg-green-500/10 text-green-500 border border-green-500/20' : ($log->action == 'delete' ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-blue-500/10 text-blue-500 border border-blue-500/20') }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm font-medium text-slate-300">{{ ucfirst($log->module) }}</td>
                    <td class="px-8 py-6 text-xs font-black font-mono text-slate-500">#{{ $log->record_id }}</td>
                    <td class="px-8 py-6 text-[10px] font-bold font-mono text-slate-600">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center text-slate-600 italic">No activity recorded in the audit trail.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-10">
        {{ $logs->withQueryString()->links('vendor.pagination.tailwind-dark') }}
    </div>
</div>
@endsection
