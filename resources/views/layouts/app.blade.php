<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DMAMS') }} - Digital Memo Archiving</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Inline Custom Styles for Glassmorphism & Aesthetics -->
    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: radial-gradient(circle at top right, rgba(49, 46, 129, 0.15), transparent 45%),
                        radial-gradient(circle at bottom left, rgba(88, 28, 135, 0.15), transparent 45%),
                        #0f172a;
            color: #f8fafc;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card-hover:hover {
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.15);
            transform: translateY(-2px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
            border-left: 4px solid #6366f1;
            color: #ffffff;
        }
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

    <!-- Sidebar Wrapper -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 glass-card border-r border-slate-800/80 transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:flex shrink-0">
        
        <!-- Brand Logo Header -->
        <div class="flex items-center justify-between h-20 px-6 border-b border-slate-800/80">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-white leading-none">DMAMS</h1>
                    <span class="text-[10px] uppercase font-semibold tracking-wider text-slate-400">Memo Archiver</span>
                </div>
            </a>
            <button class="lg:hidden text-slate-400 hover:text-white" onclick="toggleMobileSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Core</span>
            
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white transition-all group {{ Request::is('dashboard') || Request::is('/') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                </svg>
                Dashboard
            </a>

            <!-- Memos Archive Link -->
            <a href="{{ route('memos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white transition-all group {{ Request::is('memos') && !Request::is('memos/create') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Memos Archive
            </a>

            <!-- Upload Memo Link (Admin & Staff Only) -->
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                <a href="{{ route('memos.create') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white transition-all group {{ Request::is('memos/create') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Upload / Scan Memo
                </a>
            @endif

            <!-- Admin Section -->
            @if(Auth::user()->isAdmin())
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block pt-4 mb-2">Management</span>

                <!-- User Management -->
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white transition-all group {{ Request::is('users*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    User Directory
                </a>

                <!-- Audit Trails -->
                <a href="{{ route('audit_logs.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white transition-all group {{ Request::is('logs*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Audit Trails
                </a>
            @endif

        </nav>

        <!-- Sidebar User Footer Card -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-900/40">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-200 border border-slate-600">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <h2 class="text-sm font-semibold text-slate-200 truncate leading-none mb-1">{{ Auth::user()->name }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium tracking-wide uppercase leading-none {{ Auth::user()->role === 'admin' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/20' : (Auth::user()->role === 'staff' ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20') }}">
                        {{ Auth::user()->role }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-xs font-semibold rounded-xl text-rose-400 hover:text-white hover:bg-rose-500/20 border border-rose-500/10 hover:border-rose-500/30 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Sign Out Account
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        
        <!-- Header Bar -->
        <header class="flex items-center justify-between h-20 px-6 lg:px-8 border-b border-slate-800/80 glass-card bg-slate-900/30 shrink-0">
            <div class="flex items-center">
                <!-- Mobile Sidebar Toggle -->
                <button class="lg:hidden text-slate-400 hover:text-white mr-4 focus:outline-none" onclick="toggleMobileSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h2 class="text-xl font-bold tracking-tight text-white">
                    @yield('header_title', 'System Dashboard')
                </h2>
            </div>
            
            <!-- Quick Actions & Date -->
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-slate-400 hidden sm:inline">
                    {{ now()->format('l, d F Y') }}
                </span>
                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <a href="{{ route('memos.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-wide text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Archive Memo
                    </a>
                @endif
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            <!-- Toast / Session Alerts -->
            @if(session('success'))
                <div id="alert-banner-success" class="flex items-center justify-between p-4 mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 backdrop-blur-md animate-fade-in-down">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button class="text-emerald-400 hover:text-emerald-200" onclick="document.getElementById('alert-banner-success').remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="alert-banner-error" class="flex items-center justify-between p-4 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 backdrop-blur-md animate-fade-in-down">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button class="text-rose-400 hover:text-rose-200" onclick="document.getElementById('alert-banner-error').remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @yield('content')
            
        </main>
    </div>

    <!-- Toggle Sidebar JS -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
