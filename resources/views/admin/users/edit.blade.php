@extends('layouts.app')

@section('title', __('Edit User'))

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6 max-w-2xl mx-auto">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-extrabold text-green-700">{{ __('Edit User') }}</h1>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold uppercase">
                {{ $user->role }}
            </span>
        </div>

        {{-- Notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                class="bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
                <span>{{ __(session('success')) }}</span>
                <button @click="show = false">&times;</button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FIX 1: Form action must be .update --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PATCH')

            {{-- Name & Email --}}
            <div class="mt-2">
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" required>
            </div>

            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                </div>
                
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Role') }}</label>
                    <select name="role" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                        @foreach(['admin', 'staff', 'director', 'farmer'] as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                {{ __(ucfirst($role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- FIX 2: Added the Input Fields so Alpine.js has something to target --}}
            <div class="mt-4 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300" x-data="{ defaultPass: 'laterx123' }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-500 uppercase">{{ __('Change Password') }}</h3>
                    
                    <button type="button" 
                        @click="$refs.passInput.value = defaultPass; $refs.confirmInput.value = defaultPass; alert('Password fields filled with: laterx123')"
                        class="text-xs font-semibold bg-orange-100 text-orange-600 px-2 py-1 rounded hover:bg-orange-200 transition">
                        <i class="fa-solid fa-key mr-1"></i> {{ __('Set Default (laterx123)') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-600">{{ __('New Password') }}</label>
                        <input type="password" name="password" x-ref="passInput"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                            placeholder="Leave blank to keep current">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-600">{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" x-ref="confirmInput"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" 
                            placeholder="Leave blank to keep current">
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" 
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition duration-300 shadow-md">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection