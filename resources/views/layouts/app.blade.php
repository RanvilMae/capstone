<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="hidden w-64 text-white bg-green-600 shadow-md md:block">
        <div class="p-6">
            <img src="{{ asset('images/laterx-logo.png') }}" class="text-center w-28" alt="LATER-X Logo">
            <h2 class="text-2xl font-bold">{{ config('app.name', 'LATER-X') }}</h2>
        </div>
        <nav class="mt-6">
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('dashboard') }}" class="block px-6 py-2 rounded
                        {{ request()->routeIs('dashboard') ? 'bg-green-800 font-semibold' : 'hover:bg-green-700' }}">
                    Dashboard
                </a>

                <a href="{{ route('transactions.index') }}" class="block px-6 py-2 rounded
                        {{ request()->routeIs('transactions.index') ? 'bg-green-800 font-semibold' : 'hover:bg-green-700' }}">
                    Latex Monitoring
                </a>

                <a href="{{ route('admin.analytics') }}" class="block px-6 py-2 rounded
                        {{ request()->routeIs('admin.analytics') ? 'bg-green-800 font-semibold' : 'hover:bg-green-700' }}">
                    Analytics
                </a>
                
                <a href="{{ route('admin.farmer.index') }}"
                    class="block px-6 py-2 rounded
                                                        {{ request()->routeIs('admin.farmer.index') ? 'bg-green-800 font-semibold' : 'hover:bg-green-700' }}">
                    Farmers
                </a>

                <a href="{{ route('admin.users') }}" class="block px-6 py-2 rounded
                                        {{ request()->routeIs('admin.users') ? 'bg-green-800 font-semibold' : 'hover:bg-green-700' }}">
                    User Management
                </a>



            @else(auth()->user()->hasRole('staff'))
                <a href="{{ route('staff.dashboard') }}" class="block px-6 py-2 rounded hover:bg-green-700">Dashboard</a>
                <a href="{{ route('farmer.index') }}" class="block px-6 py-2 rounded hover:bg-green-700">Farmers</a>
                <a href="{{ route('farmer.farms') }}" class="block px-6 py-2 rounded hover:bg-green-700">My Farms</a>
            @endif

        </nav>
    </aside>


    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 md:hidden"></div>

    <!-- Main Content -->
    <div class="flex flex-col flex-1">
        <!-- Top Navigation / Header -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="text-white bg-green-600 shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="p-6 m-8">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
