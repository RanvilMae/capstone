<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-100">

    <div class="grid min-h-screen grid-cols-2">

        {{-- RIGHT PANEL (Login Card) --}}
        <div class="flex items-center justify-center p-4 md:p-6 bg-green-50">
            
            <div class="w-full max-w-[280px] sm:max-w-xs md:max-w-md p-4 sm:p-6 md:p-8 bg-white shadow-lg rounded-2xl">
                <img src="{{ asset('images/laterx-logo.png') }}" class="w-32 mx-auto sm:w-40 md:w-48 lg:w-56" alt="LATER-X Logo">
                
                <p class="mb-4 text-xs text-center text-gray-500 sm:text-sm">
                    Enter your credentials to access your dashboard
                </p>

                @if(session('error'))
                    <div class="p-2 mb-4 text-xs text-red-600 rounded sm:text-sm bg-red-50">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-3 sm:space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs text-gray-600 sm:text-sm">Email Address</label>
                        <input id="email" type="email" name="email" required autofocus
                               class="w-full mt-1 border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200">
                        @error('email')
                        <p class="mt-1 text-xs text-red-500 sm:text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs text-gray-600 sm:text-sm">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full mt-1 border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200">
                        @error('password')
                        <p class="mt-1 text-xs text-red-500 sm:text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex flex-col items-center justify-between text-xs sm:flex-row sm:text-sm">
                        <label class="flex items-center mb-2 sm:mb-0">
                            <input type="checkbox" name="remember" class="text-green-600 rounded focus:ring-green-500">
                            <span class="ml-2 text-gray-600">Remember me</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="text-green-600 hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                        Sign In
                    </button>

                    {{-- Register --}}
                    <p class="mt-4 text-xs text-center text-gray-600 sm:text-sm">
                        Don’t have an account?
                        <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:underline">
                            Register here
                        </a>
                    </p>
                </form>
            </div>
        </div>

        {{-- LEFT PANEL --}}
        <div class="flex flex-col items-center justify-center p-6 text-green-800 md:p-12 bg-gradient-to-br from-green-500 to-green-700">
                <h1 class="mb-2 text-xl font-bold text-center text-white md:text-3xl md:mb-4">
                    Welcome to LATER-X
                </h1>
                
                <p class="text-xs sm:text-sm md:text-base text-center text-white max-w-[220px] sm:max-w-sm md:max-w-md">
                    A smart decision support system for latex and rubber production, helping farmers make data-driven decisions.
                </p>
        </div>

    </div>

</body>
</html>
