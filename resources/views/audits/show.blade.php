@extends('layouts.app')
@section('title', $audit->audit_code)
@section('content')
<div x-data="{ teamModalOpen: false }" class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="font-mono text-sm text-[#004ea1] font-bold bg-[#004ea1]/10 px-3 py-1 rounded-lg">{{ $audit->audit_code }}</span>
                    <span class="px-2.5 py-1 text-xs rounded-full font-bold
                        {{ $audit->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $audit->status == 'planned' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                        {{ $audit->status == 'in_progress' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ $audit->status == 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                        {{ $audit->status == 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}">{{ ucwords(str_replace('_', ' ', $audit->status)) }}</span>
                    <span class="px-2.5 py-1 text-xs rounded-full font-bold
                        {{ $audit->priority == 'high' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                        {{ $audit->priority == 'medium' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : '' }}
                        {{ $audit->priority == 'low' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}">{{ ucfirst($audit->priority) }} Priority</span>
                </div>
                <h2 class="text-xl font-bold text-[#333]">{{ $audit->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $audit->description }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($audit->status == 'draft')
                <form method="POST" action="{{ route('audits.approve', $audit) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition"><i class="fas fa-check mr-1"></i>Approve Plan</button>
                </form>
                @endif
                @if($audit->status == 'planned')
                <form method="POST" action="{{ route('audits.submit', $audit) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition"><i class="fas fa-play mr-1"></i>Start Execution</button>
                </form>
                @endif
                @if($audit->report)
                    <a href="{{ route('reports.show', $audit->report) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-file-invoice"></i> Reports Workflow
                    </a>
                @else
                    <a href="{{ route('reports.create', ['audit_id' => $audit->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-file-signature"></i> Draft Report
                    </a>
                @endif
                <a href="{{ route('audits.edit', $audit) }}" class="px-3.5 py-2 bg-[#ffcc00] text-[#333] text-xs font-semibold rounded-lg hover:bg-yellow-400 transition"><i class="fas fa-edit mr-1"></i>Edit</a>
                <a href="{{ route('reports.download', $audit) }}" class="px-3.5 py-2 bg-[#004ea1] text-white text-xs font-semibold rounded-lg hover:bg-[#001533] transition"><i class="fas fa-file-pdf mr-1"></i>PDF</a>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100">
            <div><p class="text-xs text-gray-500">Type</p><p class="text-sm font-semibold text-gray-800">{{ ucfirst($audit->audit_type) }} Audit</p></div>
            <div><p class="text-xs text-gray-500">Created By</p><p class="text-sm font-semibold text-gray-800">{{ $audit->creator?->name }}</p></div>
            <div><p class="text-xs text-gray-500">Approved By</p><p class="text-sm font-semibold text-gray-800">{{ $audit->approver?->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Cycle Time</p><p class="text-sm font-semibold text-gray-800">{{ $audit->cycle_time ? $audit->cycle_time . ' days' : '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Budget Code (ERP)</p><p class="text-sm font-mono font-bold text-[#004ea1]">{{ $audit->budget_code ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Compliance Ref</p><p class="text-sm font-semibold text-[#004ea1]">{{ $audit->compliance_ref ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Planned Start</p><p class="text-sm font-semibold text-gray-800">{{ $audit->planned_start_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-500">Planned End</p><p class="text-sm font-semibold text-gray-800">{{ $audit->planned_end_date->format('d M Y') }}</p></div>
        </div>
    </div>

    {{-- Report Workflow Banner --}}
    @if($audit->report)
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-xl shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs uppercase font-bold text-[#ffcc00] tracking-wider">Reports Centre Workflow</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/20 text-white">
                    {{ $audit->report->status_label }}
                </span>
            </div>
            <h3 class="text-sm font-bold text-white mt-1">{{ $audit->report->title }}</h3>
            <p class="text-xs text-blue-200 mt-0.5">
                Draft Issue Date: {{ $audit->report->draft_issue_date?->format('d M Y') ?? '—' }} &middot; 
                Final Issue Date: {{ $audit->report->final_issue_date?->format('d M Y') ?? 'Pending' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.show', $audit->report) }}" class="px-4 py-2 bg-[#ffcc00] text-[#001533] text-xs font-bold rounded-lg hover:bg-yellow-400 transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-tasks"></i> Manage Review & Approval
            </a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Audit Engagement Team --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-[#004ea1]"></i>
                        <span>Allocated Audit Team</span>
                    </h3>
                    <button type="button" @click="teamModalOpen = true" class="text-xs font-bold text-[#004ea1] hover:underline flex items-center gap-1">
                        <i class="fas fa-user-plus text-[10px]"></i> Manage Team
                    </button>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-[#004ea1] text-white flex items-center justify-center text-xs font-bold shadow-xs">
                            {{ substr($audit->creator->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Lead Auditor / Creator</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $audit->creator->name }}</p>
                        </div>
                    </div>

                    @foreach($audit->teamMembers as $member)
                    <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Audit Team Member</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $member->name }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ $member->position }}</p>
                        </div>
                    </div>
                    @endforeach

                    @if($audit->teamMembers->isEmpty())
                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <p class="text-xs text-gray-500">No additional team members assigned.</p>
                        <button type="button" @click="teamModalOpen = true" class="mt-2 text-xs font-bold text-[#004ea1] hover:underline">
                            + Assign Team Members
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Linked Risks --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-2"><i class="fas fa-exclamation-triangle text-[#ffcc00]"></i> Risk Profile</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $audit->risks->count() }} Linked</span>
                </h3>
                <div class="space-y-2.5">
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

    {{-- Assign Team Modal --}}
    <div x-show="teamModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/50" @click="teamModalOpen = false"></div>

            <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-users-cog text-[#004ea1]"></i>
                        <span>Manage Audit Team Allocation</span>
                    </h3>
                    <button type="button" @click="teamModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('audits.assign-team', $audit) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Auditors Allocated to {{ $audit->audit_code }}</label>
                        <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto border rounded-xl p-3 bg-gray-50/50">
                            @foreach($allUsers ?? [] as $user)
                            <label class="flex items-start gap-3 p-2 rounded-lg bg-white border border-gray-200 hover:border-[#004ea1] cursor-pointer transition">
                                <input type="checkbox" name="team_members[]" value="{{ $user->id }}" {{ $audit->teamMembers->contains($user->id) ? 'checked' : '' }} class="mt-0.5 rounded text-[#004ea1] focus:ring-[#004ea1]">
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $user->position }} &middot; {{ $user->department }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="teamModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#004ea1] text-white text-xs font-bold rounded-lg hover:bg-[#001533] transition shadow-sm">
                            Save Team Allocation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
