@extends('layouts.app')

@section('title', 'Audit Fieldwork Registry')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center text-[#004ea1]">
                <i class="fas fa-file-signature text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Working Paper Registry</h2>
                <p class="text-sm text-slate-500">Centralized repository for all audit fieldwork and evidence.</p>
            </div>
        </div>
        @can('edit audits')
        <a href="{{ route('working-papers.create') }}" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[#001c40] transition-all">
            <i class="fas fa-plus mr-2"></i> Record Fieldwork
        </a>
        @endcan
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200 flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px] relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" placeholder="Search by paper title or audit code..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all">
        </div>
        <select class="px-4 py-2 bg-slate-50 border-none rounded-xl text-sm text-slate-600 focus:ring-2 focus:ring-[#004ea1]">
            <option>All Statuses</option>
            <option>Draft</option>
            <option>Pending Review</option>
            <option>Approved</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Working Paper</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Audit Engagement</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Created By</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($workingPapers as $wp)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white shadow-sm border border-slate-200 flex items-center justify-center text-[#004ea1]">
                                <i class="fas fa-file-alt text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ $wp->title }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">v{{ $wp->version }} &middot; {{ $wp->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-[#004ea1]">{{ $wp->audit->audit_code }}</span>
                            <span class="text-xs text-slate-600 truncate max-w-[200px]">{{ $wp->audit->title }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-[#004ea1]/10 flex items-center justify-center text-[10px] font-bold text-[#004ea1]">
                                {{ substr($wp->creator->name, 0, 1) }}
                            </div>
                            <span class="text-xs font-medium text-slate-600">{{ $wp->creator->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $wp->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $wp->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('working-papers.show', $wp) }}" class="p-2 text-slate-400 hover:text-[#004ea1] transition-colors"><i class="fas fa-eye"></i></a>
                            @can('edit audits')
                            <a href="{{ route('working-papers.edit', $wp) }}" class="p-2 text-slate-400 hover:text-[#ffcc00] transition-colors"><i class="fas fa-edit"></i></a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <p class="text-sm text-slate-400 italic">No working papers found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $workingPapers->links() }}
    </div>
</div>
@endsection
