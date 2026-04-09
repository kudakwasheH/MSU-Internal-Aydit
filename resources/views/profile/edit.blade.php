@extends('layouts.app')

@section('title', 'User Portfolio & Security')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Profile Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200">
        <div class="h-32 bg-gradient-to-r from-[#004ea1] to-[#002d5e] relative">
            <div class="absolute -bottom-12 left-8 flex items-end gap-6">
                <div class="w-32 h-32 rounded-3xl bg-white p-1 shadow-lg">
                    <div class="w-full h-full rounded-[20px] bg-slate-100 flex items-center justify-center text-[#004ea1] text-4xl font-black border-4 border-white">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div class="pb-4">
                    <h1 class="text-2xl font-black text-slate-800">{{ $user->name }}</h1>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ $user->position }} &middot; {{ $user->department }}</p>
                </div>
            </div>
        </div>
        <div class="pt-16 pb-8 px-8 flex flex-wrap gap-4 items-center justify-end">
            <span class="px-4 py-1.5 bg-[#ffcc00]/10 text-[#004ea1] text-xs font-black uppercase tracking-tighter border border-[#ffcc00]/20 rounded-xl">
                <i class="fas fa-id-badge mr-1"></i> {{ $user->staff_id }}
            </span>
            <span class="px-4 py-1.5 bg-green-50 text-green-700 text-xs font-black uppercase tracking-tighter border border-green-100 rounded-xl">
                <i class="fas fa-shield-halved mr-1"></i> Approval Level {{ $user->approval_level }}
            </span>
            <span class="px-4 py-1.5 bg-[#004ea1] text-white text-xs font-black uppercase tracking-tighter rounded-xl shadow-sm">
                {{ $user->roles->first()?->name }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Identity Column --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-slate-200 opacity-80 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6 border-l-4 border-slate-300 pl-4">Account Identity</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Display Name</label>
                        <input type="text" value="{{ $user->name }}" disabled 
                               class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Official Email</label>
                        <input type="email" value="{{ $user->email }}" disabled 
                               class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed">
                    </div>
                </div>
                <div class="mt-6 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                    <p class="text-[10px] text-blue-700 font-bold leading-snug">
                        <i class="fas fa-shield-check mr-1"></i> Identity Lock Active
                    </p>
                    <p class="text-[9px] text-blue-600/70 mt-1 leading-relaxed italic">
                        In accordance with the MSU Internal Audit security policy, your core identity (Name & Email) is synchronized with the University HR system and cannot be modified manually.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8 border border-slate-200">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6 border-l-4 border-[#ffcc00] pl-4">University Credentials</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Staff ID</p>
                        <p class="text-sm font-bold text-slate-700 tracking-tight">{{ $user->staff_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Department</p>
                        <p class="text-sm font-bold text-slate-700">{{ $user->department }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Designation</p>
                        <p class="text-sm font-bold text-slate-700">{{ $user->position }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Authorization</p>
                        <p class="text-sm font-bold text-slate-700">Level {{ $user->approval_level }}</p>
                    </div>
                </div>
                <div class="mt-8 p-4 bg-slate-50 rounded-xl border border-dotted border-slate-200">
                    <p class="text-[10px] text-slate-400 italic leading-snug">
                        <i class="fas fa-info-circle mr-1"></i> University credentials are fixed for audit traceability. 
                        To update your designation or access level, please contact the **System Administrator / IT Department**.
                    </p>
                </div>
            </div>
        </div>

        {{-- Security Column --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-slate-200">
                <h3 class="text-sm font-bold text-red-800 uppercase tracking-widest mb-6 border-l-4 border-red-500 pl-4">Security & Access</h3>
                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Current Password</label>
                        <input type="password" name="current_password" required 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition-all">
                    </div>
                    <div class="h-px bg-slate-100 my-4"></div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">New Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all">
                    </div>
                    <button type="submit" class="w-full mt-4 py-3 bg-slate-800 text-white text-xs font-bold rounded-xl shadow-lg hover:bg-black transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Update Password
                    </button>
                    <p class="text-[9px] text-center text-slate-400 mt-4 leading-tight uppercase font-bold tracking-widest">
                        Refresh session after updating
                    </p>
                </form>
            </div>
            
            {{-- Session Statistics (Optional) --}}
            <div class="bg-[#004ea1] rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10">
                    <i class="fas fa-shield-alt text-[120px]"></i>
                </div>
                <h4 class="text-xs font-black uppercase tracking-widest text-[#ffcc00] mb-4">Security Notice</h4>
                <p class="text-xs leading-relaxed opacity-80">
                    MSU Internal Audit requires two-factor authentication for sensitive roles. Your access is currently monitored for security compliance.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
