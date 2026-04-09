@extends('layouts.app')

@section('title', 'Rolling Audit Plan')

@section('content')
<div class="mb-4">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-[0.2em]">Reports & Analytics</h2>
    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Annual Rolling Plan & Resource Allocation Timeline</p>
</div>

@include('reports._tabs')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center text-[#004ea1]">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Annual Rolling Audit Plan</h2>
                <p class="text-sm text-slate-500">Scheduled engagements and resource allocation &middot; FY 2024</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase rounded-lg border border-green-100">Plan Status: Approved</span>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Audit Lifecycle Timeline</h3>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-[10px] font-bold text-slate-400">Planned</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-400"></span><span class="text-[10px] font-bold text-slate-400">In Progress</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span><span class="text-[10px] font-bold text-slate-400">Completed</span></div>
            </div>
        </div>
        
        <div class="p-8 overflow-x-auto">
            <div class="relative min-w-[800px]">
                {{-- Month Header --}}
                <div class="grid grid-cols-12 gap-0 border-b border-slate-100 pb-4 mb-8">
                    @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                        <div class="text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">{{ $m }}</div>
                    @endforeach
                </div>

                <div class="space-y-6 relative border-l border-slate-100 pl-4">
                    @foreach($audits as $audit)
                    <div class="relative group">
                        <div class="flex items-center justify-between mb-2">
                             <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-[#004ea1]">{{ $audit->audit_code }}</span>
                                <h4 class="text-xs font-black text-slate-700">{{ $audit->title }}</h4>
                             </div>
                             <span class="text-[9px] font-bold text-slate-400 italic">Target: {{ $audit->planned_end_date->format('M Y') }}</span>
                        </div>
                        
                        @php
                            $startMonth = $audit->planned_start_date->month;
                            $duration = ceil($audit->planned_start_date->diffInMonths($audit->planned_end_date)) ?: 1;
                            
                            $color = match($audit->status) {
                                'completed' => 'bg-green-500',
                                'in_progress' => 'bg-yellow-400',
                                'planned' => 'bg-blue-500',
                                'draft' => 'bg-slate-300',
                                'cancelled' => 'bg-red-200',
                                default => 'bg-slate-200',
                            };
                        @endphp
                        
                        <div class="grid grid-cols-12 gap-0 h-4 items-center">
                            <div class="col-span-full bg-slate-50 h-2 rounded-full relative">
                                <div class="absolute h-full rounded-full {{ $color }} shadow-sm transition-all group-hover:brightness-110" 
                                     style="left: {{ (($startMonth - 1) / 12) * 100 }}%; width: {{ ($duration / 12) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Resource Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
            <div class="w-10 h-10 bg-[#004ea1]/5 rounded-xl flex items-center justify-center text-[#004ea1]"><i class="fas fa-users"></i></div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Auditors</p>
                <p class="text-lg font-black text-slate-800">4 / 6</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
             <div class="w-10 h-10 bg-[#ffcc00]/10 rounded-xl flex items-center justify-center text-[#ffcc00]"><i class="fas fa-hourglass-half"></i></div>
             <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg Cycle Time</p>
                <p class="text-lg font-black text-slate-800">28 Days</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
             <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-500"><i class="fas fa-check-double"></i></div>
             <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Plan Revisions</p>
                <p class="text-lg font-black text-slate-800">1 (Q1)</p>
            </div>
        </div>
    </div>
</div>
@endsection
