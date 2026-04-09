@extends('layouts.app')
@section('title', 'Findings')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h2 class="text-xl font-bold text-[#333]">Audit Findings</h2><p class="text-sm text-gray-500">Track and manage audit findings</p></div>
    <a href="{{ route('findings.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition"><i class="fas fa-plus mr-2"></i>New Finding</a>
</div>
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full px-3 py-2 border rounded-lg text-sm"></div>
        <select name="severity" class="px-3 py-2 border rounded-lg text-sm"><option value="">All Severity</option>@foreach(['critical','high','medium','low'] as $s)<option value="{{ $s }}" {{ request('severity') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select>
        <select name="status" class="px-3 py-2 border rounded-lg text-sm"><option value="">All Status</option>@foreach(['open','in_progress','resolved','closed'] as $s)<option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach</select>
        <button type="submit" class="px-4 py-2 bg-[#333] text-white text-sm rounded-lg"><i class="fas fa-filter mr-1"></i>Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Audit</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Escalation</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($findings as $f)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3"><a href="{{ route('findings.show', $f) }}" class="font-medium text-gray-800 hover:text-[#004ea1]">{{ Str::limit($f->title, 30) }}</a></td>
                <td class="px-5 py-3 font-mono text-xs text-[#004ea1]">{{ $f->audit?->audit_code }}</td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $f->severity == 'critical' ? 'bg-red-100 text-red-700' : ($f->severity == 'high' ? 'bg-orange-100 text-orange-700' : ($f->severity == 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">{{ ucfirst($f->severity) }}</span></td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ ucwords(str_replace('_', ' ', $f->status)) }}</span></td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $f->assignee?->name ?? '—' }}</td>
                <td class="px-5 py-3"><span class="text-xs font-medium {{ $f->escalation_level > 1 ? 'text-red-600' : 'text-gray-400' }}">Level {{ $f->escalation_level }}</span></td>
                <td class="px-5 py-3">
                    <a href="{{ route('findings.show', $f) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('findings.edit', $f) }}" class="text-[#ffcc00]"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No findings found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 border-t">{{ $findings->withQueryString()->links() }}</div>
</div>
@endsection

