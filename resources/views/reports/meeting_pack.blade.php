@extends('layouts.app')

@section('title', 'Audit Committee Meeting Pack')

@section('content')
<div class="mb-4">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-[0.2em]">Reports & Analytics</h2>
    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Strategic Audit Committee Governance Documents</p>
</div>

@include('reports._tabs')

<div class="space-y-6">
    {{-- MSU Header --}}
    <div class="bg-[#004ea1] rounded-2xl shadow-lg p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-university text-[120px] text-white"></i>
        </div>
        <div class="relative z-10">
            <h1 class="text-3xl font-black text-white leading-tight">Audit Committee Meeting Pack</h1>
            <p class="text-blue-100 mt-2 font-medium">Strategic Snapshot & Governance Oversight Dashboard &middot; {{ now()->format('F Y') }}</p>
        </div>
        
        <div class="mt-8 flex flex-wrap gap-4 relative z-10">
            <a href="#" onclick="window.print()" class="px-6 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-xl text-white text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> Export to PDF
            </a>
            <div class="px-6 py-2 bg-[#ffcc00] rounded-xl text-[#004ea1] text-sm font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-shield-alt"></i> Internal Use Only
            </div>
        </div>
    </div>

    {{-- KRI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-b-4 border-[#004ea1]">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Audit Plan Coverage</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-800">{{ $riskCoverage }}%</h3>
                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-lg">+4% vs Prev</span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-[#004ea1] h-full rounded-full" style="width: {{ $riskCoverage }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 italic">Of active risk register items</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-b-4 border-red-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Critical Deficiencies</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-800">{{ $highRiskFindings }}</h3>
                <span class="text-[10px] font-bold text-red-600 animate-pulse bg-red-50 px-2 py-0.5 rounded-lg">High Priority</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-6 leading-tight">Unresolved findings with 'High' or 'Critical' impact ratings</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-b-4 border-orange-400">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Aging Findings (>90d)</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-800">{{ $agingFindings }}</h3>
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg">Target: 0</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-6 leading-tight">Corrective actions pending for more than 3 months</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-b-4 border-green-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Plan Execution</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-800">{{ round(($completedAudits / max(1, $totalAudits)) * 100) }}%</h3>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg">{{ $completedAudits }}/{{ $totalAudits }} Audits</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-6 leading-tight">Completed engagements against the annual audit plan</p>
        </div>
    </div>

    {{-- Main Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Section 1: Audit Activity --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Plan Delivery Snapshot</h3>
                <a href="{{ route('reports.rolling-plan') }}" class="text-[10px] font-bold text-[#004ea1] hover:underline">Full Rolling Plan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($auditsByType as $type => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-600 capitalize">{{ $type }} Audits</span>
                            <span class="text-xs font-black text-slate-800">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#004ea1]/80 h-full rounded-full" style="width: {{ ($count / array_sum($auditsByType->toArray())) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Section 2: Escalations --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-red-50/30">
                <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> Escalated Findings
                </h3>
            </div>
            <div class="p-6">
                @forelse($recentEscalations as $finding)
                <div class="mb-4 pb-4 border-b border-slate-50 last:border-0 last:mb-0 last:pb-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 rounded">Tier {{ $finding->escalation_level }}</span>
                        <span class="text-[9px] text-slate-400 font-mono">{{ $finding->created_at->format('M d') }}</span>
                    </div>
                    <p class="text-sm font-bold text-slate-800 leading-tight">{{ $finding->title }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Audit: {{ $finding->audit->audit_code }} &middot; Owner: {{ $finding->assignee->name ?? 'N/A' }}</p>
                </div>
                @empty
                <div class="py-12 text-center text-slate-400 italic text-xs">
                    No tier-rated escalations active in this period.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
