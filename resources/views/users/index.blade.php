@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">System Users</h2>
        <p class="text-sm text-gray-500">Manage application users, roles, and access levels</p>
    </div>
    <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#004ea1] hover:bg-[#003a7a] text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
        <i class="fas fa-user-plus mr-2"></i>
        Add New User
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">User Information</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Staff ID</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Department & Position</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-[#004ea1] font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-mono rounded">
                            {{ $user->staff_id }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $user->department }}</div>
                        <div class="text-xs text-gray-500">{{ $user->position }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                        <span class="px-2.5 py-1 bg-blue-50 text-[#004ea1] text-xs font-medium rounded-full border border-blue-100">
                            {{ $role->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="p-2 text-gray-400 hover:text-[#004ea1] transition-colors" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Delete User" {{ $user->id === auth()->id() ? 'disabled opacity-50' : '' }}>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
