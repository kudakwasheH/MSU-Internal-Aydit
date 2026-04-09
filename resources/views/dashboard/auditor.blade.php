@extends('layouts.app')
@section('title', 'Auditor Workspace')
@section('content')

{{-- Auditor Metric Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">My Audits</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $myAudits->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-briefcase text-[#004ea1] text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#ffcc00]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">My Findings</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $myFindings }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-signature text-[#ffcc00] text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Action Items</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $myActions }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-tasks text-orange-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Control Templates</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $templatesCount }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Main Workspace --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- My Active Audits --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-sm font-semibold text-gray-700">My Assigned Audits</h3>
                <button class="text-xs text-[#004ea1] hover:underline">View All &rarr;</button>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($myAudits as $audit)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs font-bold text-[#004ea1] mb-1 font-mono tracking-tight">{{ $audit->audit_code }}</p>
                            <h4 class="text-base font-bold text-[#333]">{{ $audit->title }}</h4>
                        </div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $audit->status_color }}-100 text-{{ $audit->status_color }}-700 uppercase tracking-wider">
                            {{ ucfirst($audit->status) }}
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-between gap-6 text-sm text-gray-500">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center"><i class="fas fa-calendar-alt mr-2 text-gray-400"></i> Due: {{ $audit->planned_end_date?->format('d M Y') ?? 'N/A' }}</span>
                            <span class="flex items-center"><i class="fas fa-layer-group mr-2 text-gray-400"></i> {{ ucfirst($audit->audit_type) }}</span>
                        </div>
                        <div class="flex-1 max-w-[200px]">
                            <div class="flex justify-between text-[10px] mb-1 font-bold text-gray-400 uppercase tracking-widest">
                                <span>Progress</span>
                                <span>65%</span> {{-- Logic placeholder --}}
                            </div>
                            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#ffcc00] h-full rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        <a href="{{ route('audits.show', $audit) }}" class="p-2 text-[#004ea1] hover:bg-[#004ea1]/5 rounded-lg transition">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-400">
                    <i class="fas fa-folder-open text-4xl mb-4 opacity-20"></i>
                    <p>No active audits assigned to you.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Tasks Checklist --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-6">Tasks Due This Week</h3>
            <div class="space-y-3">
                @forelse($tasksDueThisWeek as $task)
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 group">
                    <div class="mt-0.5">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#004ea1] focus:ring-[#004ea1]">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">{{ Str::limit($task->action_description, 100) }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] font-bold text-red-500 uppercase">Due: {{ $task->due_date->format('l, d M') }}</span>
                            <span class="text-[10px] font-medium text-gray-400">&bull; {{ $task->finding?->audit?->audit_code }}</span>
                        </div>
                    </div>
                    <button class="text-gray-300 group-hover:text-gray-500 transition"><i class="fas fa-ellipsis-v"></i></button>
                </div>
                @empty
                <p class="text-center py-6 text-sm text-gray-400">No urgent tasks due this week. Good job!</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Side Panel --}}
    <div class="space-y-6">
        {{-- Quick Actions --}}
        <div class="bg-[#004ea1] rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <h3 class="text-sm font-bold uppercase tracking-widest text-[#ffcc00] mb-6">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-3 relative z-10">
                <button class="flex items-center gap-3 w-full bg-white/10 hover:bg-white/20 p-3 rounded-lg transition text-left text-sm font-medium">
                    <div class="w-8 h-8 bg-[#ffcc00] rounded-lg flex items-center justify-center text-[#004ea1] shadow-sm">
                        <i class="fas fa-plus"></i>
                    </div>
                    Create Raw Finding
                </button>
                <button class="flex items-center gap-3 w-full bg-white/10 hover:bg-white/20 p-3 rounded-lg transition text-left text-sm font-medium">
                    <div class="w-8 h-8 bg-green-400 rounded-lg flex items-center justify-center text-[#004ea1] shadow-sm">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    Upload Evidence
                </button>
                <button class="flex items-center gap-3 w-full bg-white/10 hover:bg-white/20 p-3 rounded-lg transition text-left text-sm font-medium">
                    <div class="w-8 h-8 bg-purple-400 rounded-lg flex items-center justify-center text-[#004ea1] shadow-sm">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    Templates Repository
                </button>
                <button class="flex items-center gap-3 w-full bg-white/10 hover:bg-white/20 p-3 rounded-lg transition text-left text-sm font-medium">
                    <div class="w-8 h-8 bg-cyan-400 rounded-lg flex items-center justify-center text-[#004ea1] shadow-sm">
                        <i class="fas fa-microchip"></i>
                    </div>
                    IT / System Review
                </button>
            </div>
        </div>

        {{-- Recent Findings --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-semibold text-gray-700">My Recent Findings</h3>
            </div>
            <div class="divide-y divide-gray-50 text-sm">
                @forelse($recentFindings as $finding)
                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <p class="font-bold text-gray-800 truncate mb-1">{{ $finding->title }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $finding->audit?->audit_code }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-{{ $finding->severity == 'high' ? 'red' : 'yellow' }}-100 text-{{ $finding->severity == 'high' ? 'red' : 'yellow' }}-700 capitalize">
                            {{ $finding->severity }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="px-6 py-4 text-xs text-gray-400 text-center">No findings created yet.</p>
                @endforelse
            </div>
            <a href="{{ route('findings.index', ['assigned_to' => Auth::id()]) }}" class="block w-full py-3 text-center text-xs font-bold text-[#004ea1] hover:bg-gray-50 border-t border-gray-50">View All My Findings</a>
        </div>

        {{-- Productivity Tip --}}
        <div class="bg-amber-50 rounded-xl p-5 border border-amber-200">
            <div class="flex gap-3">
                <i class="fas fa-lightbulb text-amber-500 mt-1"></i>
                <div class="text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold mb-1 uppercase tracking-wider">MSU Auditor Tip</p>
                    Always ensure your workpapers are cross-referenced to specific findings before submitting for manager review. This reduces turnaround time by 30%.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
