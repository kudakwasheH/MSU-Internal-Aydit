@extends('layouts.app')
@section('title', 'Reports Workflow Dashboard')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#333]"><i class="fas fa-file-invoice text-[#004ea1] mr-2"></i>Reports Workflow</h2>
            <p class="text-sm text-gray-500 mt-1">Manage and track report reviews and approvals.</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/50 text-gray-500 font-medium">
                <tr>
                    <th class="px-6 py-4">Audit</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Draft Issue Date</th>
                    <th class="px-6 py-4">Final Issue Date</th>
                    <th class="px-6 py-4">Prepared By</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium text-[#004ea1]"><a href="{{ route('audits.show', $report->audit) }}">{{ $report->audit->audit_code }}</a></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-600">{{ ucwords(str_replace('_', ' ', $report->status)) }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $report->draft_issue_date ? $report->draft_issue_date->format('d M Y') : '—' }}</td>
                    <td class="px-6 py-4">{{ $report->final_issue_date ? $report->final_issue_date->format('d M Y') : '—' }}</td>
                    <td class="px-6 py-4">{{ $report->preparer->name }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('reports.show', $report) }}" class="text-[#004ea1] hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No reports found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $reports->links() }}
</div>
@endsection
