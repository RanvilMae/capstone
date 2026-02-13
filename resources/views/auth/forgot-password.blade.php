<x-guest-layout>
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
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
