<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LATER-X') }} - {{ __('Register') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        
        {{-- Language Switcher --}}
        <div class="flex justify-end mb-6 space-x-3">
            <a href="{{ route('lang.switch', 'en') }}" 
               class="text-xs font-bold tracking-widest px-3 py-1 rounded-full transition-all {{ app()->getLocale() == 'en' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:text-green-600 border border-gray-200 bg-white' }}">
                EN
            </a>
            <a href="{{ route('lang.switch', 'th') }}" 
               class="text-xs font-bold tracking-widest px-3 py-1 rounded-full transition-all {{ app()->getLocale() == 'th' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:text-green-600 border border-gray-200 bg-white' }}">
                TH
            </a>
        </div>

        {{-- Form Container --}}
        <div class="p-10 glass-effect rounded-3xl shadow-2xl shadow-green-900/10 border border-gray-100">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Create Account') }}</h2>
                <p class="text-gray-500 text-sm">{{ __('Join LATER-X to manage your plantation.') }}</p>
            </div>

            {{-- Success Session Banner --}}
            @if(session('status'))
                <div class="p-4 mb-6 text-sm text-green-700 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <div>
                        <p class="font-bold">{{ __('Registration Received!') }}</p>
                        <p class="text-xs">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            {{-- Info Banner: Administrator Approval Notice --}}
            <div class="p-4 mb-6 text-xs text-blue-700 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                <div>
                    <span class="font-bold">{{ __('Please note:') }}</span>
                    <span>{{ __('Accounts require administrator approval before you can log in.') }}</span>
                </div>
            </div>

            {{-- Validation Errors Alert --}}
            @if ($errors->any())
                <div class="p-4 mb-6 text-xs text-red-600 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ __('Please fix the following errors:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name Field Group --}}
                <div>
                    <label id="name-label" for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Name') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               autocomplete="name" aria-labelledby="name-label"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none @error('name') border-red-500 @enderror"
                               placeholder="{{ __('John Doe') }}">
                    </div>
                </div>

                {{-- Email Field Group --}}
                <div>
                    <label id="email-label" for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               autocomplete="username" aria-labelledby="email-label"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none @error('email') border-red-500 @enderror"
                               placeholder="name@example.com">
                    </div>
                </div>

                {{-- Password Field Group --}}
                <div>
                    <label id="password-label" for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Password') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               autocomplete="new-password" aria-labelledby="password-label"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Confirm Password Field Group --}}
                <div>
                    <label id="password-confirmation-label" for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               autocomplete="new-password" aria-labelledby="password-confirmation-label"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 mt-2">
                    <span>{{ __('Register') }}</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>

                {{-- Divider --}}
                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-gray-400">{{ __('Or') }}</span></div>
                </div>

                {{-- Login Link --}}
                <p class="text-center text-sm text-gray-500">
                    {{ __("Already have an account?") }}
                    <a href="{{ route('login') }}" class="font-extrabold text-green-600 hover:text-green-700 underline underline-offset-4">
                        {{ __('Sign In') }}
                    </a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>