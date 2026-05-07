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
        <div class="relative flex-col items-start justify-end hidden p-16 overflow-hidden text-white lg:flex lg:col-span-7">
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
                    <div class="flex items-center justify-center p-4 rounded-3xl w-96 h-96">
                        <img src="{{ asset('images/laterx-logo.png') }}" 
                            class="object-contain object-left w-full h-full" 
                            alt="LATER-X Logo">
                    </div>
                    {{-- Brand Name - Negative margin used to tuck it under the logo --}}
                    <span class="ml-4 -mt-10 text-6xl font-black tracking-tighter text-white drop-shadow-md">LATER-X</span>
                </div>

                {{-- Tagline --}}
                <div class="text-left">
                    <h1 class="mb-6 text-5xl font-extrabold leading-tight">
                        {{ __('Precision Tapping') }} <br>
                        <span class="text-green-400">{{ __('Smart Decisions.') }}</span>
                    </h1>
                    
                    {{-- Features list remains left-aligned for readability --}}
                    <div class="flex gap-8 text-sm font-medium opacity-80">
                        <div class="flex items-center gap-2">
                            <i class="text-green-400 fa-solid fa-cloud-sun"></i> {{ __('Weather Analytics') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="text-green-400 fa-solid fa-chart-line"></i> {{ __('Yield Prediction') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="text-green-400 fa-solid fa-microchip"></i> {{ __('DSS Engine') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: Login Interface --}}
        <div class="flex items-center justify-center p-8 lg:col-span-5">
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

                <div class="p-10 shadow-2xl glass-effect rounded-3xl shadow-green-900/10">
                    <div class="mb-10 text-center">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">{{ __('Sign In') }}</h2>
                        <p class="text-gray-500">{{ __('Manage your plantation data.') }}</p>
                    </div>

                    @if(session('error'))
                        <div class="flex items-center gap-3 p-4 mb-6 text-sm text-red-600 border border-red-100 rounded-xl bg-red-50">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block mb-2 text-xs font-bold tracking-wider text-gray-400 uppercase">{{ __('Email Address') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full py-3 pr-4 transition-all border-gray-200 outline-none pl-11 bg-gray-50 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10"
                                       placeholder="name@example.com">
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-xs font-bold tracking-wider text-gray-400 uppercase">{{ __('Password') }}</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-green-600 hover:text-green-700">
                                    {{ __('Forgot?') }}
                                </a>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" required
                                       class="w-full py-3 pr-4 transition-all border-gray-200 outline-none pl-11 bg-gray-50 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-5 h-5 text-green-600 transition-all border-gray-300 rounded-md focus:ring-green-500">
                            <span class="ml-3 text-sm text-gray-600 transition-colors group-hover:text-gray-900">{{ __('Keep me logged in') }}</span>
                        </label>

                        <button type="submit"
                                class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>{{ __('Sign In') }}</span>
                            <i class="text-sm fa-solid fa-arrow-right"></i>
                        </button>

                        <div class="relative py-4">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                            <div class="relative flex justify-center text-xs uppercase"><span class="px-4 text-gray-400 bg-white">{{ __('Or') }}</span></div>
                        </div>

                        <p class="text-sm text-center text-gray-500">
                            {{ __("New to LATER-X?") }}
                            <a href="{{ route('register') }}" class="font-extrabold text-green-600 underline hover:text-green-700 underline-offset-4">
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