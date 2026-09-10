@extends('layouts.app')

@section('title', 'Risk Register')

@section('content')
<style>
    /* Professional Light Theme for Risk Register */
    #risk-register-page {
        color: #1e293b !important;
        font-family: 'Inter', system-ui, sans-serif !important;
    }
    #risk-register-page h2 { color: #004ea1 !important; }
    #risk-register-page .text-accent { color: #004ea1 !important; }
    #risk-register-page .bg-card { background-color: #ffffff !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; }
    #risk-register-page .table-head { background-color: #f8fafc !important; border-bottom: 2px solid #e2e8f0 !important; }
    #risk-register-page .row-hover:hover { background-color: #f1f5f9 !important; }
    #risk-register-page select, #risk-register-page input { 
        background-color: #ffffff !important; 
        color: #1e293b !important; 
        border: 1px solid #cbd5e1 !important;
    }
    #risk-register-page .btn-primary {
        background: #004ea1 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #risk-register-page .tab-active { border-bottom: 3px solid #ffcc00 !important; color: #004ea1 !important; }
    #risk-register-page .risk-code { color: #004ea1 !important; font-weight: 700 !important; }
</style>

<div id="risk-register-page" x-data="{ activeTab: 'identification' }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-5xl font-black tracking-tighter mb-2">Risk Register</h2>
            <div class="flex items-center gap-3 text-sm font-medium">
                <span class="text-slate-500">Institutional Dashboard</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <form action="{{ route('risks.index') }}" method="GET" class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search risk ID or description..." class="rounded-xl py-3.5 pl-12 pr-6 w-96 text-sm outline-none focus:ring-2 focus:ring-[#004ea1]/30 transition-all shadow-sm">
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <form action="{{ route('risks.index') }}" method="GET" class="bg-card p-8 rounded-2xl mb-10 flex items-end gap-10">
        <div class="flex items-center gap-3 text-slate-500 mb-1">
            <i class="fas fa-filter text-xs"></i>
            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Filter Universe</span>
        </div>
        <div class="flex flex-1 gap-8">
            <div class="flex flex-col gap-2.5 flex-1">
                <label class="text-[10px] text-slate-500 font-black uppercase tracking-widest ml-1">Category</label>
                <select name="category" onchange="this.form.submit()" class="rounded-lg px-5 py-3 w-full text-xs outline-none hover:border-slate-500 transition-colors cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach(['operational','financial','compliance','strategic','it'] as $c)
                    <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-2.5 flex-1">
                <label class="text-[10px] text-slate-500 font-black uppercase tracking-widest ml-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="rounded-lg px-5 py-3 w-full text-xs outline-none hover:border-slate-500 transition-colors cursor-pointer">
                    <option value="">All Statuses</option>
                    @foreach(['active','mitigated','obsolete'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="pb-2 flex flex-col items-end gap-2">
            <span class="text-[9px] font-bold text-slate-600 uppercase tracking-widest">Last Updated: {{ now()->format('H:i:s') }}</span>
            <a href="{{ route('risks.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-accent transition-colors flex items-center gap-2">
                <i class="fas fa-times"></i> Clear Filters
            </a>
        </div>
    </form>

    {{-- Tabs --}}
    <div class="flex gap-12 border-b border-slate-200 mb-10 overflow-x-auto no-scrollbar">
        <button class="pb-5 tab-active font-black text-sm whitespace-nowrap tracking-tight transition-all">
            Risk Identification
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-card rounded-3xl overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead class="table-head">
                <tr>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-[#004ea1]">Risk ID</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Review Date</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Status</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Category</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Description</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">KRA at Risk</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Root Cause</th>
                    <th class="px-10 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Consequence</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($risks as $risk)
                <tr class="row-hover transition-colors group">
                    <td class="px-10 py-8">
                        <a href="{{ route('risks.show', $risk) }}" class="risk-code text-lg tracking-tighter hover:underline">
                            {{ $risk->risk_code }}
                        </a>
                    </td>
                    <td class="px-10 py-8 text-xs font-bold text-slate-500">{{ $risk->last_reviewed_at ?? date('Y-m-d') }}</td>
                    <td class="px-10 py-8">
                        @php
                            $statusColor = match($risk->status ?? 'active') {
                                'active' => 'bg-green-100 text-green-700 border-green-200',
                                'mitigated' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'obsolete' => 'bg-red-100 text-red-700 border-red-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        @endphp
                        <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border {{ $statusColor }}">
                            {{ $risk->status ?? 'active' }}
                        </span>
                    </td>
                    <td class="px-10 py-8">
                        <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            {{ $risk->category }}
                        </span>
                    </td>
                    <td class="px-10 py-8 text-sm text-slate-700 leading-relaxed max-w-lg font-medium">{{ $risk->description }}</td>
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-2 text-sm font-bold text-[#004ea1]">
                            <i class="fas fa-shield-halved text-[10px]"></i>
                            {{ $risk->kra_at_risk }}
                        </div>
                    </td>
                    <td class="px-10 py-8 text-sm text-slate-500 italic">"{{ $risk->cause }}"</td>
                    <td class="px-10 py-8 text-xs text-slate-400 leading-normal max-w-xs">
                        {{ $risk->consequence }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-12 flex justify-center">
        {{ $risks->links('vendor.pagination.tailwind-dark') }}
    </div>
</div>
@endsection

