@extends('layouts.app')
@section('title', 'Report Workflow — ' . $report->audit->audit_code)
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Top Navigation & Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="font-mono text-xs font-bold text-[#004ea1] bg-[#004ea1]/10 px-3 py-1 rounded-lg">
                        {{ $report->audit->audit_code }}
                    </span>
                    @if($report->status === 'draft')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Draft Preparation</span>
                    @elseif($report->status === 'pending_senior_review')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 animate-pulse">Under Senior Review</span>
                    @elseif($report->status === 'pending_chief_approval')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Under Chief Auditor Review</span>
                    @elseif($report->status === 'chief_approved')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Chief Approved</span>
                    @elseif($report->status === 'returned_for_revision')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Returned for Corrections</span>
                    @elseif($report->status === 'final_issued')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Final Report Issued</span>
                    @endif
                    <span class="text-xs text-gray-400 capitalize">&middot; {{ $report->audit->audit_type }} Audit</span>
                </div>
                <h2 class="text-xl font-bold text-[#333]">{{ $report->title ?? ('Audit Report: ' . $report->audit->title) }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ $report->audit->title }}</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('reports.edit', $report) }}" class="px-3.5 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition flex items-center gap-1.5">
                    <i class="fas fa-edit"></i> Edit Report
                </a>
                <a href="{{ route('reports.download', $report->audit) }}" class="px-4 py-2 bg-[#004ea1] text-white text-xs font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-file-pdf"></i> Download Official PDF
                </a>
                <a href="{{ route('reports.index') }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        {{-- Workflow Stepper --}}
        <div class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Review & Approval Lifecycle</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- Step 1: Draft --}}
                <div class="p-3.5 rounded-xl border {{ $report->workflow_step >= 1 ? 'bg-blue-50/50 border-blue-200' : 'bg-gray-50 border-gray-200 opacity-60' }} relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-800">1. Draft Prepared</span>
                        <i class="fas {{ $report->draft_issue_date ? 'fa-check-circle text-blue-600' : 'fa-circle text-gray-300' }} text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-800">{{ $report->preparer->name }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">
                        Issue Date: <span class="font-semibold text-gray-700">{{ $report->draft_issue_date ? $report->draft_issue_date->format('d M Y') : 'Pending' }}</span>
                    </p>
                </div>

                {{-- Step 2: Senior Review --}}
                <div class="p-3.5 rounded-xl border {{ $report->workflow_step >= 2 ? 'bg-blue-50/50 border-blue-200' : 'bg-gray-50 border-gray-200 opacity-60' }} relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $report->workflow_step >= 2 ? 'text-blue-800' : 'text-gray-500' }}">2. Senior Review</span>
                        <i class="fas {{ $report->senior_reviewer_id ? 'fa-check-circle text-blue-600' : ($report->status === 'pending_senior_review' ? 'fa-spinner fa-spin text-blue-500' : 'fa-circle text-gray-300') }} text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-800">{{ $report->seniorReviewer?->name ?? ($report->status === 'pending_senior_review' ? 'Pending Senior Sign-off' : 'Pending Submission') }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Senior Internal Auditor</p>
                </div>

                {{-- Step 3: Chief Approval --}}
                <div class="p-3.5 rounded-xl border {{ $report->workflow_step >= 3 ? 'bg-purple-50/50 border-purple-200' : 'bg-gray-50 border-gray-200 opacity-60' }} relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $report->workflow_step >= 3 ? 'text-purple-800' : 'text-gray-500' }}">3. Chief Review</span>
                        <i class="fas {{ $report->chief_approver_id ? 'fa-check-circle text-purple-600' : ($report->status === 'pending_chief_approval' ? 'fa-spinner fa-spin text-purple-500' : 'fa-circle text-gray-300') }} text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-800">{{ $report->chiefApprover?->name ?? ($report->status === 'pending_chief_approval' ? 'Pending Chief Review' : 'Pending Forwarding') }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Chief Internal Auditor</p>
                </div>

                {{-- Step 4: Final Issuance --}}
                <div class="p-3.5 rounded-xl border {{ $report->status === 'final_issued' ? 'bg-emerald-50 border-emerald-300' : 'bg-gray-50 border-gray-200 opacity-60' }} relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $report->status === 'final_issued' ? 'text-emerald-800' : 'text-gray-500' }}">4. Final Issuance</span>
                        <i class="fas {{ $report->status === 'final_issued' ? 'fa-check-double text-emerald-600' : 'fa-lock text-gray-300' }} text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-800">{{ $report->status === 'final_issued' ? 'Official Release' : 'Pending Approval' }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">
                        Final Date: <span class="font-semibold text-gray-700">{{ $report->final_issue_date ? $report->final_issue_date->format('d M Y') : '—' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Review & Action Command Centre --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-tasks text-[#004ea1]"></i>
            <span>Workflow Actions & Stage Progression</span>
        </h3>

        {{-- STAGE 1: DRAFT OR RETURNED FOR REVISION --}}
        @if($report->status === 'draft' || $report->status === 'returned_for_revision')
            <div class="p-5 bg-blue-50/50 border border-blue-200 rounded-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-blue-950">Draft Report Submission to Senior Auditor</h4>
                        <p class="text-xs text-blue-800 mt-0.5">Once the audit team has prepared the draft report and addressed all initial observations, submit it to the Senior Internal Auditor for quality review.</p>
                    </div>
                    <span class="px-2 py-1 bg-white text-blue-700 text-xs font-bold rounded border border-blue-200">
                        {{ $report->status === 'returned_for_revision' ? 'Revised Draft' : 'Draft' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('reports.submit-senior', $report) }}" class="space-y-3 pt-2 border-t border-blue-200/60">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Confirm Draft Issue Date</label>
                            <input type="date" name="draft_issue_date" value="{{ $report->draft_issue_date ? $report->draft_issue_date->format('Y-m-d') : date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white focus:ring-[#004ea1]">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Transmittal Note to Senior Auditor (Optional)</label>
                            <input type="text" name="note" placeholder="e.g. Fieldwork complete. Draft report submitted for your review." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white focus:ring-[#004ea1]">
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#004ea1] text-white text-xs font-bold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Submit to Senior Internal Auditor for Review</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- STAGE 2: PENDING SENIOR REVIEW --}}
        @if($report->status === 'pending_senior_review')
            <div class="p-5 bg-indigo-50/50 border border-indigo-200 rounded-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-indigo-950 flex items-center gap-2">
                            <i class="fas fa-user-check text-indigo-600"></i>
                            <span>Senior Internal Auditor Review Gate</span>
                        </h4>
                        <p class="text-xs text-indigo-800 mt-0.5">Review the draft report, evidence, and findings. If satisfactory, sign off and forward to the Chief Internal Auditor. If adjustments are needed, return with required corrections.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-white text-indigo-700 text-xs font-bold rounded border border-indigo-200">
                        Senior Review Required
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-indigo-200/60">
                    {{-- Option A: Forward to Chief --}}
                    <form method="POST" action="{{ route('reports.submit-chief', $report) }}" class="p-4 bg-white rounded-lg border border-indigo-100 space-y-3">
                        @csrf
                        <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider text-green-700 flex items-center gap-1.5">
                            <i class="fas fa-check-circle"></i> Approve & Forward to Chief
                        </h5>
                        <textarea name="senior_comments" rows="2" placeholder="Add Senior Review sign-off endorsement notes for the Chief Auditor..." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-[#004ea1]"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-purple-700 text-white text-xs font-bold rounded-lg hover:bg-purple-800 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-double"></i>
                            <span>Endorse & Forward to Chief Auditor</span>
                        </button>
                    </form>

                    {{-- Option B: Return for Revision --}}
                    <form method="POST" action="{{ route('reports.return-revision', $report) }}" class="p-4 bg-white rounded-lg border border-red-100 space-y-3">
                        @csrf
                        <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                            <i class="fas fa-undo"></i> Request Corrections / Changes
                        </h5>
                        <textarea name="comments" required rows="2" placeholder="Specify required corrections, additional evidence, or editorial amendments..." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-amber-500"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-undo"></i>
                            <span>Return to Audit Team for Corrections</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- STAGE 3: PENDING CHIEF APPROVAL --}}
        @if($report->status === 'pending_chief_approval')
            <div class="p-5 bg-purple-50/50 border border-purple-200 rounded-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-purple-950 flex items-center gap-2">
                            <i class="fas fa-stamp text-purple-600"></i>
                            <span>Chief Internal Auditor Review & Final Sign-off</span>
                        </h4>
                        <p class="text-xs text-purple-800 mt-0.5">As Chief Internal Auditor, perform executive review. Once approved, the official final report can be issued.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-white text-purple-700 text-xs font-bold rounded border border-purple-200">
                        Chief Approval Required
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-purple-200/60">
                    {{-- Option A: Chief Approve and Issue Final Report --}}
                    <form method="POST" action="{{ route('reports.issue', $report) }}" class="p-4 bg-white rounded-lg border border-purple-100 space-y-3">
                        @csrf
                        <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                            <i class="fas fa-stamp"></i> Approve & Issue Final Report
                        </h5>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Final Issue Date</label>
                            <input type="date" name="final_issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-[#004ea1]">
                        </div>
                        <textarea name="issuance_notes" rows="2" placeholder="Official issuance notes or distribution directives..." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-[#004ea1]"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span>Authorize & Issue Final Report</span>
                        </button>
                    </form>

                    {{-- Option B: Return for Corrections --}}
                    <form method="POST" action="{{ route('reports.return-revision', $report) }}" class="p-4 bg-white rounded-lg border border-red-100 space-y-3">
                        @csrf
                        <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                            <i class="fas fa-undo"></i> Request Corrections
                        </h5>
                        <textarea name="comments" required rows="3" placeholder="Chief Auditor comments and corrections to be addressed..." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-amber-500"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-undo"></i>
                            <span>Return for Adjustments</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- STAGE 4: CHIEF APPROVED (READY FOR ISSUANCE) --}}
        @if($report->status === 'chief_approved')
            <div class="p-5 bg-emerald-50 border border-emerald-300 rounded-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            <span>Report Approved by Chief Internal Auditor</span>
                        </h4>
                        <p class="text-xs text-emerald-800 mt-0.5">All reviews and approvals have been completed. Issue the final report to stakeholders.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('reports.issue', $report) }}" class="flex flex-wrap items-end gap-3 pt-2 border-t border-emerald-200">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Final Issue Date</label>
                        <input type="date" name="final_issue_date" value="{{ date('Y-m-d') }}" required class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-check-double"></i>
                        <span>Release Official Final Report</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- STAGE 5: FINAL ISSUED --}}
        @if($report->status === 'final_issued')
            <div class="p-5 bg-emerald-50/80 border border-emerald-300 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-950">Official Final Report Released</h4>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Issued on <span class="font-bold">{{ $report->final_issue_date ? $report->final_issue_date->format('d F Y') : '—' }}</span> &middot; Approved by {{ $report->chiefApprover?->name ?? 'Chief Internal Auditor' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('reports.download', $report->audit) }}" class="px-4 py-2 bg-emerald-700 text-white text-xs font-bold rounded-lg hover:bg-emerald-800 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Download Official Final PDF
                </a>
            </div>
        @endif
    </div>

    {{-- Main Grid: Governance Meta & Review Trail --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left: Engagement & Sign-offs Card --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Dates & Governance Box --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-[#004ea1]"></i>
                    <span>Key Dates & Sign-offs</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500 font-medium">Draft Issue Date</span>
                        <span class="font-bold text-gray-800">{{ $report->draft_issue_date ? $report->draft_issue_date->format('d M Y') : '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500 font-medium">Final Issue Date</span>
                        <span class="font-bold {{ $report->final_issue_date ? 'text-emerald-700' : 'text-gray-400' }}">
                            {{ $report->final_issue_date ? $report->final_issue_date->format('d M Y') : 'Pending Issuance' }}
                        </span>
                    </div>

                    <div class="py-1.5 border-b border-gray-50">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Prepared By (Audit Team)</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $report->preparer->name }}</p>
                        <p class="text-[10px] text-gray-500">{{ $report->preparer->position }}</p>
                    </div>

                    <div class="py-1.5 border-b border-gray-50">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Senior Internal Auditor Sign-off</p>
                        @if($report->seniorReviewer)
                            <p class="font-bold text-blue-700 mt-0.5 flex items-center gap-1">
                                <i class="fas fa-check-circle text-[10px]"></i> {{ $report->seniorReviewer->name }}
                            </p>
                            <p class="text-[10px] text-gray-500">{{ $report->seniorReviewer->position }}</p>
                        @else
                            <p class="text-xs text-gray-400 italic mt-0.5">Pending Senior Review</p>
                        @endif
                    </div>

                    <div class="py-1.5">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Chief Internal Auditor Approval</p>
                        @if($report->chiefApprover)
                            <p class="font-bold text-purple-700 mt-0.5 flex items-center gap-1">
                                <i class="fas fa-check-circle text-[10px]"></i> {{ $report->chiefApprover->name }}
                            </p>
                            <p class="text-[10px] text-gray-500">{{ $report->chiefApprover->position }}</p>
                        @else
                            <p class="text-xs text-gray-400 italic mt-0.5">Pending Chief Approval</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Audit Engagement Team Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-[#004ea1]"></i>
                        <span>Audit Team Allocated</span>
                    </h3>
                    <span class="text-[10px] font-mono font-bold text-[#004ea1] bg-[#004ea1]/10 px-2 py-0.5 rounded">
                        {{ $report->audit->teamMembers->count() }} Members
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($report->audit->teamMembers as $member)
                        <div class="flex items-center gap-2.5 p-2 bg-gray-50 rounded-lg">
                            <div class="w-7 h-7 rounded-full bg-[#004ea1] text-white flex items-center justify-center text-[10px] font-bold">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-gray-800 truncate">{{ $member->name }}</p>
                                <p class="text-[10px] text-gray-500 truncate">{{ $member->position }} &middot; {{ $member->department }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic py-2">No team members explicitly allocated.</p>
                    @endforelse
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <a href="{{ route('audits.show', $report->audit) }}" class="text-xs text-[#004ea1] hover:underline font-semibold flex items-center gap-1">
                        <i class="fas fa-external-link-alt text-[10px]"></i> View Audit Universe Engagement
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Executive Summary & Review Comments Log --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Report Body Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div>
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Executive Summary & Conclusions</h3>
                    <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-700 whitespace-pre-wrap leading-relaxed border border-gray-100">
                        {{ $report->executive_summary ?? 'No executive summary drafted yet. Click "Edit Report" to provide executive summary.' }}
                    </div>
                </div>

                @if($report->scope)
                <div>
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Scope & Testing Methodology</h3>
                    <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-700 whitespace-pre-wrap leading-relaxed border border-gray-100">
                        {{ $report->scope }}
                    </div>
                </div>
                @endif

                @if($report->comments)
                <div>
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Initial Transmittal Comments</h3>
                    <div class="p-3 bg-blue-50/50 rounded-lg text-xs text-blue-900 border border-blue-100">
                        {{ $report->comments }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Review Comments, Observations & Corrections Tracker --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-comments text-[#004ea1]"></i>
                            <span>Review Comments & Corrections Tracker</span>
                        </h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">Track reviewer feedback, required adjustments, and resolution by the audit team</p>
                    </div>
                </div>

                {{-- Comments list --}}
                <div class="space-y-3">
                    @php $notes = $report->review_notes ?? []; @endphp
                    @forelse($notes as $index => $note)
                        <div class="p-4 rounded-xl border {{ !empty($note['addressed']) ? 'bg-gray-50 border-gray-200' : 'bg-amber-50/70 border-amber-200' }}">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                                        {{ substr($note['user_name'] ?? 'U', 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ $note['user_name'] ?? 'Reviewer' }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $note['role'] ?? 'Auditor' }} &middot; {{ $note['action'] ?? 'Comment' }} &middot; {{ \Carbon\Carbon::parse($note['created_at'] ?? now())->diffForHumans() }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(!empty($note['addressed']))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 flex items-center gap-1">
                                            <i class="fas fa-check text-[9px]"></i> Addressed
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-[9px]"></i> Action Required
                                        </span>
                                    @endif

                                    <form method="POST" action="{{ route('reports.toggle-comment', [$report, $index]) }}">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-gray-500 hover:text-[#004ea1] underline font-medium" title="Toggle correction status">
                                            {{ !empty($note['addressed']) ? 'Mark Pending' : 'Mark Addressed' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-xs text-gray-700 whitespace-pre-wrap pl-8">{{ $note['comment'] ?? '' }}</p>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <i class="fas fa-check-circle text-gray-300 text-2xl mb-1"></i>
                            <p class="text-xs font-bold text-gray-500">No review comments recorded yet.</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Senior and Chief Auditors can add observations or return the report for specific corrections.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Add Comment Form --}}
                <form method="POST" action="{{ route('reports.add-comment', $report) }}" class="pt-4 border-t border-gray-100 space-y-3">
                    @csrf
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Add Observation or Response</label>
                        <select name="type" class="px-2.5 py-1 border border-gray-300 rounded text-xs bg-white">
                            <option value="observation">Observation / Feedback</option>
                            <option value="correction">Required Correction</option>
                            <option value="response">Audit Team Response</option>
                        </select>
                    </div>
                    <textarea name="comment" required rows="2" placeholder="Type review observation, audit team response, or note here..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-[#004ea1]"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded-lg hover:bg-black transition flex items-center gap-1.5">
                            <i class="fas fa-comment-dots"></i> Post Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
