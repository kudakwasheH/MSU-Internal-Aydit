@extends('layouts.app')
@section('title', 'Prepare Audit Report')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#333] flex items-center gap-2">
                <i class="fas fa-file-signature text-[#004ea1]"></i>
                <span>Prepare Audit Report</span>
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Draft and issue report for Senior Internal Auditor review</p>
        </div>
        <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:text-[#004ea1] flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
            @csrf

            {{-- Audit Selection / Confirmation --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Select Audit Engagement <span class="text-red-500">*</span>
                </label>
                @if($audit)
                    <input type="hidden" name="audit_id" value="{{ $audit->id }}">
                    <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold bg-[#004ea1] text-white px-2.5 py-0.5 rounded">
                                    {{ $audit->audit_code }}
                                </span>
                                <span class="text-sm font-bold text-gray-800">{{ $audit->title }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ ucfirst($audit->audit_type) }} Audit &middot; Status: {{ ucwords(str_replace('_', ' ', $audit->status)) }}
                                @if($audit->teamMembers->count())
                                    &middot; Team: {{ $audit->teamMembers->pluck('name')->join(', ') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-[#004ea1]">
                            <span class="px-2 py-1 bg-white rounded border border-blue-200">
                                {{ $audit->findings->count() }} Findings
                            </span>
                            <span class="px-2 py-1 bg-white rounded border border-blue-200">
                                {{ $audit->workingPapers->count() }} Working Papers
                            </span>
                        </div>
                    </div>
                @else
                    <select name="audit_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                        <option value="">-- Select Completed or In-Progress Engagement --</option>
                        @foreach($availableAudits as $avail)
                            <option value="{{ $avail->id }}" {{ old('audit_id') == $avail->id ? 'selected' : '' }}>
                                {{ $avail->audit_code }} — {{ $avail->title }} ({{ ucfirst($avail->status) }})
                            </option>
                        @endforeach
                    </select>
                    @error('audit_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Report Title --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Report Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $audit ? 'Internal Audit Report: ' . $audit->title : '') }}" required placeholder="e.g., Internal Audit Report on Procurement Operations" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Draft Issue Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Draft Issue Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="draft_issue_date" value="{{ old('draft_issue_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    <p class="text-[11px] text-gray-400 mt-0.5">Recorded as the initial draft release date</p>
                    @error('draft_issue_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Executive Summary --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Executive Summary & Key Findings
                </label>
                <textarea name="executive_summary" rows="4" placeholder="Provide a high-level summary of the audit engagement, objectives, key strengths observed, and significant risk exposures..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('executive_summary') }}</textarea>
                <p class="text-[11px] text-gray-400 mt-0.5">Summarize high-level conclusions for Senior Internal Auditor and Chief Internal Auditor</p>
            </div>

            {{-- Scope & Methodology --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Scope, Limitations & Methodology
                </label>
                <textarea name="scope" rows="3" placeholder="Outline the audit period, systems evaluated, sample sizes tested, and any limitation in scope..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('scope') }}</textarea>
            </div>

            {{-- Transmittal Comments --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Preparer Comments / Review Notes
                </label>
                <textarea name="comments" rows="3" placeholder="Add any specific observations or areas that require special review attention from the Senior Auditor..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('comments') }}</textarea>
            </div>

            {{-- Submission Mode --}}
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="submit_for_review" value="1" {{ old('submit_for_review', true) ? 'checked' : '' }} class="mt-1 rounded text-[#004ea1] focus:ring-[#004ea1]">
                    <div>
                        <p class="text-sm font-bold text-gray-800">Immediately submit to Senior Internal Auditor for review</p>
                        <p class="text-xs text-gray-500 mt-0.5">If unchecked, the report will be saved as a draft editable by the audit team before forwarding.</p>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-[#004ea1] text-white text-sm font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Save & Proceed</span>
                </button>
                <a href="{{ route('reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
