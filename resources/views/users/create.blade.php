@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-[#004ea1] transition-colors flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to User List
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Create User Account</h2>
            <p class="text-sm text-gray-500">Enter user details and assign system roles</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Name --}}
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="e.g. John Doe">
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
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
                    <input type="text" name="staff_id" id="staff_id" value="{{ old('staff_id') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="MSU0000">
                </div>

                {{-- Department --}}
                <div class="space-y-2">
                    <label for="department" class="block text-sm font-semibold text-gray-700">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="e.g. Internal Audit">
                </div>

                {{-- Position --}}
                <div class="space-y-2">
                    <label for="position" class="block text-sm font-semibold text-gray-700">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none"
                           placeholder="e.g. Senior Auditor">
                </div>

                {{-- Approval Level --}}
                <div class="space-y-2">
                    <label for="approval_level" class="block text-sm font-semibold text-gray-700">Approval Level (1-5)</label>
                    <select name="approval_level" id="approval_level" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none text-sm">
                        <option value="1" {{ old('approval_level', 1) == 1 ? 'selected' : '' }}>Level 1 — Operational / Field Auditor</option>
                        <option value="2" {{ old('approval_level') == 2 ? 'selected' : '' }}>Level 2 — Senior Auditor / Supervisory Reviewer</option>
                        <option value="3" {{ old('approval_level') == 3 ? 'selected' : '' }}>Level 3 — Audit Manager / Chief Internal Auditor</option>
                        <option value="4" {{ old('approval_level') == 4 ? 'selected' : '' }}>Level 4 — Executive / Audit Committee / Council</option>
                        <option value="5" {{ old('approval_level') == 5 ? 'selected' : '' }}>Level 5 — System Administrator</option>
                    </select>
                    <p class="text-[11px] text-gray-400">Defines hierarchical sign-off authority and delegation limits.</p>
                </div>

                {{-- Role --}}
                <div class="space-y-2 md:col-span-2">
                    <label for="role" class="block text-sm font-semibold text-gray-700">System Role</label>
                    <select name="role" id="role" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#004ea1]/20 focus:border-[#004ea1] transition-all outline-none">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SSO Notice --}}
                <div class="md:col-span-2 p-4 bg-blue-50/80 border border-blue-200/80 rounded-xl flex items-center gap-3.5 text-xs text-[#004ea1]">
                    <div class="w-8 h-8 rounded-lg bg-white border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Passwordless Google SSO Authentication</p>
                        <p class="text-gray-600 mt-0.5">Staff members will sign in directly using their university Google workspace account (<strong>@staff.msu.ac.zw</strong>). No password setup required.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t border-gray-100 pt-8">
                <button type="reset" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all">
                    Reset Form
                </button>
                <button type="submit" class="px-8 py-2.5 rounded-lg bg-[#004ea1] text-white font-semibold shadow-md hover:bg-[#003a7a] transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (!emailInput) return;

    emailInput.addEventListener('input', function(e) {
        const val = this.value;
        // When user types @, auto-complete the domain
        if (val.endsWith('@')) {
            this.value = val + 'staff.msu.ac.zw';
            // Place cursor right after the @
            const pos = val.length;
            this.setSelectionRange(pos, this.value.length);
        }
    });

    emailInput.addEventListener('keydown', function(e) {
        // If the domain part is selected and user presses a key, let them overwrite
        if (this.selectionStart !== this.selectionEnd && e.key.length === 1 && e.key !== '@') {
            // Allow natural overwrite behavior
        }
    });
});
</script>
@endpush
