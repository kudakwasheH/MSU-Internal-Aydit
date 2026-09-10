<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - MSU Internal Audit</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { display: flex; align-items: center; gap: 1rem; padding: 0.875rem 1.75rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; color: rgba(255, 255, 255, 0.7); border-left: 4px solid transparent; }
        .sidebar-link:hover { color: white; background-color: #003a7a; }
        .sidebar-link i { width: 1.25rem; text-align: center; transition: color 0.2s; font-size: 1rem; }
        .sidebar-link.active { background-color: #002d5e; color: white; border-left-color: #ffcc00; font-weight: 600; }
        .sidebar-link.active i { color: #ffcc00; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-slate-800" x-data="{ sidebarOpen: true }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#004ea1] text-white transition-all duration-300 ease-in-out shadow-lg"
               :class="sidebarOpen ? 'w-64' : 'w-20'">
            {{-- Logo Area --}}
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10 mb-2">
                <div class="w-10 h-10 bg-white rounded flex items-center justify-center flex-shrink-0 p-1">
                    <img src="{{ asset('images/main-logo.png') }}" alt="MSU Logo" class="w-full h-full object-contain">
                </div>
                <div x-show="sidebarOpen" class="overflow-hidden">
                    <p class="font-bold text-sm leading-none text-white tracking-tight">MSU</p>
                    <p class="text-[#ffcc00] text-[10px] uppercase font-bold tracking-widest mt-1">Internal Audit</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 flex flex-col">
                @can('view dashboard')
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                @endcan
                
                @can('view audits')
                <a href="{{ route('audits.index') }}" class="sidebar-link {{ request()->routeIs('audits.*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt"></i>
                    <span x-show="sidebarOpen">Audit Universe</span>
                </a>
                <a href="{{ route('working-papers.index') }}" class="sidebar-link {{ request()->routeIs('working-papers.*') ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i>
                    <span x-show="sidebarOpen">Working Papers</span>
                </a>
                @endcan

                @can('view findings')
                <a href="{{ route('findings.index') }}" class="sidebar-link {{ request()->routeIs('findings.*') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span x-show="sidebarOpen">Finding Tracker</span>
                </a>
                @endcan

                @can('view action-items')
                <a href="{{ route('action-items.index') }}" class="sidebar-link {{ request()->routeIs('action-items.*') ? 'active' : '' }}">
                    <i class="fas fa-check-square"></i>
                    <span x-show="sidebarOpen">Action Items</span>
                </a>
                @endcan

                @can('view risks')
                <a href="{{ route('risks.index') }}" class="sidebar-link {{ request()->routeIs('risks.index') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span x-show="sidebarOpen">Risk Register</span>
                </a>
                <a href="{{ route('risks.heatmap') }}" class="sidebar-link {{ request()->routeIs('risks.heatmap') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span x-show="sidebarOpen">Risk Heatmap</span>
                </a>
                @endcan

                @can('view reports')
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ (request()->routeIs('reports.*') && !request()->routeIs('reports.generate') && !request()->routeIs('reports.meeting-pack') && !request()->routeIs('reports.rolling-plan')) ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span x-show="sidebarOpen">Reports Centre</span>
                </a>
                <a href="{{ route('reports.generate') }}" class="sidebar-link {{ request()->routeIs('reports.generate') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span x-show="sidebarOpen">Reports Dashboard</span>
                </a>
                <a href="{{ route('reports.meeting-pack') }}" class="sidebar-link {{ request()->routeIs('reports.meeting-pack') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span x-show="sidebarOpen">Meeting Pack</span>
                </a>
                @endcan

                @can('view audit-logs')
                <a href="{{ route('audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span x-show="sidebarOpen">System Logs</span>
                </a>
                @endcan

                @can('manage users')
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span x-show="sidebarOpen">User Management</span>
                </a>
                @endcan
            </nav>

            {{-- User Profile Link --}}
            <a href="{{ route('profile.edit') }}" class="mt-auto border-t border-white/10 p-4 bg-black/5 block hover:bg-white/5 transition-all group">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white flex-shrink-0 group-hover:bg-[#ffcc00] group-hover:text-[#004ea1] transition-all">
                        <i class="fas fa-user-gear text-xs"></i>
                    </div>
                    <div x-show="sidebarOpen" class="overflow-hidden">
                        <p class="text-xs font-bold truncate group-hover:text-[#ffcc00] transition-colors">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-white/50 truncate">{{ Auth::user()->position }}</p>
                    </div>
                </div>
            </a>
            
            {{-- Sign Out --}}
            <div class="p-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left py-2 border-none rounded hover:bg-red-600/20 text-red-400 group">
                        <i class="fas fa-sign-out-alt fa-fw group-hover:text-red-300"></i>
                        <span x-show="sidebarOpen">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-[#004ea1] transition">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-[#333]">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="hidden sm:inline text-sm text-gray-500">
                            <i class="fas fa-id-badge mr-1"></i>
                            {{ Auth::user()->staff_id }} &middot; {{ Auth::user()->position }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium bg-[#004ea1] text-white rounded-full">
                            {{ Auth::user()->roles->first()?->name ?? 'User' }}
                        </span>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show"
                 class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
            @endif
            @if($errors->any())
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Validation Errors:</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Page Content --}}
            <main class="p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-4 text-center text-xs text-gray-400 border-t border-gray-200">
                &copy; {{ date('Y') }} Midlands State University &middot; Internal Audit Management System v1.0
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

