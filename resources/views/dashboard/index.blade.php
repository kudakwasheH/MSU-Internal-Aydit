@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
{{-- Metric Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Audit Plan Status</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $totalAudits }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> {{ $completedAudits }} Finalized</p>
            </div>
            <div class="w-12 h-12 bg-[#004ea1]/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-clipboard-check text-[#004ea1] text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#ffcc00]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">University Staff</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $activeUsers }} Active Accounts</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-[#ffcc00] text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Risk Heat Index</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $highRiskFindings }}</p>
                <p class="text-xs text-red-500 mt-1">High/Critical Findings</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-fire text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Council Reports</p>
                <p class="text-3xl font-bold text-[#333] mt-1">{{ $completedAudits }}</p>
                <p class="text-xs text-gray-400 mt-1">Ready for Issuance</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-pdf text-orange-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-4 gap-5 mb-8">
    <div class="bg-gradient-to-br from-[#004ea1] to-[#001533] rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em]">Risk Coverage</p>
                <p class="text-3xl font-black mt-1">{{ $riskCoverage }}%</p>
            </div>
            <i class="fas fa-shield-halved text-3xl text-white/20"></i>
        </div>
        <div class="mt-4 bg-white/10 rounded-full h-1.5 overflow-hidden">
            <div class="bg-[#ffcc00] h-full rounded-full shadow-[0_0_10px_rgba(255,204,0,0.5)]" style="width: {{ $riskCoverage }}%"></div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Recurring Issues</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $recurringFindings }}</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500">
                <i class="fas fa-redo-alt text-xl"></i>
            </div>
        </div>
        <p class="text-[10px] text-orange-600 font-bold mt-2 uppercase tracking-tighter">Requires Management Focus</p>
    </div>

    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">System Health</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-slate-800">{{ $systemHealth['uptime'] }}</p>
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-tighter">{{ $systemHealth['health'] }}</span>
                </div>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-500">
                <i class="fas fa-server text-xl"></i>
            </div>
        </div>
        <p class="text-[10px] text-slate-400 font-medium mt-2">Latency: <span class="text-slate-700 font-bold">{{ $systemHealth['responseTime'] }}</span></p>
    </div>

    <div class="bg-slate-800 rounded-xl p-5 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-10">
            <i class="fas fa-stopwatch text-6xl"></i>
        </div>
        <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Avg Cycle Time</p>
        <p class="text-3xl font-black mt-1">{{ $cycleTime }} <span class="text-xs font-normal opacity-50">days</span></p>
        <p class="text-[10px] text-[#ffcc00] font-bold mt-2 uppercase tracking-tighter">Target: 21 Days</p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Findings by Severity</h3>
        <canvas id="findingsSeverityChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Audits by Status</h3>
        <canvas id="auditsStatusChart" height="200"></canvas>
    </div>
</div>

{{-- Tables Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Recent Findings</h3>
            <a href="{{ route('findings.index') }}" class="text-xs text-[#004ea1] hover:underline">View All &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentFindings as $finding)
            <a href="{{ route('findings.show', $finding) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                <div>
                    <p class="text-sm font-medium text-gray-800 truncate max-w-[250px]">{{ $finding->title }}</p>
                    <p class="text-xs text-gray-400">{{ $finding->audit?->audit_code }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    {{ $finding->severity === 'critical' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $finding->severity === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $finding->severity === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $finding->severity === 'low' ? 'bg-green-100 text-green-700' : '' }}">
                    {{ ucfirst($finding->severity) }}
                </span>
            </a>
            @empty
            <p class="px-6 py-4 text-sm text-gray-400">No findings yet.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Overdue Action Items</h3>
            <a href="{{ route('action-items.index') }}" class="text-xs text-[#004ea1] hover:underline">View All &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($overdueActionsList as $action)
            <a href="{{ route('action-items.show', $action) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                <div>
                    <p class="text-sm font-medium text-gray-800 truncate max-w-[250px]">{{ Str::limit($action->action_description, 50) }}</p>
                    <p class="text-xs text-gray-400">{{ $action->assignee?->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                    Due {{ $action->due_date->format('d M') }}
                </span>
            </a>
            @empty
            <p class="px-6 py-4 text-sm text-gray-400">No overdue actions.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const severityData = @json($findingsBySeverity);
    new Chart(document.getElementById('findingsSeverityChart'), {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [severityData.critical || 0, severityData.high || 0, severityData.medium || 0, severityData.low || 0],
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745'],
                borderWidth: 0,
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    const statusData = @json($auditsByStatus);
    new Chart(document.getElementById('auditsStatusChart'), {
        type: 'bar',
        data: {
            labels: ['Draft', 'Planned', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Audits',
                data: [statusData.draft || 0, statusData.planned || 0, statusData.in_progress || 0, statusData.completed || 0, statusData.cancelled || 0],
                backgroundColor: ['#9ca3af', '#3b82f6', '#f59e0b', '#28a745', '#dc3545'],
                borderRadius: 6,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
});
</script>
@endpush
@endsection

