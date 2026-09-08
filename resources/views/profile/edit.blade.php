@extends('layouts.app')

@section('title', 'User Profile & Security')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in duration-500">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-2xl bg-[#004ea1] flex items-center justify-center text-white text-3xl font-bold shadow-lg border-4 border-white">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                <p class="text-slate-500 font-medium mt-1 flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-slate-100 rounded text-xs font-bold uppercase tracking-wider text-slate-600">{{ $user->position }}</span>
                    <span class="text-slate-300">|</span>
                    <span>{{ $user->department }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden md:block">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Role</p>
                <p class="text-sm font-bold text-[#004ea1]">{{ $user->roles->first()?->name }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-[#004ea1]">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Identity & Credentials --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Account Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-3">
                        <i class="fas fa-user-circle text-[#004ea1]"></i>
                        Account Information
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Synced: {{ now()->format('d M, Y') }}</span>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Full Name</label>
                        <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 font-semibold text-sm">
                            <i class="fas fa-id-card text-slate-300"></i>
                            {{ $user->name }}
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                        <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 font-semibold text-sm">
                            <i class="fas fa-envelope text-slate-300"></i>
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="md:col-span-2 p-4 bg-blue-50 rounded-xl border border-blue-100 flex gap-4 items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Personal identity details are managed by the **University Human Resources System**. For corrections, please contact HR or the System Administrator.
                        </p>
                    </div>
                </div>
            </div>

            {{-- University Credentials --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-3">
                        <i class="fas fa-university text-[#004ea1]"></i>
                        University Placement
                    </h3>
                </div>
                <div class="p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Staff Number</p>
                        <p class="text-sm font-bold text-slate-800">{{ $user->staff_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Department</p>
                        <p class="text-sm font-bold text-slate-800">{{ $user->department }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Position</p>
                        <p class="text-sm font-bold text-slate-800">{{ $user->position }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Clearance</p>
                        <p class="text-sm font-bold text-slate-800">Level {{ $user->approval_level }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Security & Settings --}}
        <div class="space-y-8">
            {{-- Google SSO Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-[#004ea1] text-white">
                    <h3 class="text-base font-bold flex items-center gap-3">
                        <i class="fas fa-shield-alt text-[#ffcc00]"></i>
                        Authentication
                    </h3>
                </div>
                <div class="p-8 space-y-6">
                    {{-- SSO Status --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-800">Google Single Sign-On</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                        @if(auth()->user()->google_id)
                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[11px] flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-emerald-500"></i> Linked
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full font-semibold text-[11px] flex items-center gap-1.5">
                                <i class="fas fa-clock text-amber-500"></i> Pending
                            </span>
                        @endif
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- Info Notice --}}
                    <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-xl">
                        <p class="text-xs text-slate-600 leading-relaxed">
                            <i class="fas fa-info-circle text-[#004ea1] mr-1.5"></i>
                            Your account is secured via <strong>Google Workspace SSO</strong>. Password management is handled by your university Google account (<strong>@staff.msu.ac.zw</strong>).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Audit Compliance Box --}}
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center gap-3 mb-4 text-[#004ea1]">
                    <i class="fas fa-clipboard-check"></i>
                    <h4 class="text-xs font-bold uppercase tracking-widest">Compliance Status</h4>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                        <span class="text-xs text-slate-500 font-medium">System Access</span>
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded">SECURE</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-200/50">
                        <span class="text-xs text-slate-500 font-medium">Activity Logging</span>
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded">ENABLED</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-slate-500 font-medium">Session IP</span>
                        <span class="font-mono text-[10px] text-slate-400">{{ request()->ip() }}</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 italic mt-6 leading-relaxed">
                    * All profile modifications are recorded in the system audit logs for accountability.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
