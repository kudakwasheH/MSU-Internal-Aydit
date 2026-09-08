@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div x-data="{ 
    deleteModalOpen: false, 
    userToDelete: null, 
    deleteActionUrl: '',
    confirmDelete(user, url) {
        this.userToDelete = user;
        this.deleteActionUrl = url;
        this.deleteModalOpen = true;
    }
}" @keydown.escape.window="deleteModalOpen = false">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">System Users</h2>
            <p class="text-sm text-gray-500 mt-0.5">Manage application users, roles, and access levels</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#004ea1] hover:bg-[#003a7a] text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200">
            <i class="fas fa-user-plus text-xs"></i>
            <span>Add New User</span>
        </a>
    </div>

    {{-- Users Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">User Information</th>
                        <th class="px-6 py-4">Staff ID</th>
                        <th class="px-6 py-4">Department & Position</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200/60 flex items-center justify-center text-[#004ea1] font-bold shadow-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate flex items-center gap-2">
                                        <span>{{ $user->name }}</span>
                                        @if($user->google_id)
                                            <span class="inline-flex items-center text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded font-normal" title="Linked with Google SSO">
                                                <i class="fab fa-google text-[9px] mr-1 text-emerald-600"></i>SSO
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-gray-100/90 text-gray-700 text-xs font-mono font-medium rounded-lg border border-gray-200/50">
                                {{ $user->staff_id }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->department ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $user->position ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($user->roles as $role)
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-[#004ea1] text-xs font-medium rounded-full border border-blue-100">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">No role assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-1.5">
                                <a href="{{ route('users.edit', $user) }}" 
                                   class="p-2 text-gray-400 hover:text-[#004ea1] hover:bg-blue-50 rounded-lg transition-all" 
                                   title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($user->id !== auth()->id())
                                    <button type="button" 
                                            @click="confirmDelete({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                staff_id: '{{ addslashes($user->staff_id) }}',
                                                position: '{{ addslashes($user->position ?? '') }}'
                                            }, '{{ route('users.destroy', $user) }}')"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" 
                                            title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @else
                                    <span class="p-2 text-gray-300 cursor-not-allowed" title="You cannot delete your own active session">
                                        <i class="fas fa-trash-alt"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Modern Delete Confirmation Modal --}}
    <div x-cloak
         x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        {{-- Backdrop blur with fade --}}
        <div x-show="deleteModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="deleteModalOpen = false"></div>

        <div class="min-h-screen flex items-center justify-center p-4 text-center sm:p-0">
            {{-- Modal Panel with smooth pop-in animation --}}
            <div x-show="deleteModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="deleteModalOpen = false"
                 class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-100">
                
                {{-- Close Button --}}
                <button type="button" 
                        @click="deleteModalOpen = false" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <div class="p-6 pt-7">
                    {{-- Icon Badge --}}
                    <div class="w-14 h-14 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-4 text-red-600 shadow-xs ring-8 ring-red-50/50">
                        <i class="fas fa-user-slash text-xl"></i>
                    </div>

                    {{-- Title & Subtitle --}}
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Delete User Account</h3>
                        <p class="text-sm text-gray-500 mt-1.5">
                            Are you sure you want to permanently delete this user? This action will revoke their system access and cannot be undone.
                        </p>
                    </div>

                    {{-- Target User Preview Card --}}
                    <template x-if="userToDelete">
                        <div class="mt-4 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center flex-shrink-0 text-sm">
                                <span x-text="userToDelete.name ? userToDelete.name.charAt(0).toUpperCase() : 'U'"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate" x-text="userToDelete.name"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="userToDelete.email"></p>
                            </div>
                            <span class="px-2 py-0.5 bg-white border border-gray-200 text-gray-700 text-[11px] font-mono rounded font-medium" x-text="userToDelete.staff_id"></span>
                        </div>
                    </template>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 border-t border-gray-100">
                    <button type="button" 
                            @click="deleteModalOpen = false"
                            class="w-full sm:w-auto px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl transition-all shadow-xs text-center">
                        Cancel
                    </button>
                    
                    <form :action="deleteActionUrl" method="POST" class="w-full sm:w-auto inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full sm:w-auto px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2">
                            <i class="fas fa-trash-alt text-xs"></i>
                            <span>Delete User</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
