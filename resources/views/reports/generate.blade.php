@extends('layouts.app')
@section('title', 'Reports & Analytics')
@section('content')
<div class="mb-4">
    <h2 class="text-xl font-black text-slate-800 uppercase tracking-[0.2em]">Reports & Analytics</h2>
    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Enterprise Performance Snapshot & Strategic Distribution</p>
</div>

@include('reports._tabs')
<div x-data="{ activeTab: null }" class="space-y-6 mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Total Holdings --}}
        <button @click="activeTab = (activeTab === 'holdings' ? null : 'holdings')"
            :class="activeTab === 'holdings' ? 'ring-2 ring-[#004ea1] shadow-lg' : ''"
            class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1] text-left group hover:shadow-md transition-all active:scale-95">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Holdings</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $totalFindings }}</p>
            <p class="text-[10px] text-green-600 font-bold mt-1 uppercase tracking-tighter"><i class="fas fa-check-double mr-1"></i> {{ $resolvedFindings }} Resolved</p>
        </button>

        {{-- Overdue Actions --}}
        <button @click="activeTab = (activeTab === 'overdue' ? null : 'overdue')"
            :class="activeTab === 'overdue' ? 'ring-2 ring-orange-500 shadow-lg' : ''"
            class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500 text-left group hover:shadow-md transition-all active:scale-95">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Overdue Actions</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $overdueActions }}</p>
            <p class="text-[10px] text-orange-600 font-bold mt-1 uppercase tracking-tighter"><i class="fas fa-clock mr-1"></i> Immediate Response Needed</p>
        </button>

        {{-- Risk Distribution --}}
        <button @click="activeTab = (activeTab === 'risks' ? null : 'risks')"
            :class="activeTab === 'risks' ? 'ring-2 ring-[#ffcc00] shadow-lg' : ''"
            class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#ffcc00] text-left group hover:shadow-md transition-all active:scale-95">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Risk Distribution</p>
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($risksByCategory as $cat => $count)
                    <span class="text-[9px] font-black bg-slate-50 border border-slate-100 px-2 py-1 rounded-lg text-slate-600 uppercase tracking-tighter">
                        {{ ucfirst($cat) }}: <span class="text-[#004ea1]">{{ $count }}</span>
                    </span>
                @endforeach
            </div>
        </button>

        {{-- Audit Status --}}
        <button @click="activeTab = (activeTab === 'status' ? null : 'status')"
            :class="activeTab === 'status' ? 'ring-2 ring-green-500 shadow-lg' : ''"
            class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 text-left group hover:shadow-md transition-all active:scale-95">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Audit Status</p>
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($auditsByStatus as $s => $c)
                    <span class="text-[9px] font-black bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-lg text-slate-600 uppercase tracking-tighter">
                        {{ ucwords(str_replace('_',' ',$s)) }}: <span class="text-green-600">{{ $c }}</span>
                    </span>
                @endforeach
            </div>
        </button>
    </div>

    {{-- Detail Panes --}}
    <div x-show="activeTab === 'holdings'" x-collapse x-cloak class="bg-[#004ea1] rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
        <h3 class="text-[10px] font-black text-[#ffcc00] uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
            <i class="fas fa-chart-bar"></i> Audit Volume Breakdown by Type
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($auditsByType as $type => $count)
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/10">
                    <p class="text-[9px] font-black text-white/50 uppercase tracking-widest mb-1">{{ $type }} Audits</p>
                    <p class="text-2xl font-black">{{ $count }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div x-show="activeTab === 'overdue'" x-collapse x-cloak class="bg-slate-900 rounded-2xl p-8 text-white">
        <h3 class="text-[10px] font-black text-red-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i> Top Overdue Remediation Actions
        </h3>
        <div class="space-y-3">
            @forelse($overdueActionsList as $action)
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight">{{ $action->title }}</p>
                            <p class="text-[9px] text-white/40 uppercase font-bold mt-1">Audit: {{ $action->finding->audit->audit_code }} &middot; Owner: {{ $action->assignee->name }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black text-red-400 bg-red-400/10 px-3 py-1 rounded-lg border border-red-400/20">Overdue</span>
                </div>
            @empty
                <div class="py-8 text-center bg-white/5 rounded-xl border border-dashed border-white/20">
                    <p class="text-[10px] text-white/30 uppercase font-bold tracking-widest">No priority overdue actions found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Findings by Severity</h3>
        <canvas id="reportFindingsChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Risks by Category</h3>
        <canvas id="reportRisksChart" height="200"></canvas>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Download Audit Reports</h3></div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Findings</th>
            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Download</th>
        </tr></thead>
        <tbody class="divide-y">
            @foreach($audits as $audit)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-[#004ea1]">{{ $audit->audit_code }}</td>
                <td class="px-5 py-3 text-sm">{{ $audit->title }}</td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs bg-gray-100 rounded-full">{{ ucwords(str_replace('_',' ',$audit->status)) }}</span></td>
                <td class="px-5 py-3 text-sm">{{ $audit->findings->count() }}</td>
                <td class="px-5 py-3"><a href="{{ route('reports.download', $audit) }}" class="px-3 py-1 bg-[#004ea1] text-white text-xs rounded-lg hover:bg-[#001533]"><i class="fas fa-file-pdf mr-1"></i>PDF</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sev = @json($findingsBySeverity);
    new Chart(document.getElementById('reportFindingsChart'), { type: 'doughnut', data: { labels: ['Critical','High','Medium','Low'], datasets: [{ data: [sev.critical||0,sev.high||0,sev.medium||0,sev.low||0], backgroundColor: ['#dc3545','#fd7e14','#ffc107','#28a745'] }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
    const cats = @json($risksByCategory);
    new Chart(document.getElementById('reportRisksChart'), { type: 'bar', data: { labels: Object.keys(cats).map(c=>c.charAt(0).toUpperCase()+c.slice(1)), datasets: [{ data: Object.values(cats), backgroundColor: ['#004ea1','#ffcc00','#333','#28a745','#3b82f6'], borderRadius: 6 }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } } });
});
</script>
@endpush
@endsection

