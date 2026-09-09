@extends('layouts.app')
@section('title', 'Reports Centre — Review & Approval Workflow')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#004ea1] animate-pulse"></span>
                <h2 class="text-xl font-bold text-[#333]">Reports Centre</h2>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">Multi-stage report review, senior sign-off, chief auditor approval, and final report issuance workflow</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Prepare New Report</span>
            </a>
            <a href="{{ route('reports.generate') }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-chart-pie text-[#004ea1]"></i>
                <span class="hidden sm:inline">Analytics</span>
            </a>
        </div>
    </div>

    {{-- Workflow Status KPI Counters --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <a href="{{ route('reports.index') }}" class="p-4 rounded-xl border {{ !request('status') ? 'bg-[#004ea1]/5 border-[#004ea1] ring-1 ring-[#004ea1]' : 'bg-white border-gray-200 hover:border-gray-300' }} transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500">All Reports</p>
                <i class="fas fa-layer-group text-xs text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-[#004ea1] mt-2">{{ $counts['all'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Total in cycle</p>
        </a>

        <a href="{{ route('reports.index', ['status' => 'draft']) }}" class="p-4 rounded-xl border {{ request('status') == 'draft' ? 'bg-amber-50 border-amber-400 ring-1 ring-amber-400' : 'bg-white border-gray-200 hover:border-gray-300' }} transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500">Draft & Revisions</p>
                <i class="fas fa-edit text-xs text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-2">{{ $counts['draft'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Audit team preparing</p>
        </a>

        <a href="{{ route('reports.index', ['status' => 'pending_senior_review']) }}" class="p-4 rounded-xl border {{ request('status') == 'pending_senior_review' ? 'bg-blue-50 border-blue-400 ring-1 ring-blue-400' : 'bg-white border-gray-200 hover:border-gray-300' }} transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500">Senior Review</p>
                <i class="fas fa-user-check text-xs text-blue-500"></i>
            </div>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $counts['pending_senior'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Senior Auditor review</p>
        </a>

        <a href="{{ route('reports.index', ['status' => 'pending_chief_approval']) }}" class="p-4 rounded-xl border {{ request('status') == 'pending_chief_approval' ? 'bg-purple-50 border-purple-400 ring-1 ring-purple-400' : 'bg-white border-gray-200 hover:border-gray-300' }} transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500">Chief Approval</p>
                <i class="fas fa-stamp text-xs text-purple-500"></i>
            </div>
            <p class="text-2xl font-bold text-purple-600 mt-2">{{ $counts['pending_chief'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Chief Auditor review</p>
        </a>

        <a href="{{ route('reports.index', ['status' => 'final_issued']) }}" class="p-4 rounded-xl border {{ request('status') == 'final_issued' ? 'bg-emerald-50 border-emerald-400 ring-1 ring-emerald-400' : 'bg-white border-gray-200 hover:border-gray-300' }} transition">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500">Final Issued</p>
                <i class="fas fa-check-circle text-xs text-emerald-500"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $counts['final_issued'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Officially released</p>
        </a>
    </div>

    {{-- Filter and Search Bar --}}
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search Reports</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Audit code, title, or comments..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Workflow Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                    <option value="">All Workflow Stages</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending_senior_review" {{ request('status') == 'pending_senior_review' ? 'selected' : '' }}>Pending Senior Review</option>
                    <option value="pending_chief_approval" {{ request('status') == 'pending_chief_approval' ? 'selected' : '' }}>Pending Chief Approval</option>
                    <option value="chief_approved" {{ request('status') == 'chief_approved' ? 'selected' : '' }}>Chief Approved</option>
                    <option value="returned_for_revision" {{ request('status') == 'returned_for_revision' ? 'selected' : '' }}>Returned for Revision</option>
                    <option value="final_issued" {{ request('status') == 'final_issued' ? 'selected' : '' }}>Final Report Issued</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-filter text-xs"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    {{-- Workflow Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5">Audit Assignment</th>
                        <th class="px-5 py-3.5">Workflow Stage & Status</th>
                        <th class="px-5 py-3.5">Audit Team</th>
                        <th class="px-5 py-3.5">Draft Issue Date</th>
                        <th class="px-5 py-3.5">Final Issue Date</th>
                        <th class="px-5 py-3.5">Sign-off Signatures</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <a href="{{ route('reports.show', $report) }}" class="font-mono text-xs font-bold text-[#004ea1] hover:underline flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice text-[10px]"></i>
                                    {{ $report->audit->audit_code }}
                                </a>
                                <span class="text-xs text-gray-800 font-medium mt-0.5 max-w-xs truncate">{{ $report->title ?? $report->audit->title }}</span>
                                <span class="text-[10px] text-gray-400 capitalize">{{ $report->audit->audit_type }} Audit &middot; {{ ucfirst($report->audit->priority) }} Priority</span>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            @if($report->status === 'draft')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Draft Preparation
                                </span>
                            @elseif($report->status === 'pending_senior_review')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span> Senior Auditor Review
                                </span>
                            @elseif($report->status === 'pending_chief_approval')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Chief Auditor Review
                                </span>
                            @elseif($report->status === 'chief_approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="fas fa-check text-[10px]"></i> Chief Approved
                                </span>
                            @elseif($report->status === 'returned_for_revision')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fas fa-undo text-[10px]"></i> Corrections Required
                                </span>
                            @elseif($report->status === 'final_issued')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-double text-[10px]"></i> Final Report Issued
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    {{ ucwords(str_replace('_', ' ', $report->status)) }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                @if($report->audit->teamMembers->count())
                                    <div class="flex -space-x-2 overflow-hidden">
                                        @foreach($report->audit->teamMembers->take(3) as $member)
                                            <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center" title="{{ $member->name }} ({{ $member->position }})">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($report->audit->teamMembers->count() > 3)
                                        <span class="text-[10px] font-semibold text-gray-500">+{{ $report->audit->teamMembers->count() - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">No team assigned</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            @if($report->draft_issue_date)
                                <div class="flex items-center gap-1.5 text-xs text-gray-700">
                                    <i class="far fa-calendar-alt text-gray-400"></i>
                                    <span>{{ $report->draft_issue_date->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if($report->final_issue_date)
                                <div class="flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                                    <i class="fas fa-calendar-check text-emerald-500"></i>
                                    <span>{{ $report->final_issue_date->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Pending Issuance</span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 text-[11px]">
                                <span class="px-1.5 py-0.5 rounded {{ $report->preparer ? 'bg-green-50 text-green-700 font-semibold' : 'bg-gray-100 text-gray-400' }}" title="Prepared by {{ $report->preparer?->name }}">
                                    <i class="fas fa-pen text-[9px] mr-1"></i>Prep
                                </span>
                                <span class="px-1.5 py-0.5 rounded {{ $report->seniorReviewer ? 'bg-blue-50 text-blue-700 font-semibold' : 'bg-gray-100 text-gray-400' }}" title="{{ $report->seniorReviewer ? 'Reviewed by ' . $report->seniorReviewer->name : 'Senior Review Pending' }}">
                                    <i class="fas fa-user-check text-[9px] mr-1"></i>Snr
                                </span>
                                <span class="px-1.5 py-0.5 rounded {{ $report->chiefApprover ? 'bg-purple-50 text-purple-700 font-semibold' : 'bg-gray-100 text-gray-400' }}" title="{{ $report->chiefApprover ? 'Approved by ' . $report->chiefApprover->name : 'Chief Approval Pending' }}">
                                    <i class="fas fa-stamp text-[9px] mr-1"></i>Chief
                                </span>
                            </div>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('reports.show', $report) }}" class="px-3 py-1.5 bg-[#004ea1] text-white text-xs font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-1">
                                    <i class="fas fa-eye text-[10px]"></i> View Workflow
                                </a>
                                <a href="{{ route('reports.download', $report->audit) }}" class="p-1.5 text-gray-500 hover:text-red-600 rounded-lg hover:bg-gray-100 transition" title="Download Official PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-[#004ea1] flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700">No reports matching your criteria.</p>
                                <p class="text-xs text-gray-400 mt-1">Audit team members can prepare and submit draft reports once audit engagements are completed.</p>
                                <a href="{{ route('reports.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-[#004ea1] text-white text-xs font-semibold rounded-lg hover:bg-[#001533] transition">
                                    <i class="fas fa-plus"></i> Prepare Draft Report
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $reports->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
