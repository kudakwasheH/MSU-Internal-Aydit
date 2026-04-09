@extends('layouts.app')
@section('title', 'Audits')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-[#333]">Audit Engagements</h2>
        <p class="text-sm text-gray-500">Manage audit plans and track progress</p>
    </div>
    <a href="{{ route('audits.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition">
        <i class="fas fa-plus mr-2"></i>New Audit
    </a>
</div>
{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Code or title..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                <option value="">All Status</option>
                @foreach(['draft','planned','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Type</label>
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                <option value="">All Types</option>
                @foreach(['internal','external','it','compliance','performance'] as $t)
                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Priority</label>
            <select name="priority" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                <option value="">All Priorities</option>
                @foreach(['high','medium','low'] as $p)
                <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#333] text-white text-sm rounded-lg hover:bg-[#555] transition"><i class="fas fa-filter mr-1"></i>Filter</button>
        <a href="{{ route('audits.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">Clear</a>
    </form>
</div>
{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($audits as $audit)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-[#004ea1] font-semibold">{{ $audit->audit_code }}</td>
                <td class="px-5 py-3"><a href="{{ route('audits.show', $audit) }}" class="text-sm font-medium text-gray-800 hover:text-[#004ea1]">{{ Str::limit($audit->title, 35) }}</a></td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">{{ ucfirst($audit->audit_type) }}</span></td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $audit->priority == 'high' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $audit->priority == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $audit->priority == 'low' ? 'bg-green-100 text-green-700' : '' }}">{{ ucfirst($audit->priority) }}</span>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $audit->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $audit->status == 'planned' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $audit->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $audit->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $audit->status == 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">{{ ucwords(str_replace('_', ' ', $audit->status)) }}</span>
                </td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $audit->planned_start_date->format('d M') }} - {{ $audit->planned_end_date->format('d M Y') }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('audits.show', $audit) }}" class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('audits.edit', $audit) }}" class="text-[#ffcc00] hover:text-yellow-600" title="Edit"><i class="fas fa-edit"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No audits found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 border-t border-gray-100">{{ $audits->withQueryString()->links() }}</div>
</div>
@endsection

