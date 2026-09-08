@extends('layouts.app')

@section('title', 'Risk Heat Map')

@section('content')
<style>
    /* Professional Light Theme for Heat Map */
    #risk-heatmap-page {
        color: #1e293b !important;
        font-family: 'Inter', system-ui, sans-serif !important;
    }
    #risk-heatmap-page .bg-card { 
        background-color: #ffffff !important; 
        border: 1px solid #e2e8f0 !important; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; 
    }
    #risk-heatmap-page .text-accent { color: #004ea1 !important; }
    #risk-heatmap-page h2 { color: #004ea1 !important; }
    
    /* Matrix Styling */
    .heatmap-grid {
        display: grid;
        grid-template-columns: auto repeat(5, 1fr);
        gap: 12px;
    }
    .heatmap-cell {
        aspect-ratio: 1 / 0.8;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .heatmap-cell:hover { transform: scale(1.05); z-index: 10; }
    
    /* Risk Levels - Refined for Light Theme */
    .level-extreme { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-bottom: 4px solid #991b1b; }
    .level-high { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-bottom: 4px solid #9a3412; }
    .level-medium { background: linear-gradient(135deg, #facc15 0%, #eab308 100%); border-bottom: 4px solid #a16207; }
    .level-low { background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%); border-bottom: 4px solid #166534; }
</style>

<div id="risk-heatmap-page">
    <div class="flex items-center justify-between mb-12">
        <div>
            <h2 class="text-5xl font-black text-[#004ea1] tracking-tighter mb-2">Risk Heat Map</h2>
            <p class="text-slate-500 text-sm font-medium">Enterprise Visualization <span class="mx-2 text-slate-300">|</span> <span class="text-accent font-bold">Impact vs Likelihood Matrix</span></p>
        </div>
        <div class="bg-card px-8 py-4 rounded-2xl flex items-center gap-6">
            <div class="text-center">
                <div class="text-2xl font-black text-[#004ea1]">{{ $risks->count() }}</div>
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Risks</div>
            </div>
            <div class="w-px h-8 bg-slate-200"></div>
            <div class="text-center">
                <div class="text-2xl font-black text-red-600">{{ $risks->where('residual_risk_score', '>=', 15)->count() }}</div>
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Extreme</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        {{-- Matrix Column --}}
        <div class="lg:col-span-3 bg-card p-10 rounded-3xl shadow-2xl relative">
            {{-- Y-Axis Label --}}
            <div class="absolute left-4 top-1/2 -translate-y-1/2 -rotate-90 origin-center">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] whitespace-nowrap">Impact (Severity)</span>
            </div>

            <div class="ml-12">
                <div class="heatmap-grid">
                    {{-- Top Left Empty --}}
                    <div></div>
                    {{-- Column Headers --}}
                    @for($l = 1; $l <= 5; $l++)
                        <div class="text-center mb-4">
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">L{{ $l }}</div>
                            <div class="text-[9px] text-slate-600 font-bold uppercase truncate">
                                @if($l==1) Rare @elseif($l==2) Unlikely @elseif($l==3) Possible @elseif($l==4) Likely @else A.Certain @endif
                            </div>
                        </div>
                    @endfor

                    {{-- Rows --}}
                    @for($i = 5; $i >= 1; $i--)
                        {{-- Row Header --}}
                        <div class="flex flex-col justify-center text-right pr-4">
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">I{{ $i }}</div>
                            <div class="text-[9px] text-slate-600 font-bold uppercase leading-none mt-1">
                                @if($i==5) Cat @elseif($i==4) Maj @elseif($i==3) Mod @elseif($i==2) Min @else Ins @endif
                            </div>
                        </div>

                        {{-- Cells --}}
                        @for($l = 1; $l <= 5; $l++)
                            @php
                                $score = $i * $l;
                                $cellRisks = $risks->where('residual_impact', $i)->where('residual_likelihood', $l);
                                $class = $score >= 15 ? 'level-extreme' : ($score >= 10 ? 'level-high' : ($score >= 5 ? 'level-medium' : 'level-low'));
                            @endphp
                            <div class="heatmap-cell {{ $class }} cursor-pointer" onclick="filterRisks({{ $i }}, {{ $l }})">
                                @if($cellRisks->count() > 0)
                                    <div class="flex flex-wrap justify-center gap-1 p-2">
                                        @foreach($cellRisks->take(6) as $risk)
                                            <a href="{{ route('risks.show', $risk) }}" title="{{ $risk->risk_code }}" class="w-6 h-6 bg-white/20 hover:bg-white hover:text-slate-900 transition-all rounded-md flex items-center justify-center text-[9px] font-black text-white border border-white/30" onclick="event.stopPropagation()">
                                                {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    </div>
                                    <span class="absolute bottom-2 right-2 text-[9px] font-black text-white/40 uppercase tracking-widest">{{ $cellRisks->count() }}</span>
                                @else
                                    <div class="w-1 h-1 rounded-full bg-white/10"></div>
                                @endif
                            </div>
                        @endfor
                    @endfor
                </div>

                {{-- X-Axis Label --}}
                <div class="text-center mt-10">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Likelihood (Probability)</span>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8">
            {{-- Legend --}}
            <div class="bg-card p-8 rounded-3xl">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Risk Profile</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-red-500/10 border border-red-500/20">
                        <span class="text-xs font-black text-red-500 uppercase tracking-tight">Extreme</span>
                        <span class="text-[10px] text-red-500/60 font-bold">15 - 25</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-orange-500/10 border border-orange-500/20">
                        <span class="text-xs font-black text-orange-500 uppercase tracking-tight">High Risk</span>
                        <span class="text-[10px] text-orange-500/60 font-bold">10 - 14</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-yellow-500/10 border border-yellow-500/20">
                        <span class="text-xs font-black text-yellow-500 uppercase tracking-tight">Medium</span>
                        <span class="text-[10px] text-yellow-500/60 font-bold">5 - 9</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-green-500/10 border border-green-500/20">
                        <span class="text-xs font-black text-green-500 uppercase tracking-tight">Low Risk</span>
                        <span class="text-[10px] text-green-500/60 font-bold">1 - 4</span>
                    </div>
                </div>
            </div>

            {{-- Priority List --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-[10px] font-black text-[#004ea1] uppercase tracking-[0.2em]" id="priority-focus-title">Priority Focus</h3>
                    <button onclick="resetFilter()" class="text-[9px] font-bold text-slate-400 hover:text-[#004ea1] uppercase hidden" id="reset-filter-btn">Reset</button>
                </div>
                <div class="space-y-6" id="risk-list">
                    @forelse($risks->sortByDesc('residual_risk_score')->take(5) as $risk)
                        <div class="relative group risk-item" data-impact="{{ $risk->residual_impact }}" data-likelihood="{{ $risk->residual_likelihood }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full mt-2" style="background-color: {{ $risk->risk_color }}"></div>
                                    <div>
                                        <a href="{{ route('risks.show', $risk) }}" class="text-[11px] font-black text-slate-700 hover:text-[#004ea1] transition-colors uppercase leading-snug">
                                            {{ $risk->risk_code }}
                                        </a>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Score: {{ $risk->residual_risk_score }}</div>
                                    </div>
                                </div>
                                <a href="#" onclick="alert('Response recording interface would open here.'); event.preventDefault();" class="px-2 py-1 bg-slate-100 text-[#004ea1] text-[9px] font-bold rounded hover:bg-slate-200 transition-all whitespace-nowrap">
                                    Record Response
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-4 text-xs italic">No risks analyzed.</div>
                    @endforelse
                </div>
                <a href="{{ route('risks.index') }}" class="mt-8 block w-full py-3 text-center bg-[#004ea1] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#003a7a] transition-all shadow-sm">
                    Full Register
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function filterRisks(impact, likelihood) {
        document.getElementById('priority-focus-title').innerText = `Filtered Risks (I${impact} x L${likelihood})`;
        document.getElementById('reset-filter-btn').classList.remove('hidden');
        
        const items = document.querySelectorAll('.risk-item');
        let hasVisible = false;
        
        items.forEach(item => {
            if (item.getAttribute('data-impact') == impact && item.getAttribute('data-likelihood') == likelihood) {
                item.style.display = 'block';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        if (!hasVisible && items.length > 0) {
            // Optional: show a "no risks" message dynamically if needed
        }
    }

    function resetFilter() {
        document.getElementById('priority-focus-title').innerText = 'Priority Focus';
        document.getElementById('reset-filter-btn').classList.add('hidden');
        
        const items = document.querySelectorAll('.risk-item');
        items.forEach(item => {
            item.style.display = 'block';
        });
    }
</script>
@endsection
