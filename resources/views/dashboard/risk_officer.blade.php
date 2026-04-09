@extends('layouts.app')
@section('title', 'Risk Officer Dashboard')
@section('content')

{{-- Risk Overview Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Critical Risks</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $topRisks->where('residual_risk_score', '>=', 20)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Risks</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $activeRisks }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-shield-virus text-orange-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">KRI Status (Red)</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $kriStats['red'] }}</p>
            </div>
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-gauge-high text-[#004ea1] text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Treatment Progress</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ round($treatmentProgress) }}%</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-tasks text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Risk Heat Map --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-semibold text-gray-700">Enterprise Risk Heat Map (MSU)</h3>
            <span class="text-xs text-gray-400">Residual Risk Scoring</span>
        </div>
        
        <div class="relative">
            {{-- Heat Map Grid --}}
            <div class="grid grid-cols-6 gap-2">
                {{-- Y-Axis Label --}}
                <div class="col-span-1 flex flex-col justify-between py-10 text-[10px] uppercase font-bold text-gray-400">
                    <span>Certain (5)</span>
                    <span>Likely (4)</span>
                    <span>Possible (3)</span>
                    <span>Unlikely (2)</span>
                    <span>Rare (1)</span>
                </div>
                
                {{-- Grid Cells --}}
                <div class="col-span-5 grid grid-cols-5 aspect-square border-2 border-gray-100">
                    @for($l=5; $l>=1; $l--)
                        @for($i=1; $i<=5; $i++)
                            @php
                                $score = $l * $i;
                                $color = 'bg-green-50';
                                if($score >= 20) $color = 'bg-red-500';
                                elseif($score >= 15) $color = 'bg-orange-400';
                                elseif($score >= 10) $color = 'bg-yellow-300';
                                elseif($score >= 5) $color = 'bg-green-300';
                            @endphp
                            <div class="{{ $color }} border border-white/20 flex items-center justify-center relative group min-h-[60px]">
                                @if($score == 25) <i class="fas fa-circle text-white/50 text-[6px]"></i> @endif
                                {{-- Placeholder for Risk Dots --}}
                                @if($l == 4 && $i == 5)
                                    <div class="w-3 h-3 bg-white rounded-full shadow-lg ring-2 ring-red-700 animate-pulse cursor-pointer" title="Cybersecurity Risk (R-102)"></div>
                                @endif
                                @if($l == 3 && $i == 4)
                                    <div class="w-3 h-3 bg-white rounded-full shadow-lg ring-2 ring-orange-700 cursor-pointer" title="Student Protests (R-205)"></div>
                                @endif
                            </div>
                        @endfor
                    @endfor
                </div>
                
                {{-- X-Axis Label --}}
                <div class="col-start-2 col-span-5 flex justify-between px-2 text-[10px] uppercase font-bold text-gray-400 mt-2">
                    <span>Insignificant (1)</span>
                    <span>Minor (2)</span>
                    <span>Moderate (3)</span>
                    <span>Major (4)</span>
                    <span>Catastrophic (5)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- KRI Monitoring --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-6">Key Risk Indicators (KRIs)</h3>
        <div class="space-y-6">
            <div class="group">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-xs font-bold text-gray-800">Student Attrition Rate</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Target: < 10%</p>
                    </div>
                    <span class="text-sm font-bold text-red-500">12.5%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: 85%"></div>
                </div>
            </div>
            
            <div class="group">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-xs font-bold text-gray-800">Procurement Violations</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Target: 0</p>
                    </div>
                    <span class="text-sm font-bold text-yellow-500">3 Reported</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>

            <div class="group">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-xs font-bold text-gray-800">Staff Vacancy Rate</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Limit: 15%</p>
                    </div>
                    <span class="text-sm font-bold text-green-600">8%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 40%"></div>
                </div>
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-100">
            <h4 class="text-xs font-bold text-[#004ea1] uppercase mb-2">Risk Officer Alert</h4>
            <p class="text-[11px] text-gray-600 leading-relaxed">
                <i class="fas fa-info-circle mr-1"></i>
                Student attrition has breached the 10% threshold. Faculty of Commerce and Law are contributing significantly.
            </p>
        </div>
    </div>
</div>

{{-- Top Risks and Treatments --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-700">Top 5 Residual Risks</h3>
            <a href="{{ route('risks.index') }}" class="text-xs text-[#004ea1] hover:underline">Full Register &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topRisks as $risk)
            <div class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-bold text-gray-800">{{ $risk->title }}</p>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $risk->residual_risk_score >= 20 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        Score: {{ $risk->residual_risk_score }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-[11px] text-gray-500">
                    <span><i class="fas fa-folder mr-1"></i> {{ ucfirst($risk->category) }}</span>
                    <span><i class="fas fa-user-tie mr-1"></i> {{ $risk->owner?->name ?? 'Unassigned' }}</span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-gray-400">No risks identified.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-6">Upcoming Risk Assessments</h3>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border-l-4 border-[#ffcc00]">
                <div class="w-10 h-10 bg-white rounded flex flex-col items-center justify-center shadow-sm">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Apr</span>
                    <span class="text-sm font-bold text-[#333]">12</span>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-800">Faculty of Commerce Workshop</p>
                    <p class="text-[10px] text-gray-500">Risk Identification & Control Assessment</p>
                </div>
                <button class="text-xs text-[#004ea1] hover:text-[#003a7a]"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border-l-4 border-gray-300">
                <div class="w-10 h-10 bg-white rounded flex flex-col items-center justify-center shadow-sm text-gray-400">
                    <span class="text-[10px] font-bold uppercase">Apr</span>
                    <span class="text-sm font-bold">25</span>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-800">ICT Infrastructure Review</p>
                    <p class="text-[10px] text-gray-500">Cybersecurity Resilience Assessment</p>
                </div>
                <button class="text-xs text-gray-400"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="mt-8">
            <button class="w-full py-3 bg-[#004ea1] text-white rounded-lg font-bold text-sm hover:bg-[#003a7a] transition shadow-md">
                <i class="fas fa-plus-circle mr-2"></i>Schedule New Assessment
            </button>
        </div>
    </div>
</div>

@endsection
