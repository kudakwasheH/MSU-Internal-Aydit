@extends('layouts.app')
@section('title', 'Edit Report — ' . $report->audit->audit_code)
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#333] flex items-center gap-2">
                <i class="fas fa-edit text-[#004ea1]"></i>
                <span>Edit Report: {{ $report->audit->audit_code }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Update report contents, executive summary, and revise addressed comments</p>
        </div>
        <a href="{{ route('reports.show', $report) }}" class="text-sm text-gray-500 hover:text-[#004ea1] flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Report Workflow
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('reports.update', $report) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Status Banner --}}
            <div class="p-4 rounded-xl border flex items-center justify-between {{ $report->status === 'returned_for_revision' ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-blue-50 border-blue-200 text-blue-900' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas {{ $report->status === 'returned_for_revision' ? 'fa-exclamation-triangle text-amber-600' : 'fa-info-circle text-[#004ea1]' }}"></i>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider">Current Stage: {{ $report->status_label }}</p>
                        @if($report->status === 'returned_for_revision')
                            <p class="text-xs mt-0.5">Please address the reviewer comments below before resubmitting.</p>
                        @endif
                    </div>
                </div>
                <span class="text-xs font-mono font-bold">{{ $report->audit->audit_code }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Report Title --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Report Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $report->title ?? ('Audit Report: ' . $report->audit->title)) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Draft Issue Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Draft Issue Date
                    </label>
                    <input type="date" name="draft_issue_date" value="{{ old('draft_issue_date', $report->draft_issue_date ? $report->draft_issue_date->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    @error('draft_issue_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @if($report->status === 'final_issued' || $report->chief_approver_id)
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Final Issue Date
                </label>
                <input type="date" name="final_issue_date" value="{{ old('final_issue_date', $report->final_issue_date ? $report->final_issue_date->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
            </div>
            @endif

            {{-- Executive Summary --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Executive Summary & Key Findings
                </label>
                <textarea name="executive_summary" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('executive_summary', $report->executive_summary) }}</textarea>
            </div>

            {{-- Scope & Methodology --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Scope, Limitations & Methodology
                </label>
                <textarea name="scope" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('scope', $report->scope) }}</textarea>
            </div>

            {{-- Transmittal Comments --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Comments / Notes on Corrections
                </label>
                <textarea name="comments" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">{{ old('comments', $report->comments) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-[#004ea1] text-white text-sm font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Update Report</span>
                </button>
                <a href="{{ route('reports.show', $report) }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
