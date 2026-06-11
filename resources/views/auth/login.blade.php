<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased bg-slate-50">

    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">

        {{-- LEFT PANEL: Brand & Visuals --}}
        <div class="relative hidden lg:flex lg:col-span-7 flex-col items-start justify-end p-16 text-white overflow-hidden">
            {{-- Background Image Overlay --}}
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/rubber.jpg') }}" 
                     class="object-cover w-full h-full brightness-[0.4]" 
                     alt="{{ __('Rubber Plantation') }}">
                <div class="absolute inset-0 bg-gradient-to-t from-green-900/90 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-10 w-full">
                {{-- Centered Logo and Brand Section --}}
                <div class="flex flex-col items-start mb-10 text-left">
                    {{-- Large Logo Container - Aligned to the start (left) --}}
                    <div class="p-4 rounded-3xl flex items-center justify-center w-96 h-96">
                        <img src="{{ asset('images/laterx-logo.png') }}" 
                             class="w-full h-full object-contain object-left" 
                             alt="LATER-X Logo">
                    </div>
                    {{-- Brand Name - Negative margin used to tuck it under the logo --}}
                    <span class="text-6xl font-black tracking-tighter text-white drop-shadow-md -mt-10 ml-4">LATER-X</span>
                </div>

                {{-- Tagline --}}
                <div class="text-left">
                    <h1 class="text-5xl font-extrabold leading-tight mb-6">
                        {{ __('Precision Tapping') }} <br>
                        <span class="text-green-400">{{ __('Smart Decisions.') }}</span>
                    </h1>
                    
                    {{-- Features list remains left-aligned for readability --}}
                    <div class="flex gap-8 text-sm font-medium opacity-80">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-cloud-sun text-green-400"></i> {{ __('Weather Analytics') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-green-400"></i> {{ __('Yield Prediction') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-microchip text-green-400"></i> {{ __('DSS Engine') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: Login Interface --}}
        <div class="lg:col-span-5 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                
                {{-- Language Switcher --}}
                <div class="flex justify-end mb-8 space-x-4">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="text-xs font-bold tracking-widest px-3 py-1 rounded-full {{ app()->getLocale() == 'en' ? 'bg-green-600 text-white' : 'text-gray-400 hover:text-green-600 border border-gray-200' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'th') }}" 
                       class="text-xs font-bold tracking-widest px-3 py-1 rounded-full {{ app()->getLocale() == 'th' ? 'bg-green-600 text-white' : 'text-gray-400 hover:text-green-600 border border-gray-200' }}">
                        TH
                    </a>
                </div>

                <div class="p-10 glass-effect rounded-3xl shadow-2xl shadow-green-900/10">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Sign In') }}</h2>
                        <p class="text-gray-500">{{ __('Manage your plantation data.') }}</p>
                    </div>

                    @if(session('error'))
                        <div class="p-4 mb-6 text-sm text-red-600 rounded-xl bg-red-50 border border-red-100 flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        {{-- Email Field Group --}}
                        <div>
                            <label id="email-label" for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Email Address') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                       autocomplete="username" aria-labelledby="email-label"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none"
                                       placeholder="name@example.com">
                            </div>
                        </div>

                        {{-- Password Field Group --}}
                        <div>
                            <div class="flex justify-between mb-2">
                                <label id="password-label" for="password" class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Password') }}</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-green-600 hover:text-green-700">
                                    {{ __('Forgot?') }}
                                </a>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                       autocomplete="current-password" aria-labelledby="password-label"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="remember" class="w-5 h-5 text-green-600 rounded-md border-gray-300 focus:ring-green-500 transition-all">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">{{ __('Keep me logged in') }}</span>
                        </label>

                        <button type="submit"
                                class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>{{ __('Sign In') }}</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>

                        <div class="relative py-4">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-gray-400">{{ __('Or') }}</span></div>
                        </div>

                        <p class="text-center text-sm text-gray-500">
                            {{ __("New to LATER-X?") }}
                            <a href="{{ route('register') }}" class="font-extrabold text-green-600 hover:text-green-700 underline underline-offset-4">
                                {{ __('Create Account') }}
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>