<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex">

    {{-- Desktop Sidebar --}}
    <aside class="fixed flex-col justify-between hidden w-64 h-screen text-gray-800 shadow md:flex bg-green-50">
        <div>
            <div class="p-6">
                <img src="{{ asset('images/laterx-logo.png') }}" class="mx-auto mb-2 w-28" alt="LATER-X Logo">
                <h2 class="text-2xl font-bold text-center text-green-700">{{ config('app.name', 'LATER-X') }}</h2>
            </div>

            <nav class="mt-6 space-y-1">
                <a href="{{ route('dashboard.index') }}"
                   class="block px-6 py-2 rounded {{ request()->routeIs('dashboard*') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                    {{ __('Dashboard') }}
                </a>

                <a href="{{ route('transactions.index') }}"
                   class="block px-6 py-2 rounded {{ request()->routeIs('transactions.index') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                    {{ __('Latex Monitoring') }}
                </a>

                <a href="{{ route('transactions.create') }}"
                   class="block px-6 py-2 rounded {{ request()->routeIs('transactions.create') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                    {{ __('Create Transaction') }}
                </a>

                <a href="{{ route('plots.index') }}"
                   class="block px-6 py-2 rounded {{ request()->routeIs('admin.plots.*') || request()->routeIs('staff.plots.*') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                    {{ __('Plot Management') }}
                </a>

                <a href="{{ route('main.farmer.index') }}"
                   class="block px-6 py-2 rounded {{ request()->routeIs('admin.farmer.*') || request()->routeIs('staff.farmer.index') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                    {{ __('Farmers') }}
                </a>

                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}"
                       class="block px-6 py-2 rounded {{ request()->routeIs('admin.users*') ? 'bg-green-200 font-semibold text-green-900' : 'hover:bg-green-100' }}">
                        {{ __('User Management') }}
                    </a>
                @endif
            </nav>
        </div>

        {{-- Profile Dropdown --}}
        <div x-data="{ open: false }" class="relative px-6 mb-6">
            <button @click="open = !open"
                    class="flex items-center w-full px-4 py-2 space-x-2 transition-colors duration-300 bg-green-200 rounded hover:bg-green-300">
                <i class="text-2xl text-green-700 fas fa-user-circle"></i>
                <span class="hidden font-semibold text-green-700 md:block">{{ auth()->user()->name }}</span>
                <i class="ml-auto text-green-700 fas fa-chevron-down"></i>
            </button>

            <div x-show="open" @click.away="open = false"
                 class="absolute left-0 z-50 w-full p-2 mb-2 bg-white border rounded shadow-lg bottom-full">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-left text-red-500 rounded hover:bg-red-100">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Sidebar --}}
    <aside x-show="sidebarOpen" @click.away="sidebarOpen = false"
           class="fixed inset-y-0 left-0 z-50 flex flex-col justify-between w-64 text-gray-800 shadow bg-green-50 md:hidden">
        <div>
            <div class="flex items-center justify-between p-6">
                <img src="{{ asset('images/laterx-logo.png') }}" class="w-28" alt="LATER-X Logo">
                <button @click="sidebarOpen = false" class="text-gray-600 hover:text-gray-800">
                    <i class="text-xl fas fa-times"></i>
                </button>
            </div>

            <nav class="mt-6 space-y-1">
                <a href="{{ route('dashboard.index') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('Dashboard') }}</a>
                <a href="{{ route('transactions.index') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('Latex Monitoring') }}</a>
                <a href="{{ route('transactions.create') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('Create Transaction') }}</a>
                <a href="{{ route('plots.index') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('Plot Management') }}</a>
                <a href="{{ route('main.farmer.index') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('Farmers') }}</a>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}" class="block px-6 py-2 rounded hover:bg-green-100">{{ __('User Management') }}</a>
                @endif
            </nav>
        </div>

        <div class="px-6 mb-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 space-x-2 text-red-600 rounded bg-red-50 hover:bg-red-100">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex flex-col flex-1 min-h-screen md:ml-64">
        
        {{-- TOP BAR --}}
        <header class="sticky top-0 z-10 flex items-center justify-between h-16 px-8 bg-white shadow-sm">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="mr-4 text-green-700 md:hidden focus:outline-none">
                    <i class="text-2xl fas fa-bars"></i>
                </button>
                <h2 class="hidden text-sm font-medium text-gray-500 md:block">
                    {{ __('LATER-eX Decision Support System') }}
                </h2>
            </div>

            <div class="flex items-center space-x-4">
                {{-- Language Switcher --}}
                <div class="flex p-1 bg-gray-100 rounded-lg">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="px-3 py-1 text-xs font-bold rounded-md transition-all {{ app()->getLocale() == 'en' ? 'bg-white shadow text-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'th') }}" 
                       class="px-3 py-1 text-xs font-bold rounded-md transition-all {{ app()->getLocale() == 'th' ? 'bg-white shadow text-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        TH
                    </a>
                </div>
                
                <div class="w-px h-6 bg-gray-200"></div>

            </div>
        </header>

        {{-- Page Heading --}}
        @isset($header)
            <header class="text-white bg-green-600 shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>