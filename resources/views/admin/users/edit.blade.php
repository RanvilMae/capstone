@extends('layouts.app')

@section('title', __('Edit User'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-4xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header & Back Navigation --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                        {{ __('Edit User') }}
                    </h1>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                        {{ __('Update account details and permissions') }}
                    </p>
                </div>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-extrabold uppercase tracking-wider ml-2">
                    {{ __($user->role) }}
                </span>
            </div>
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                {{ __('Back') }}
            </a>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)" 
                 class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-sm font-bold">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ __(session('success')) }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-black">&times;</button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 7000)" 
                 class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>{{ __('Please fix the following errors') }}:</span>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-black">&times;</button>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 font-medium pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Edit User Form --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Name --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                    {{ __('Name') }} <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       placeholder="{{ __('Full Name') }}"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                       required>
            </div>

            {{-- Email & Role Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        {{ __('Email') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           placeholder="name@example.com"
                           class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                           required>
                </div>
                
                {{-- Role Select --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        {{ __('Role') }} <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" 
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                            required>
                        @foreach(['admin', 'staff', 'director', 'farmer'] as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                {{ __(ucfirst($role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Optional Password Change Section --}}
            <div class="bg-gray-50/80 p-5 rounded-2xl border border-dashed border-gray-300 space-y-4" x-data="{ defaultPass: 'laterx123' }">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-lock text-emerald-600"></i>
                            {{ __('Change Password') }}
                        </h3>
                        <p class="text-[11px] font-semibold text-gray-500 mt-0.5">
                            {{ __('Leave blank to keep current password') }}
                        </p>
                    </div>
                    
                    <button type="button" 
                            @click="$refs.passInput.value = defaultPass; $refs.confirmInput.value = defaultPass;"
                            class="text-xs font-bold bg-amber-50 border border-amber-200 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-100 transition inline-flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-key text-amber-600"></i>
                        {{ __('Set Default (laterx123)') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600">{{ __('New Password') }}</label>
                        <input type="password" 
                               name="password" 
                               x-ref="passInput"
                               placeholder="{{ __('Leave blank to keep current') }}"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600">{{ __('Confirm Password') }}</label>
                        <input type="password" 
                               name="password_confirmation" 
                               x-ref="confirmInput"
                               placeholder="{{ __('Leave blank to keep current') }}"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200">
                    <i class="fa-solid fa-floppy-disk mr-2 text-xs"></i>
                    {{ __('Save Changes') }}
                </button>
                <a href="{{ route('admin.users') }}" 
                   class="w-full sm:w-auto px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all text-center">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection