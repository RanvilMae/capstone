<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LATER-X')) - {{ config('app.name', 'LATER-X') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.25s ease-out forwards; }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50/50 text-gray-800">

<!-- Global Alpine State (Sidebar + Import Modal) -->
<div x-data="{ sidebarOpen: false, openImportModal: false }" class="min-h-screen flex">

    {{-- Desktop Sidebar --}}
    <aside class="hidden md:flex flex-col justify-between w-64 h-screen bg-emerald-950 text-white shadow-2xl fixed top-0 left-0 z-40 border-r border-emerald-900/50">
        <div>
            {{-- Brand Logo --}}
            <div class="p-6 border-b border-emerald-900/50 flex flex-col items-center">
                <a href="{{ route('dashboard.index') }}" class="group text-center">
                    <img src="{{ asset('images/laterx-logo.png') }}" class="mx-auto w-24 mb-2 transition-transform duration-300 group-hover:scale-105" alt="LATER-X Logo">
                    <h2 class="text-xl font-black text-emerald-400 tracking-wider uppercase">{{ config('app.name', 'LATER-X') }}</h2>
                    <p class="text-[10px] font-bold text-emerald-300/70 uppercase tracking-widest mt-0.5">Decision Support System</p>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="p-4 space-y-1">
                <a href="{{ route('dashboard.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('dashboard*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie text-sm w-5 text-center"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('transactions.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('transactions.index') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-flask text-sm w-5 text-center"></i>
                    <span>{{ __('Latex Monitoring') }}</span>
                </a>

                <a href="{{ route('transactions.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('transactions.create') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-circle-plus text-sm w-5 text-center"></i>
                    <span>{{ __('Create Transaction') }}</span>
                </a>

                <a href="{{ route('plots.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.plots.*') || request()->routeIs('staff.plots.*') || request()->routeIs('plots.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-vector-square text-sm w-5 text-center"></i>
                    <span>{{ __('Plot Management') }}</span>
                </a>

                <a href="{{ route('main.farmer.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.farmer.*') || request()->routeIs('staff.farmer.index') || request()->routeIs('main.farmer.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-users text-sm w-5 text-center"></i>
                    <span>{{ __('Farmers') }}</span>
                </a>

                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-sm w-5 text-center"></i>
                    <span>{{ __('Sales Report') }}</span>
                </a>

                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="fa-solid fa-user-gear text-sm w-5 text-center"></i>
                        <span>{{ __('User Management') }}</span>
                    </a>
                @endif
            </nav>
        </div>

        {{-- Profile Dropdown --}}
        <div x-data="{ open: false }" class="p-4 border-t border-emerald-900/50 relative">
            <button @click="open = !open"
                    class="flex items-center gap-3 w-full p-2.5 bg-emerald-900/40 border border-emerald-800/50 rounded-xl hover:bg-emerald-900/80 transition-all">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center uppercase shadow">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="text-left overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-semibold text-emerald-300 uppercase truncate">{{ auth()->user()->role ?? 'User' }}</p>
                </div>
                <i class="fa-solid fa-chevron-up ml-auto text-xs text-emerald-400"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute bottom-full left-4 right-4 mb-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 p-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>{{ __('Logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Sidebar Backdrop --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"></div>

    {{-- Mobile Sidebar --}}
    <aside x-show="sidebarOpen" x-cloak
           class="fixed inset-y-0 left-0 w-64 bg-emerald-950 text-white shadow-2xl z-50 flex flex-col justify-between md:hidden">
        <div>
            <div class="p-6 flex justify-between items-center border-b border-emerald-900/50">
                <img src="{{ asset('images/laterx-logo.png') }}" class="w-24" alt="LATER-X Logo">
                <button @click="sidebarOpen = false" class="text-emerald-300 hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Dashboard') }}</a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Latex Monitoring') }}</a>
                <a href="{{ route('transactions.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Create Transaction') }}</a>
                <a href="{{ route('plots.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Plot Management') }}</a>
                <a href="{{ route('main.farmer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Farmers') }}</a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('Sales Report') }}</a>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-emerald-100 hover:bg-emerald-900/50">{{ __('User Management') }}</a>
                @endif
            </nav>
        </div>

        <div class="p-4 border-t border-emerald-900/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-rose-600/20 border border-rose-500/30 text-rose-300 rounded-xl hover:bg-rose-600 hover:text-white transition-all text-xs font-bold">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Shell --}}
    <div class="flex-1 md:ml-64 min-h-screen flex flex-col">
        
        {{-- TOP BAR --}}
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-16 flex items-center justify-between px-6 z-30 sticky top-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="text-emerald-700 md:hidden focus:outline-none">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                <h2 class="hidden md:block text-xs font-extrabold text-gray-500 uppercase tracking-widest">
                    {{ __('LATER-eX Decision Support System') }}
                </h2>
            </div>

            <div class="flex items-center gap-4">
                {{-- Language Switcher --}}
                <div class="flex bg-gray-100/80 p-1 rounded-xl border border-gray-200/50">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="px-3 py-1 text-xs font-black rounded-lg transition-all {{ app()->getLocale() == 'en' ? 'bg-white shadow-sm text-emerald-700' : 'text-gray-500 hover:text-emerald-700' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'th') }}" 
                       class="px-3 py-1 text-xs font-black rounded-lg transition-all {{ app()->getLocale() == 'th' ? 'bg-white shadow-sm text-emerald-700' : 'text-gray-500 hover:text-emerald-700' }}">
                        TH
                    </a>
                </div>
                
                <div class="h-4 w-px bg-gray-200"></div>

                <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-gradient-to-r from-emerald-800 to-emerald-700 text-white shadow-md">
                <div class="px-6 py-6 mx-auto max-w-7xl">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>