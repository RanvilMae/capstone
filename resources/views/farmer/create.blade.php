@extends('layouts.app')

@section('title', __('Add Farmer'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-2xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Page Header --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Add New Farmer') }}
                </h1>
            </div>
            <a href="{{ route('main.farmer.index') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1"></i> {{ __('Cancel') }}
            </a>
        </div>

        {{-- Validation Errors Toast --}}
        @if ($errors->any())
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 7000)" 
                class="fixed top-6 right-6 z-50 flex flex-col bg-rose-600 text-white px-5 py-4 rounded-2xl shadow-xl space-y-2 transition transform duration-300 max-w-md"
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
            >
                <div class="flex justify-between items-center border-b border-rose-500/50 pb-2">
                    <strong class="font-bold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ __('Please fix the following errors') }}:
                    </strong>
                    <button @click="show = false" class="ml-4 text-white hover:text-rose-200 text-lg font-black">&times;</button>
                </div>
                <ul class="list-disc list-inside text-xs font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Farmer Form --}}
        <form action="{{ route('main.farmer.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name & Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Name') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="{{ __('Enter full name') }}"
                           class="w-full p-3 bg-gray-50/50 border @error('name') border-rose-500 @else border-gray-200 @enderror rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                           required>
                    @error('name')
                        <p class="mt-1 text-[11px] text-rose-500 font-bold">{{ __($message) }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Email') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="{{ __('Enter email address') }}"
                           class="w-full p-3 bg-gray-50/50 border @error('email') border-rose-500 @else border-gray-200 @enderror rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                           required>
                    @error('email')
                        <p class="mt-1 text-[11px] text-rose-500 font-bold">{{ __($message) }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phone & Address --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Phone') }}
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           placeholder="{{ __('Enter phone number') }}"
                           class="w-full p-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Address') }}
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="{{ __('Enter full address') }}"
                           class="w-full p-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                </div>
            </div>

            {{-- Farm Location & Size --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Farm Location') }}
                    </label>
                    <input type="text" name="farm_location" value="{{ old('farm_location') }}"
                           placeholder="{{ __('Enter farm location') }}"
                           class="w-full p-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                        {{ __('Farm Size (ha)') }}
                    </label>
                    <input type="number" step="0.01" name="farm_size" value="{{ old('farm_size') }}"
                           placeholder="{{ __('Enter area size') }}"
                           class="w-full p-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block mb-2 text-xs font-black text-gray-700 uppercase tracking-wider">
                    {{ __('Notes') }}
                </label>
                <textarea name="notes" rows="4"
                          placeholder="{{ __('Enter additional notes...') }}"
                          class="w-full p-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('main.farmer.index') }}" 
                   class="w-1/3 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xs uppercase tracking-wider rounded-xl transition text-center">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="w-2/3 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-200 transition duration-300">
                    {{ __('Add Farmer') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection