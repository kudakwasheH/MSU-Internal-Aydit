<div class="mb-8 border-b border-slate-200">
    <nav class="flex -mb-px space-x-8 overflow-x-auto no-scrollbar" aria-label="Reports">
        @php
            $tabs = [
                ['name' => 'Summary Analytics', 'route' => 'reports.generate', 'icon' => 'fas fa-chart-pie'],
                ['name' => 'Meeting Pack', 'route' => 'reports.meeting-pack', 'icon' => 'fas fa-briefcase'],
                ['name' => 'Rolling Audit Plan', 'route' => 'reports.rolling-plan', 'icon' => 'fas fa-calendar-alt'],
                ['name' => 'System Audit Logs', 'route' => 'audit-logs.index', 'icon' => 'fas fa-history'],
            ];
            $currentRoute = Route::currentRouteName();
        @endphp

        @foreach($tabs as $tab)
            @php
                $isActive = $currentRoute === $tab['route'];
            @endphp
            <a href="{{ route($tab['route']) }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-[10px] uppercase tracking-[0.2em] transition-all flex items-center gap-2 group
               {{ $isActive 
                  ? 'border-[#004ea1] text-[#004ea1]' 
                  : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300' }}">
                <i class="{{ $tab['icon'] }} {{ $isActive ? 'text-[#ffcc00]' : 'text-slate-300 group-hover:text-slate-400' }} transition-colors"></i>
                {{ $tab['name'] }}
            </a>
        @endforeach
    </nav>
</div>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
