@extends('layouts.app')

@section('title', __('Add Plot'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-3xl animate-fade-in">

    {{-- Success Alert --}}
    @if(session('success'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="fixed top-6 right-6 z-50 flex items-center bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-2xl transition transform duration-300 border border-emerald-400/30"
        x-transition:enter="transform ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transform ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
    >
        <svg class="w-5 h-5 mr-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill="currentColor" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
        </svg>
        <span class="text-sm font-bold">{{ __(session('success')) }}</span>
        <button @click="show = false" class="ml-4 text-white/80 hover:text-white font-black text-lg">&times;</button>
    </div>
    @endif

    {{-- Validation Errors Alert --}}
    @if ($errors->any())
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
        <p class="text-xs font-black uppercase tracking-wider mb-2 text-rose-600">{{ __('Please fix the following errors') }}:</p>
        <ul class="list-disc pl-5 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ __($error) }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100">
        {{-- Header Section --}}
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Add New Plot') }}
                </h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                    {{ __('Register a new land plot entry') }}
                </p>
            </div>
            <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                <i class="fa-solid fa-map-location-dot text-2xl"></i>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('plots.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Farmer Selection --}}
            <div>
                <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                    {{ __('Farmer') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="farmer_id" required
                        class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm appearance-none">
                        <option value="" disabled {{ old('farmer_id') ? '' : 'selected' }}>{{ __('Select Farmer') }}</option>
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                {{ $farmer->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-user-gear absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            {{-- Plot Size & Location --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Plot Size --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                        {{ __('Plot Size (Rai)') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="plot_size_rai" value="{{ old('plot_size_rai') }}" required
                            placeholder="0.00"
                            class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm">
                        <i class="fa-solid fa-[#000] fa-chart-area absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                {{-- Plot Location --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                        {{ __('Plot Location') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="plot_location" value="{{ old('plot_location') }}" required
                            placeholder="{{ __('Enter land location') }}"
                            class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                    {{ __('Notes') }}
                </label>
                <div class="relative">
                    <textarea name="notes" rows="4"
                        placeholder="{{ __('Additional details or descriptions...') }}"
                        class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm">{{ old('notes') }}</textarea>
                    <i class="fa-solid fa-note-sticky absolute left-4 top-4 text-gray-400"></i>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('plots.index') }}"
                    class="w-1/3 py-4 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-black uppercase tracking-widest text-xs rounded-2xl transition-all">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                    class="w-2/3 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-xl shadow-emerald-200 flex items-center justify-center">
                    <i class="fa-solid fa-check-circle mr-2"></i>{{ __('Save Plot') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection