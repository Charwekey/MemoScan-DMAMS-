@extends('layouts.app')

@section('header_title', 'Activity Audit Trails')

@section('content')
<div class="space-y-6">

    <!-- Filter Form Toolbar Card -->
    <div class="glass-card rounded-2xl p-5 shadow-lg">
        <form action="{{ route('audit_logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <!-- Filter by User -->
            <div>
                <label for="filter_user" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Executing User</label>
                <select name="user_id" id="filter_user" class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:border-indigo-500 transition-all text-xs">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ strtoupper($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Action -->
            <div>
                <label for="filter_action" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Administrative Action</label>
                <select name="action" id="filter_action" class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:border-indigo-500 transition-all text-xs">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold tracking-wide text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Apply Filter
                </button>
                @if(request()->filled('user_id') || request()->filled('action'))
                    <a href="{{ route('audit_logs.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold tracking-wide text-slate-300 hover:text-white bg-slate-950 hover:bg-slate-900 rounded-xl border border-slate-800 transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Timeline Entries Table -->
    <div class="glass-card rounded-2xl p-6 shadow-lg flex flex-col overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pr-2 w-48">Timestamp</th>
                        <th class="pb-3 pr-2 w-48">Administrator</th>
                        <th class="pb-3 pr-2 w-40">Action Class</th>
                        <th class="pb-3 pr-2">Log Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-xs text-slate-300">
                    @forelse($logs as $log)
                        @php
                            $badgeColor = match($log->action) {
                                'login' => 'bg-blue-500/20 text-blue-400 border border-blue-500/20',
                                'logout' => 'bg-slate-500/20 text-slate-400 border border-slate-500/20',
                                'create_user' => 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/20',
                                'edit_user' => 'bg-amber-500/20 text-amber-400 border border-amber-500/20',
                                'delete_user' => 'bg-rose-500/20 text-rose-400 border border-rose-500/20',
                                'create_memo' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
                                'edit_memo' => 'bg-purple-500/20 text-purple-400 border border-purple-500/20',
                                'delete_memo' => 'bg-orange-500/20 text-orange-400 border border-orange-500/20',
                                'restore_memo' => 'bg-teal-500/20 text-teal-400 border border-teal-500/20',
                                'download_memo' => 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/20',
                                'preview_memo' => 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/15',
                                default => 'bg-slate-800 text-slate-300 border border-slate-700'
                            };
                        @endphp
                        <tr class="group hover:bg-slate-800/20 transition-all">
                            <!-- Timestamp -->
                            <td class="py-3.5 pr-2 font-mono text-slate-400">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <span class="text-[9px] block text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- Administrator -->
                            <td class="py-3.5 pr-2">
                                @if($log->user)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-300 border border-slate-700">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-200 block leading-tight">{{ $log->user->name }}</span>
                                            <span class="text-[9px] uppercase tracking-wider text-slate-500 block">{{ $log->user->role }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-500 italic">System / Visitor</span>
                                @endif
                            </td>

                            <!-- Action Badge -->
                            <td class="py-3.5 pr-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider leading-none {{ $badgeColor }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            <!-- Details Description -->
                            <td class="py-3.5 pr-2 text-slate-200 font-medium">
                                {{ $log->details }}
                                @if($log->target_id)
                                    <span class="text-[9px] text-slate-500 font-mono ml-1.5">(Target ID: #{{ $log->target_id }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">No activity logs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($logs->hasPages())
            <div class="mt-6 border-t border-slate-800 pt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
