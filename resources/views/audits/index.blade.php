@extends('layouts.app')
@section('title', 'Audit Universe')
@section('content')
<div x-data="{ assignModalOpen: false, selectedAudit: null, selectedMembers: [] }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#004ea1]"></span>
                <h2 class="text-xl font-bold text-[#333]">Audit Universe</h2>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">Manage audit engagements, planned timelines, and allocated audit team members</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('audits.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>New Audit Engagement</span>
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Search Engagements</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Code or title..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                    <option value="">All Status</option>
                    @foreach(['draft','planned','in_progress','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Type</label>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                    <option value="">All Types</option>
                    @foreach(['internal','external','it','compliance','performance'] as $t)
                    <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
                    <option value="">All Priorities</option>
                    @foreach(['high','medium','low'] as $p)
                    <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-semibold rounded-lg hover:bg-[#001533] transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-filter text-xs"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'type', 'priority']))
            <a href="{{ route('audits.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5">Code</th>
                        <th class="px-5 py-3.5">Title & Type</th>
                        <th class="px-5 py-3.5">Allocated Audit Team</th>
                        <th class="px-5 py-3.5">Priority</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Planned Period</th>
                        <th class="px-5 py-3.5">Report Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($audits as $audit)
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="px-5 py-4 font-mono text-xs text-[#004ea1] font-bold">
                            <a href="{{ route('audits.show', $audit) }}" class="hover:underline">{{ $audit->audit_code }}</a>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col max-w-xs">
                                <a href="{{ route('audits.show', $audit) }}" class="text-sm font-semibold text-gray-800 hover:text-[#004ea1] truncate">{{ $audit->title }}</a>
                                <span class="text-[10px] text-gray-400 capitalize">{{ $audit->audit_type }} Audit</span>
                            </div>
                        </td>

                        {{-- Allocated Audit Team Members Display --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if($audit->teamMembers->count())
                                    <div class="flex -space-x-2 overflow-hidden">
                                        @foreach($audit->teamMembers->take(3) as $member)
                                            <div class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-[#004ea1] text-white text-xs font-bold flex items-center justify-center shadow-xs" title="{{ $member->name }} — {{ $member->position }}">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-700 leading-tight">
                                            {{ $audit->teamMembers->first()->name }}
                                            @if($audit->teamMembers->count() > 1)
                                                <span class="text-gray-400 text-[10px] font-normal">+{{ $audit->teamMembers->count() - 1 }} more</span>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-400 leading-tight">{{ $audit->teamMembers->count() }} member(s) assigned</span>
                                    </div>
                                @else
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded font-medium italic">Unassigned</span>
                                @endif

                                {{-- Quick Team Assignment Trigger Button --}}
                                <button type="button" 
                                    @click="selectedAudit = {{ json_encode(['id' => $audit->id, 'code' => $audit->audit_code, 'title' => $audit->title]) }}; selectedMembers = {{ json_encode($audit->teamMembers->pluck('id')) }}; assignModalOpen = true"
                                    class="p-1 text-gray-400 hover:text-[#004ea1] hover:bg-gray-100 rounded transition ml-1" 
                                    title="Assign or update audit team">
                                    <i class="fas fa-user-plus text-xs"></i>
                                </button>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 text-xs rounded-full font-bold
                                {{ $audit->priority == 'high' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                {{ $audit->priority == 'medium' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : '' }}
                                {{ $audit->priority == 'low' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}">
                                {{ ucfirst($audit->priority) }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 text-xs rounded-full font-bold
                                {{ $audit->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                                {{ $audit->status == 'planned' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                {{ $audit->status == 'in_progress' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                {{ $audit->status == 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ $audit->status == 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}">
                                {{ ucwords(str_replace('_', ' ', $audit->status)) }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-xs text-gray-500">
                            {{ $audit->planned_start_date->format('d M') }} - {{ $audit->planned_end_date->format('d M Y') }}
                        </td>

                        <td class="px-5 py-4">
                            @if($audit->report)
                                <a href="{{ route('reports.show', $audit->report) }}" class="text-xs font-semibold text-[#004ea1] hover:underline flex items-center gap-1">
                                    <i class="fas fa-file-invoice"></i> {{ $audit->report->status_label }}
                                </a>
                            @else
                                <a href="{{ route('reports.create', ['audit_id' => $audit->id]) }}" class="text-xs text-gray-400 hover:text-[#004ea1] italic">
                                    + Draft Report
                                </a>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('audits.show', $audit) }}" class="p-1.5 text-blue-600 hover:text-blue-800 rounded-lg hover:bg-blue-50 transition" title="View Audit Details"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('audits.edit', $audit) }}" class="p-1.5 text-[#ffcc00] hover:text-yellow-600 rounded-lg hover:bg-yellow-50 transition" title="Edit Audit"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('reports.download', $audit) }}" class="p-1.5 text-red-600 hover:text-red-800 rounded-lg hover:bg-red-50 transition" title="Download Report PDF"><i class="fas fa-file-pdf"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">No audit engagements found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $audits->withQueryString()->links() }}</div>
    </div>

    {{-- Assign Team Members Quick Modal --}}
    <div x-show="assignModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/50" @click="assignModalOpen = false"></div>

            <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-users-cog text-[#004ea1]"></i>
                            <span>Assign Audit Team Members</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="selectedAudit ? selectedAudit.code + ' — ' + selectedAudit.title : ''"></p>
                    </div>
                    <button type="button" @click="assignModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="'/audits/' + (selectedAudit ? selectedAudit.id : '') + '/assign-team'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Auditors Allocated to this Engagement</label>
                        <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto border rounded-xl p-3 bg-gray-50/50">
                            @foreach($allUsers ?? [] as $user)
                            <label class="flex items-start gap-3 p-2 rounded-lg bg-white border border-gray-200 hover:border-[#004ea1] cursor-pointer transition">
                                <input type="checkbox" name="team_members[]" value="{{ $user->id }}" :checked="selectedMembers.includes({{ $user->id }})" class="mt-0.5 rounded text-[#004ea1] focus:ring-[#004ea1]">
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $user->position }} &middot; {{ $user->department }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="assignModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
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
