@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-[#004ea1] transition-colors flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to User List
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Edit User Account</h2>
            <p class="text-sm text-gray-500">Update details for {{ $user->name }}</p>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Name --}}
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="username@staff.msu.ac.zw">
                    <p class="text-[11px] text-gray-500">Must be an official staff email ending with <span class="font-semibold text-[#004ea1]">@staff.msu.ac.zw</span></p>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Staff ID --}}
                <div class="space-y-2">
                    <label for="staff_id" class="block text-sm font-semibold text-gray-700">Staff ID</label>
                    <input type="text" name="staff_id" id="staff_id" value="{{ old('staff_id', $user->staff_id) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                </div>

                {{-- Department --}}
                <div class="space-y-2">
                    <label for="department" class="block text-sm font-semibold text-gray-700">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                </div>

                {{-- Position --}}
                <div class="space-y-2">
                    <label for="position" class="block text-sm font-semibold text-gray-700">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                </div>

                {{-- Approval Level --}}
                <div class="space-y-2">
                    <label for="approval_level" class="block text-sm font-semibold text-gray-700">Approval Level (1-5)</label>
                    <select name="approval_level" id="approval_level" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('approval_level', $user->approval_level) == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Role --}}
                <div class="space-y-2 md:col-span-2">
                    <label for="role" class="block text-sm font-semibold text-gray-700">System Role</label>
                    <select name="role" id="role" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $userRole) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-2">Security</h3>
                    <p class="text-xs text-gray-500 mb-4">Leave password fields empty if you don't want to change the password.</p>
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="••••••••">
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t border-gray-100 pt-8">
                <button type="submit" class="px-8 py-2.5 rounded-lg bg-[#004ea1] text-white font-semibold shadow-md hover:bg-[#003a7a] transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
