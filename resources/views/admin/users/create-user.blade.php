@extends('layouts.app')

@section('title', __('Add New User'))

@section('content')
<div class="container p-6 mx-auto">
    <div class="max-w-2xl p-8 mx-auto space-y-6 bg-white shadow-xl rounded-2xl">
        {{-- Page Header --}}
        <h1 class="text-3xl font-extrabold text-green-700">{{ __('Add New User') }}</h1>

        {{-- Success Alert --}}
        @if(session('success'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)" 
                class="fixed z-50 flex items-center px-4 py-3 text-white transition duration-300 transform bg-green-600 rounded-lg shadow-lg top-6 right-6"
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
            >
                <svg class="w-5 h-5 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
                </svg>
                <span>{{ __(session('success')) }}</span>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">&times;</button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 7000)" 
                class="fixed z-50 flex flex-col px-4 py-3 space-y-2 text-white transition duration-300 transform bg-red-600 rounded-lg shadow-lg top-6 right-6"
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
            >
                <div class="flex items-center justify-between">
                    <strong class="font-semibold">{{ __('Please fix the following errors') }}:</strong>
                    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">&times;</button>
                </div>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add User Form --}}
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name & Email --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                        required>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                        required>
                </div>
                
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Role') }}</label>
                    <select name="role" 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                            required>
                        <option value="">{{ __('Select Role') }}</option>
                        <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>{{ __('Director') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                    </select>
                </div>
            </div>

            {{-- Role & Password --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Password') }}</label>
                    <input type="password" name="password" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                        required>
                </div>
                
            <div>
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                    required>
            </div>
            </div>


            {{-- Submit Button --}}
            <button type="submit" 
                class="w-full py-3 font-semibold text-white transition duration-300 bg-green-600 shadow-md rounded-xl hover:bg-green-700">
                {{ __('Add User') }}
            </button>
        </form>
    </div>
</div>
@endsection