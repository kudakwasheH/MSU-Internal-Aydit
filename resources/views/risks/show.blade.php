@extends('layouts.app')
@section('title', $risk->risk_code)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <span class="font-mono text-sm text-[#004ea1] font-bold bg-[#004ea1]/10 px-3 py-1 rounded-lg">{{ $risk->risk_code }}</span>
                <h2 class="text-xl font-bold text-[#333] mt-2">{{ $risk->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $risk->description }}</p>
            </div>
            <a href="{{ route('risks.edit', $risk) }}" class="px-4 py-2 bg-[#ffcc00] text-[#333] text-sm rounded-lg"><i class="fas fa-edit mr-1"></i>Edit</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-6 border-t border-slate-100">
            {{-- Inherent Column --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Inherent Risk (Gross)</h3>
                <div class="flex items-end gap-4 mb-4">
                    <div class="text-4xl font-black text-slate-700">{{ $risk->inherent_risk_score }}<span class="text-lg text-slate-300 font-normal">/25</span></div>
                    <div class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white" style="background-color: {{ $risk->inherent_risk_color }}">
                        {{ $risk->inherent_risk_level }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Likelihood</p>
                        <p class="text-sm font-bold text-slate-700">L{{ $risk->inherent_likelihood }}</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Impact</p>
                        <p class="text-sm font-bold text-slate-700">I{{ $risk->inherent_impact }}</p>
                    </div>
                </div>
            </div>

            {{-- Residual Column --}}
            <div class="p-4 bg-[#004ea1]/5 rounded-2xl border border-[#004ea1]/10">
                <h3 class="text-xs font-bold text-[#004ea1] uppercase tracking-widest mb-4">Residual Risk (Net)</h3>
                <div class="flex items-end gap-4 mb-4">
                    <div class="text-4xl font-black text-[#004ea1]">{{ $risk->residual_risk_score }}<span class="text-lg text-[#004ea1]/30 font-normal">/25</span></div>
                    <div class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white" style="background-color: {{ $risk->risk_color }}">
                        {{ $risk->risk_level }}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 font-bold text-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Likelihood</p>
                        <p class="text-sm font-bold text-slate-700">L{{ $risk->residual_likelihood }}</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Impact</p>
                        <p class="text-sm font-bold text-slate-700">I{{ $risk->residual_impact }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-slate-100">
            <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Strategic Category</p><p class="text-sm font-bold text-slate-700">{{ ucfirst($risk->category) }}</p></div>
            <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Risk Owner</p><p class="text-sm font-bold text-slate-700">{{ $risk->owner?->name }}</p></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Status</p>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold {{ $risk->status == 'active' ? 'text-green-600' : 'text-slate-400' }}">
                    <i class="fas fa-circle text-[8px]"></i>
                    {{ ucfirst($risk->status) }}
                </span>
            </div>
        </div>
    </div>
    @if($risk->audits->count())
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Linked Audits ({{ $risk->audits->count() }})</h3>
        @foreach($risk->audits as $audit)
        <a href="{{ route('audits.show', $audit) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition mb-2">
            <p class="font-mono text-xs text-[#004ea1]">{{ $audit->audit_code }}</p>
            <p class="text-sm font-medium">{{ $audit->title }}</p>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection

