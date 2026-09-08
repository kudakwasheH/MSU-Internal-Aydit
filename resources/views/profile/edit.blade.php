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
            {{-- Security Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-[#004ea1] text-white">
                    <h3 class="text-base font-bold flex items-center gap-3">
                        <i class="fas fa-lock text-[#ffcc00]"></i>
                        Security Settings
                    </h3>
                </div>
                <div class="p-8">
                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Current Password</label>
                            <input type="password" name="current_password" required 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] focus:border-transparent transition-all outline-none"
                                   placeholder="Confirm current password">
                        </div>
                        <div class="h-px bg-slate-100 my-4"></div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">New Password</label>
                            <input type="password" name="password" required 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] focus:border-transparent transition-all outline-none"
                                   placeholder="Enter new password">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] focus:border-transparent transition-all outline-none"
                                   placeholder="Repeat new password">
                        </div>
                        <button type="submit" class="w-full mt-4 py-3.5 bg-[#004ea1] hover:bg-[#003a7a] text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-key"></i>
                            Update Password
                        </button>
                    </form>
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
