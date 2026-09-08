@extends('layouts.app')
@section('title', 'Report Details')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-[#333]">Report for {{ $report->audit->audit_code }}</h2>
                <p class="text-sm text-gray-500 mt-1">Status: <span class="font-bold">{{ ucwords(str_replace('_', ' ', $report->status)) }}</span></p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-[#004ea1] hover:underline text-sm"><i class="fas fa-arrow-left mr-1"></i>Back to Reports</a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-t border-b border-gray-100 mb-6">
            <div><p class="text-xs text-gray-500">Prepared By</p><p class="text-sm font-medium">{{ $report->preparer->name }}</p></div>
            <div><p class="text-xs text-gray-500">Draft Issue Date</p><p class="text-sm font-medium">{{ $report->draft_issue_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Senior Reviewer</p><p class="text-sm font-medium">{{ $report->seniorReviewer?->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Chief Approver</p><p class="text-sm font-medium">{{ $report->chiefApprover?->name ?? '—' }}</p></div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-sm font-bold text-gray-700 mb-2">Comments & Notes</h3>
            <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">{{ $report->comments ?? 'No comments provided.' }}</div>
        </div>

        <div class="flex flex-wrap gap-3">
            @if($report->status === 'draft')
            <form method="POST" action="{{ route('reports.submit-senior', $report) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"><i class="fas fa-paper-plane mr-2"></i>Submit for Senior Review</button>
            </form>
            @endif

            @if($report->status === 'pending_senior_review')
            <form method="POST" action="{{ route('reports.submit-chief', $report) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700"><i class="fas fa-check-double mr-2"></i>Approve & Forward to Chief</button>
            </form>
            @endif

            @if($report->status === 'pending_chief_approval')
            <form method="POST" action="{{ route('reports.issue', $report) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700"><i class="fas fa-check-circle mr-2"></i>Issue Final Report</button>
            </form>
            @endif

            @if(in_array($report->status, ['pending_senior_review', 'pending_chief_approval']))
            <form method="POST" action="{{ route('reports.reject', $report) }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="comments" placeholder="Reason for rejection..." required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] w-64">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700"><i class="fas fa-times-circle mr-2"></i>Reject</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
