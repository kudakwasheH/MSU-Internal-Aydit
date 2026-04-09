@extends('layouts.app')
@section('title', 'Audit Committee Dashboard')
@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1]">
        <p class="text-xs font-medium text-gray-500 uppercase">Completion Rate</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $auditCompletionRate }}%</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#ffcc00]">
        <p class="text-xs font-medium text-gray-500 uppercase">Risk Coverage</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $riskCoverage }}%</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-xs font-medium text-gray-500 uppercase">High-Risk Findings</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $highRiskFindings }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest">Recurring Findings</p>
        <div class="flex items-baseline gap-2">
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $recurringFindings }}</p>
            <span class="text-[10px] font-bold text-orange-600 uppercase tracking-tighter">Systemic Issues</span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1">Requires Policy Review</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest">Plan Execution</p>
        <p class="text-3xl font-black text-slate-800 mt-1">{{ $completedAudits }} / {{ $totalAudits }}</p>
        <p class="text-[10px] text-green-600 font-bold uppercase tracking-tighter">{{ $auditCompletionRate }}% Progress</p>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Aging Analysis --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-6 font-bold uppercase tracking-wider">Findings Aging Analysis</h3>
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <span class="w-24 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">> 180 Days</span>
                <div class="flex-1 bg-gray-100 h-4 rounded-full overflow-hidden">
                    <div class="bg-red-600 h-full rounded-full" style="width: 15%"></div>
                </div>
                <span class="text-xs font-bold text-red-600">3 Cases</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="w-24 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">90-180 Days</span>
                <div class="flex-1 bg-gray-100 h-4 rounded-full overflow-hidden">
                    <div class="bg-orange-500 h-full rounded-full" style="width: 25%"></div>
                </div>
                <span class="text-xs font-bold text-orange-600">5 Cases</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="w-24 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">30-90 Days</span>
                <div class="flex-1 bg-gray-100 h-4 rounded-full overflow-hidden">
                    <div class="bg-yellow-400 h-full rounded-full" style="width: 40%"></div>
                </div>
                <span class="text-xs font-bold text-yellow-600">8 Cases</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="w-24 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">< 30 Days</span>
                <div class="flex-1 bg-gray-100 h-4 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full rounded-full" style="width: 20%"></div>
                </div>
                <span class="text-xs font-bold text-green-600">4 Cases</span>
            </div>
        </div>
        <div class="mt-8 p-4 bg-red-50 border border-red-100 rounded-lg shadow-inner">
            <p class="text-xs text-red-800 font-medium">
                <i class="fas fa-bullhorn mr-1"></i>
                Attention: 3 critical findings in Finance/Procurement have exceeded 180 days.
            </p>
        </div>
    </div>

    {{-- Meeting Prep --}}
    <div class="bg-white rounded-xl shadow-sm p-6 text-white overflow-hidden relative" style="background-color: #004ea1;">
        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <h3 class="text-sm font-bold uppercase tracking-widest text-[#ffcc00] mb-6">Upcoming Committee Meeting</h3>
        
        <div class="flex items-center gap-6 mb-8 relative z-10">
            <div class="text-center">
                <p class="text-[10px] font-bold uppercase text-[#ffcc00]/70">Days Until</p>
                <p class="text-5xl font-black">12</p>
            </div>
            <div class="h-12 w-px bg-white/20"></div>
            <div>
                <p class="text-sm font-bold">Q1 Audit Review Session</p>
                <p class="text-xs text-white/70">15 May 2026 &middot; Senate Room</p>
            </div>
        </div>

        <h4 class="text-[10px] font-bold uppercase text-[#ffcc00]/70 mb-3 tracking-widest">Meeting Pack & Governance Tools</h4>
        <div class="space-y-2 relative z-10">
            <a href="{{ route('reports.meeting-pack') }}" class="w-full flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-sm group">
                <span class="flex items-center gap-3"><i class="fas fa-chart-line text-[#ffcc00] group-hover:scale-110 transition-transform"></i> Strategic Meeting Pack</span>
                <i class="fas fa-external-link-alt opacity-50 text-xs"></i>
            </a>
            <a href="{{ route('reports.rolling-plan') }}" class="w-full flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-sm group">
                <span class="flex items-center gap-3"><i class="fas fa-calendar-check text-blue-300 group-hover:scale-110 transition-transform"></i> Rolling Audit Plan</span>
                <i class="fas fa-calendar-alt opacity-50 text-xs"></i>
            </a>
            <a href="{{ route('reports.generate') }}" class="w-full flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-sm group border border-white/10 mt-4">
                <span class="flex items-center gap-3"><i class="fas fa-file-pdf text-red-400"></i> Standard Reports</span>
                <i class="fas fa-download opacity-50 text-xs"></i>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 font-bold uppercase tracking-wider">Findings by Severity</h3>
        <canvas id="committeeFindingsChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 font-bold uppercase tracking-wider">Escalation Levels</h3>
        <canvas id="escalationChart" height="200"></canvas>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Recent Escalations</h3></div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Finding</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Escalated To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($recentEscalations as $esc)
            <tr>
                <td class="px-6 py-3">{{ $esc->finding?->title }}</td>
                <td class="px-6 py-3">{{ $esc->escalatedToUser?->name }}</td>
                <td class="px-6 py-3">Level {{ $esc->escalated_to_level }}</td>
                <td class="px-6 py-3"><span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">{{ ucfirst($esc->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-4 text-gray-400">No escalations.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sev = @json($findingsBySeverity);
    new Chart(document.getElementById('committeeFindingsChart'), {
        type: 'pie', data: { labels: ['Critical','High','Medium','Low'], datasets: [{ data: [sev.critical||0,sev.high||0,sev.medium||0,sev.low||0], backgroundColor: ['#dc3545','#fd7e14','#ffc107','#28a745'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    const esc = @json($escalationStatus);
    new Chart(document.getElementById('escalationChart'), {
        type: 'bar', data: { labels: ['Level 1','Level 2','Level 3'], datasets: [{ label: 'Findings', data: [esc.level1,esc.level2,esc.level3], backgroundColor: ['#ffc107','#fd7e14','#dc3545'], borderRadius: 6 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
});
</script>
@endpush
@endsection

