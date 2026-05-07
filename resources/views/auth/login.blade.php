<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-100">

    <div class="grid min-h-screen grid-cols-1 md:grid-cols-2">

        {{-- LEFT PANEL (Visual/Info) --}}
        <div class="hidden md:flex flex-col items-center justify-center p-6 text-white md:p-12 bg-gradient-to-br from-green-500 to-green-700">
            <h1 class="mb-2 text-xl font-bold text-center md:text-4xl md:mb-4">
                {{ __('Welcome to LATER-X') }}
            </h1>
            <p class="text-sm md:text-lg text-center opacity-90 max-w-md">
                {{ __('A smart decision support system for latex and rubber production, helping farmers make data-driven decisions.') }}
            </p>
        </div>

        {{-- RIGHT PANEL (Login Card) --}}
        <div class="flex items-center justify-center p-4 md:p-6 bg-green-50">

            <div class="w-full max-w-[320px] sm:max-w-md p-6 sm:p-8 bg-white shadow-xl rounded-2xl">
                
                {{-- Language Switcher --}}
                <div class="flex justify-end mb-6 text-xs uppercase tracking-widest">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="{{ app()->getLocale() == 'en' ? 'text-green-600 font-bold border-b-2 border-green-600' : 'text-gray-400 hover:text-green-500' }} pb-1">
                        EN
                    </a>
                    <span class="mx-3 text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'th') }}" 
                       class="{{ app()->getLocale() == 'th' ? 'text-green-600 font-bold border-b-2 border-green-600' : 'text-gray-400 hover:text-green-500' }} pb-1">
                        TH
                    </a>
                </div>

                <img src="{{ asset('images/laterx-logo.png') }}" class="w-32 mx-auto mb-4 sm:w-40" alt="LATER-X Logo">
                
                <p class="mb-6 text-xs text-center text-gray-500 sm:text-sm">
                    {{ __('Enter your credentials to access your dashboard') }}
                </p>

                @if(session('error'))
                    <div class="p-3 mb-4 text-xs text-red-600 rounded-lg sm:text-sm bg-red-50 border border-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 sm:text-sm">{{ __('Email Address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full mt-1 border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-700 sm:text-sm">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required
                               class="w-full mt-1 border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="text-green-600 font-medium hover:underline">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-md shadow-green-200 transition transform active:scale-[0.98]">
                        {{ __('Sign In') }}
                    </button>

                    {{-- Register --}}
                    <p class="mt-6 text-xs text-center text-gray-600 sm:text-sm">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="font-bold text-green-600 hover:underline">
                            {{ __('Register here') }}
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>