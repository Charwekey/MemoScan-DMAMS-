@extends('layouts.app')

@section('header_title', 'User Directory')

@section('content')
<div class="space-y-6">
    
    <!-- Header Page Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">Manage school administration staff, view their system roles, and assign security levels.</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold tracking-wide text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Add New User
            </button>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="glass-card rounded-2xl p-6 shadow-lg flex flex-col overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pr-2">User Details</th>
                        <th class="pb-3 pr-2">Email Address</th>
                        <th class="pb-3 pr-2">System Role</th>
                        <th class="pb-3 pr-2">Added On</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-xs text-slate-300">
                    @forelse($users as $user)
                        <tr class="group hover:bg-slate-800/20 transition-all">
                            <!-- Name / Initial -->
                            <td class="py-3.5 pr-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-slate-300">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-200 block">{{ $user->name }}</span>
                                        @if($user->id === Auth::id())
                                            <span class="text-[9px] font-medium text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/15">Active Account</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Email -->
                            <td class="py-3.5 pr-2 font-mono text-slate-300">
                                {{ $user->email }}
                            </td>
                            
                            <!-- Role -->
                            <td class="py-3.5 pr-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium tracking-wide uppercase {{ $user->role === 'admin' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/20' : ($user->role === 'staff' ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            
                            <!-- Created Date -->
                            <td class="py-3.5 pr-2 text-slate-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            
                            <!-- Actions -->
                            <td class="py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <button onclick='openEditModal(@json($user))' class="p-1.5 rounded-lg bg-slate-800 text-indigo-400 hover:text-white hover:bg-indigo-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Edit User">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>

                                @if($user->id === Auth::id())
                                    <span class="inline-block" title="You cannot delete your own logged-in account.">
                                        <button disabled class="p-1.5 rounded-lg bg-slate-900 text-slate-600 border border-slate-800 cursor-not-allowed inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @else
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this user? All their logged activities will remain, but the account will be revoked.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 text-rose-400 hover:text-white hover:bg-rose-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Delete User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if($users->hasPages())
            <div class="mt-6 border-t border-slate-800 pt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add New User -->
<div id="create-user-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="w-full max-w-lg glass-card rounded-2xl overflow-hidden shadow-2xl relative animate-zoom-in">
        
        <!-- Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
            <h4 class="font-bold text-slate-200 text-sm uppercase tracking-wider">Create New Administrator Account</h4>
            <button onclick="closeCreateModal()" class="text-slate-400 hover:text-white p-1 hover:bg-slate-800 rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="create_name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" name="name" id="create_name" required value="{{ old('name') }}" placeholder="e.g. Dr. John Doe" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Email -->
            <div>
                <label for="create_email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" id="create_email" required value="{{ old('email') }}" placeholder="e.g. j.doe@dmams.com" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Password -->
            <div>
                <label for="create_password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Access Password</label>
                <input type="password" name="password" id="create_password" required placeholder="Minimum 8 characters" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Role Selection -->
            <div>
                <label for="create_role" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Security Authorization Role</label>
                <select name="role" id="create_role" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="" disabled selected>Select Role...</option>
                    <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>Viewer (Read-Only Access)</option>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Upload & Limit Edit)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Full Control Access)</option>
                </select>
            </div>

            <!-- Validation Error Alert inside Modal -->
            @if ($errors->any() && !session('is_edit_error'))
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Footer Actions -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-950 hover:bg-slate-900 rounded-xl border border-slate-800 transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg transition-all">
                    Save Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Existing User -->
<div id="edit-user-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="w-full max-w-lg glass-card rounded-2xl overflow-hidden shadow-2xl relative animate-zoom-in">
        
        <!-- Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
            <h4 class="font-bold text-slate-200 text-sm uppercase tracking-wider">Modify User Account Details</h4>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white p-1 hover:bg-slate-800 rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="edit-user-form" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="edit_name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" name="name" id="edit_name" required value="" placeholder="e.g. Dr. John Doe" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Email -->
            <div>
                <label for="edit_email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" id="edit_email" required value="" placeholder="e.g. j.doe@dmams.com" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="edit_password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Change Password</label>
                    <span class="text-[10px] text-slate-500">Leave blank to retain current password</span>
                </div>
                <input type="password" name="password" id="edit_password" placeholder="Enter new password (optional)" 
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <!-- Role Selection -->
            <div>
                <label for="edit_role" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Security Authorization Role</label>
                <select name="role" id="edit_role" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="viewer">Viewer (Read-Only Access)</option>
                    <option value="staff">Staff (Upload & Limit Edit)</option>
                    <option value="admin">Admin (Full Control Access)</option>
                </select>
            </div>

            <!-- Validation Error Alert inside Modal -->
            @if ($errors->any() && session('is_edit_error'))
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Footer Actions -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-950 hover:bg-slate-900 rounded-xl border border-slate-800 transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg transition-all">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const createModal = document.getElementById('create-user-modal');
    const editModal = document.getElementById('edit-user-modal');
    const editForm = document.getElementById('edit-user-form');

    function openCreateModal() {
        createModal.classList.remove('hidden');
        createModal.classList.add('flex');
    }

    function closeCreateModal() {
        createModal.classList.add('hidden');
        createModal.classList.remove('flex');
    }

    function openEditModal(user) {
        // Build Action Link
        editForm.action = `/users/${user.id}`;
        
        // Populate inputs
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_role').value = user.role;

        // Open modal
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
    }

    // Retain modal open state on validation errors
    window.addEventListener('DOMContentLoaded', () => {
        @if($errors->any())
            @if(session('is_edit_error'))
                // Try to find stored user details to re-populate action and fields properly
                const errorUserId = "{{ old('edit_user_id') }}";
                if(errorUserId) {
                    editForm.action = `/users/${errorUserId}`;
                    document.getElementById('edit_name').value = "{{ old('name') }}";
                    document.getElementById('edit_email').value = "{{ old('email') }}";
                    document.getElementById('edit_role').value = "{{ old('role') }}";
                }
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            @else
                createModal.classList.remove('hidden');
                createModal.classList.add('flex');
            @endif
        @endif
    });
</script>
@endsection
