@extends('layouts.app')
@section('title', 'Executive Dashboard')
@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004ea1]">
        <p class="text-xs font-medium text-gray-500 uppercase">Strategic Risks</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $strategicRisks }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-xs font-medium text-gray-500 uppercase">High-Risk Findings</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $highRiskFindings }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#ffcc00]">
        <p class="text-xs font-medium text-gray-500 uppercase">Risk Coverage</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $riskCoverage }}%</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <p class="text-xs font-medium text-gray-500 uppercase">Overdue Actions</p>
        <p class="text-3xl font-bold text-[#333] mt-1">{{ $overdueActions }}</p>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-1">
        <h3 class="text-sm font-semibold text-gray-700 mb-6 flex items-center justify-between">
            Departmental Efficiency
            <i class="fas fa-bolt text-yellow-500"></i>
        </h3>
        <div class="space-y-5">
            @foreach($deptEfficiency as $dept)
            <div>
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="font-black text-slate-500 uppercase tracking-widest">{{ $dept['dept'] }}</span>
                    <span class="font-bold text-slate-800">{{ $dept['score'] }}%</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $dept['score'] > 80 ? 'bg-green-500' : ($dept['score'] > 70 ? 'bg-blue-500' : 'bg-orange-500') }}" 
                         style="width: {{ $dept['score'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="mt-8 text-[10px] text-slate-400 italic leading-snug">
            Measured by the ratio of 'Closed' vs 'Open' action items within the agreed SLAs.
        </p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
        <h3 class="text-sm font-semibold text-gray-700 mb-6">Strategic Risk Coverage</h3>
        <div class="flex items-center justify-center h-48">
            <div class="text-center">
                <p class="text-5xl font-black text-[#004ea1]">{{ $riskCoverage }}%</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Enterprise Heatmap Integration</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cats = @json($risksByCategory);
    new Chart(document.getElementById('riskCategoryChart'), {
        type: 'doughnut',
        data: { labels: Object.keys(cats).map(c => c.charAt(0).toUpperCase()+c.slice(1)), datasets: [{ data: Object.values(cats), backgroundColor: ['#004ea1','#ffcc00','#333','#28a745','#3b82f6'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    const sev = @json($findingsBySeverity);
    new Chart(document.getElementById('execFindingsChart'), {
        type: 'bar', data: { labels: ['Critical','High','Medium','Low'], datasets: [{ data: [sev.critical||0,sev.high||0,sev.medium||0,sev.low||0], backgroundColor: ['#dc3545','#fd7e14','#ffc107','#28a745'], borderRadius: 6 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
});
</script>
@endpush
@endsection

