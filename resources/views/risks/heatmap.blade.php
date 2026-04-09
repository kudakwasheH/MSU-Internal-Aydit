@extends('layouts.app')

@section('title', 'Network Risk Heat Map')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center text-[#004ea1]">
                <i class="fas fa-layer-group text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Enterprise Risk Heat Map</h2>
                <p class="text-sm text-slate-500">Visualizing Residual Risk exposure across 25 segments (Impact vs. Likelihood)</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Active Risks:</span>
            <span class="px-3 py-1 bg-[#004ea1] text-white text-sm font-bold rounded-full">{{ $risks->count() }}</span>
        </div>
    </div>

    {{-- Main Grid Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- The Matrix --}}
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm p-8 border border-slate-200 overflow-x-auto">
            <div class="relative min-w-[600px] pb-4">
                {{-- Y-Axis Label --}}
                <div class="absolute -left-16 top-1/2 -translate-y-1/2 -rotate-90 origin-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] whitespace-nowrap">Impact (Severity)</span>
                </div>

                {{-- Table Container --}}
                <div class="ml-12 mr-4">
                    <table class="w-full table-fixed border-separate border-spacing-2">
                        <thead>
                            <tr>
                                <th class="w-20"></th>
                                @for($l = 1; $l <= 5; $l++)
                                    <th class="pb-4">
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">L{{ $l }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter opacity-60">
                                            @if($l==1) Rare @elseif($l==2) Unlikely @elseif($l==3) Possible @elseif($l==4) Likely @else A.Certain @endif
                                        </div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 5; $i >= 1; $i--)
                                <tr>
                                    {{-- Row headers --}}
                                    <td class="pr-6 text-right align-middle">
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">I{{ $i }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter opacity-60 leading-tight">
                                            @if($i==5) Catastrophic @elseif($i==4) Major @elseif($i==3) Moderate @elseif($i==2) Minor @else Insignificant @endif
                                        </div>
                                    </td>
                                    
                                    @for($l = 1; $l <= 5; $l++)
                                        @php
                                            $score = $i * $l;
                                            $cellRisks = $risks->where('residual_impact', $i)->where('residual_likelihood', $l);
                                            
                                            if ($score >= 15) {
                                                $bg = 'bg-red-500'; $border = 'border-red-600';
                                            } elseif ($score >= 10) {
                                                $bg = 'bg-orange-500'; $border = 'border-orange-600';
                                            } elseif ($score >= 5) {
                                                $bg = 'bg-[#ffcc00]'; $border = 'border-[#e6b800]';
                                            } else {
                                                $bg = 'bg-green-500'; $border = 'border-green-600';
                                            }
                                        @endphp
                                        
                                        <td class="relative">
                                            <div class="{{ $bg }} {{ $border }} border-b-4 rounded-xl p-3 h-24 shadow-sm transition-all duration-300 hover:scale-[1.05] hover:shadow-lg cursor-default flex flex-col items-center justify-center gap-1.5 overflow-hidden">
                                                @if($cellRisks->count() > 0)
                                                    <div class="flex flex-wrap justify-center gap-1.5 max-w-full">
                                                        @foreach($cellRisks->take(4) as $risk)
                                                            <a href="{{ route('risks.show', $risk) }}" 
                                                                class="w-7 h-7 bg-white/20 backdrop-blur-lg rounded-lg flex items-center justify-center text-[10px] font-black text-white hover:bg-white hover:text-[#004ea1] transition-all border border-white/40 shadow-sm"
                                                                title="{{ $risk->risk_code }}: {{ $risk->title }}">
                                                                {{ $loop->iteration }}
                                                            </a>
                                                        @endforeach
                                                        @if($cellRisks->count() > 4)
                                                            <div class="w-7 h-7 bg-white/30 backdrop-blur-lg rounded-lg flex items-center justify-center text-[10px] font-black text-white border border-white/40">
                                                                +{{ $cellRisks->count() - 4 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span class="text-[9px] font-black text-white/40 uppercase tracking-widest">{{ $cellRisks->count() }} Risks</span>
                                                @else
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white/10"></div>
                                                @endif
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- X-Axis Label --}}
                <div class="text-center mt-8 ml-12">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Likelihood (Probability)</span>
                </div>
            </div>
        </div>

        {{-- Legend and Critical Risks --}}
        <div class="space-y-6">
            {{-- Heat Legend --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
                <h3 class="text-sm font-bold text-slate-800 mb-4 px-2 border-l-4 border-[#004ea1]">Rating Legend</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-bold text-red-700">Extreme</span>
            {{-- Heat Legend --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 px-2 border-l-4 border-[#004ea1]">Risk Response Legend</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-red-50/50 rounded-xl border border-red-100/50 group hover:bg-red-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-red-500 rounded-full shadow-[0_0_8px_rgba(239,68,68,0.4)] animate-pulse"></div>
                            <span class="text-[11px] font-black text-red-700 uppercase tracking-tight">Extreme</span>
                        </div>
                        <span class="text-[10px] text-red-400 font-black font-mono">15-25</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-orange-50/50 rounded-xl border border-orange-100/50 group hover:bg-orange-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-orange-500 rounded-full shadow-[0_0_8px_rgba(249,115,22,0.4)]"></div>
                            <span class="text-[11px] font-black text-orange-700 uppercase tracking-tight">High Risk</span>
                        </div>
                        <span class="text-[10px] text-orange-400 font-black font-mono">10-14</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50/50 rounded-xl border border-[#ffcc00]/20 group hover:bg-[#ffcc00]/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-[#ffcc00] rounded-full shadow-[0_0_8px_rgba(255,204,0,0.4)]"></div>
                            <span class="text-[11px] font-black text-[#8a6d00] uppercase tracking-tight">Medium</span>
                        </div>
                        <span class="text-[10px] text-[#ffcc00] font-black font-mono">5-9</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50/50 rounded-xl border border-green-100/50 group hover:bg-green-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                            <span class="text-[11px] font-black text-green-700 uppercase tracking-tight">Low Risk</span>
                        </div>
                        <span class="text-[10px] text-green-500 font-black font-mono">1-4</span>
                    </div>
                </div>
            </div>

            {{-- Top 5 Critical Risks List --}}
            <div class="bg-slate-900 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                <h3 class="text-[10px] font-black text-[#ffcc00] uppercase tracking-[0.2em] mb-6 px-2 border-l-4 border-[#ffcc00]">Governance Priority</h3>
                <div class="space-y-4">
                    @forelse($risks->sortByDesc('residual_risk_score')->take(5) as $risk)
                        <a href="{{ route('risks.show', $risk) }}" class="block group">
                            <div class="flex items-start gap-3">
                                <div class="mt-1.5 w-2 h-2 rounded-full shrink-0 shadow-[0_0_8px_rgba(255,255,255,0.3)]"
                                     style="background-color: {{ $risk->risk_color }}"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-black group-hover:text-[#ffcc00] transition-colors leading-snug uppercase tracking-tight">
                                        {{ $risk->risk_code }}: {{ $risk->title }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] text-white/40 font-bold uppercase tracking-widest">{{ $risk->category }}</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-white/10 text-white/60">Score: {{ $risk->residual_risk_score }}</span>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover:text-[#ffcc00] group-hover:translate-x-1 transition-all"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 bg-white/5 rounded-xl border border-white/10">
                            <p class="text-[10px] text-white/30 italic uppercase font-bold tracking-widest leading-relaxed">System Monitoring:<br>No Extreme Risks Found</p>
                        </div>
                    @endforelse
                </div>
                
                <a href="{{ route('risks.index') }}" class="mt-8 flex items-center justify-center gap-2 py-2.5 text-[10px] font-black text-slate-900 bg-[#ffcc00] hover:bg-white rounded-xl transition-all shadow-lg active:scale-95 uppercase tracking-widest">
                    View Enterprise Register
                    <i class="fas fa-chevron-right text-[8px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
