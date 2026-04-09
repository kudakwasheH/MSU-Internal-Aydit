@extends('layouts.guest')
@section('title', 'Login')
@section('content')
<div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
    <div class="bg-[#004ea1] px-8 py-8 text-center">
        <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center mx-auto mb-4 p-1.5 shadow-sm">
            <img src="{{ asset('images/main-logo.png') }}" alt="MSU Logo" class="w-full h-full object-contain">
        </div>
        <h1 class="text-xl font-bold text-white">Midlands State University</h1>
        <p class="text-[#ffcc00] text-sm mt-1">Internal Audit Management System</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="px-8 py-8 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#004ea1] focus:border-transparent text-sm"
                    placeholder="staff@msu.ac.zw">
            </div>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" required
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#004ea1] focus:border-transparent text-sm"
                    placeholder="••••••••">
            </div>
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#004ea1] focus:ring-[#004ea1]">
                Remember me
            </label>
        </div>
        <button type="submit" class="w-full bg-[#004ea1] text-white py-2.5 rounded-lg font-semibold hover:bg-[#001533] transition-colors text-sm">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>
    </form>
    <div class="bg-gray-50 px-8 py-4 text-center text-xs text-gray-500">
        Demo: admin@msu.ac.zw / password123
    </div>
</div>
@endsection

