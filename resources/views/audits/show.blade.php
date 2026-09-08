@extends('layouts.app')
@section('title', $audit->audit_code)
@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm text-[#004ea1] font-bold bg-[#004ea1]/10 px-3 py-1 rounded-lg">{{ $audit->audit_code }}</span>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $audit->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $audit->status == 'planned' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $audit->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $audit->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $audit->status == 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">{{ ucwords(str_replace('_', ' ', $audit->status)) }}</span>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $audit->priority == 'high' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $audit->priority == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $audit->priority == 'low' ? 'bg-green-100 text-green-700' : '' }}">{{ ucfirst($audit->priority) }} Priority</span>
                </div>
                <h2 class="text-xl font-bold text-[#333]">{{ $audit->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $audit->description }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($audit->status == 'draft')
                <form method="POST" action="{{ route('audits.approve', $audit) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"><i class="fas fa-check mr-1"></i>Approve</button>
                </form>
                @endif
                @if($audit->status == 'planned')
                <form method="POST" action="{{ route('audits.submit', $audit) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"><i class="fas fa-play mr-1"></i>Start</button>
                </form>
                @endif
                @if(in_array($audit->status, ['in_progress', 'completed']))
                    @if($audit->report)
                        <a href="{{ route('reports.show', $audit->report) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition"><i class="fas fa-file-invoice mr-1"></i>View Report</a>
                    @else
                        <form method="POST" action="{{ route('reports.store') }}">
                            @csrf
                            <input type="hidden" name="audit_id" value="{{ $audit->id }}">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition"><i class="fas fa-file-invoice mr-1"></i>Draft Report</button>
                        </form>
                    @endif
                @endif
                <a href="{{ route('audits.edit', $audit) }}" class="px-4 py-2 bg-[#ffcc00] text-[#333] text-sm rounded-lg hover:bg-yellow-400 transition"><i class="fas fa-edit mr-1"></i>Edit</a>
                <a href="{{ route('reports.download', $audit) }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm rounded-lg hover:bg-[#001533] transition"><i class="fas fa-file-pdf mr-1"></i>PDF</a>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100">
            <div><p class="text-xs text-gray-500">Type</p><p class="text-sm font-medium">{{ ucfirst($audit->audit_type) }}</p></div>
            <div><p class="text-xs text-gray-500">Created By</p><p class="text-sm font-medium">{{ $audit->creator?->name }}</p></div>
            <div><p class="text-xs text-gray-500">Approved By</p><p class="text-sm font-medium">{{ $audit->approver?->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Cycle Time</p><p class="text-sm font-medium">{{ $audit->cycle_time ? $audit->cycle_time . ' days' : '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Budget Code (ERP)</p><p class="text-sm font-medium text-[#004ea1]">{{ $audit->budget_code ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Compliance Ref</p><p class="text-sm font-medium text-[#004ea1]">{{ $audit->compliance_ref ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Engagement Type</p><p class="text-sm font-medium">{{ ucfirst($audit->audit_type) }}</p></div>
            <div><p class="text-xs text-gray-500">Planned Start</p><p class="text-sm font-medium">{{ $audit->planned_start_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-500">Planned End</p><p class="text-sm font-medium">{{ $audit->planned_end_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-500">Actual Start</p><p class="text-sm font-medium">{{ $audit->actual_start_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Actual End</p><p class="text-sm font-medium">{{ $audit->actual_end_date?->format('d M Y') ?? '—' }}</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Linked Risks --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
                <h3 class="text-sm font-bold text-slate-700 mb-4 px-2 border-l-4 border-[#ffcc00] flex items-center justify-between">
                    <span class="flex items-center gap-2"><i class="fas fa-exclamation-triangle text-[#ffcc00]"></i> Risk Profile</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $audit->risks->count() }} Linked</span>
                </h3>
                <div class="space-y-3">
                    @foreach($audit->risks as $risk)
                    <a href="{{ route('risks.show', $risk) }}" class="block p-3 bg-slate-50 rounded-xl hover:bg-[#004ea1]/5 border border-transparent hover:border-[#004ea1]/20 transition-all group">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-[10px] font-bold text-[#004ea1] group-hover:underline">{{ $risk->risk_code }}</p>
                            <span class="text-[9px] font-black uppercase" style="color: {{ $risk->risk_color }}">{{ $risk->risk_level }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-700 leading-tight">{{ $risk->title }}</p>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Audit Team & Metadata --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
                <h3 class="text-sm font-bold text-slate-700 mb-4 px-2 border-l-4 border-[#004ea1]">Engagement Team</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[#004ea1] text-xs">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Drafted By</p>
                            <p class="text-xs font-bold text-slate-700">{{ $audit->creator->name }}</p>
                        </div>
                    </div>
                    @if($audit->approver)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-green-600 text-xs">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Authorized By</p>
                            <p class="text-xs font-bold text-slate-700">{{ $audit->approver->name }}</p>
                        </div>
                    </div>
                    @endif
                    @foreach($audit->teamMembers as $member)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-blue-600 text-xs">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Team Member</p>
                            <p class="text-xs font-bold text-slate-700">{{ $member->name }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Fieldwork & Working Papers --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest"><i class="fas fa-file-signature text-[#004ea1] mr-2"></i>Fieldwork & Working Papers</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Primary audit evidence and testing documentation</p>
                    </div>
                    <a href="{{ route('working-papers.create') }}?audit_id={{ $audit->id }}" class="px-4 py-1.5 bg-[#004ea1] text-white text-[10px] font-bold rounded-lg hover:bg-[#001c40] transition-all shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Record Fieldwork
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($audit->workingPapers as $wp)
                        <div class="group flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-transparent hover:border-[#004ea1]/20 hover:bg-white transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-[#004ea1] shadow-sm border border-slate-100">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $wp->title }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-medium text-slate-400 italic">By {{ $wp->creator->name }} &middot; v{{ $wp->version }}</span>
                                        <span class="px-1.5 py-0.5 rounded-md text-[8px] font-black uppercase tracking-tighter {{ $wp->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $wp->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('working-papers.show', $wp) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-[#004ea1] hover:border-[#004ea1] transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400 text-xl">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-400">No fieldwork documented for this engagement.</p>
                            <p class="text-[10px] text-slate-300 italic mt-1 font-medium">Auditors must document all tests and associate evidence here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Audit Findings --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest"><i class="fas fa-search text-red-500 mr-2"></i>Deficiencies & Findings</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Identified control gaps and non-compliance issues</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($audit->findings as $finding)
                        <a href="{{ route('findings.show', $finding) }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-[#ff4444]/5 border border-transparent hover:border-[#ff4444]/20 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-10 rounded-full @if($finding->severity == 'critical') bg-red-600 @elseif($finding->severity == 'high') bg-orange-500 @elseif($finding->severity == 'medium') bg-yellow-400 @else bg-green-500 @endif"></div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $finding->title }}</h4>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5 uppercase">Severity: {{ $finding->severity }} &middot; Status: {{ $finding->status }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-xs font-bold text-slate-400">No findings recorded yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

