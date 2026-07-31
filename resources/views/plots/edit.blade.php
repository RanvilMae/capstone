@extends('layouts.app')

@section('title', __('Edit Plot'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-4xl animate-fade-in">

    {{-- Success Alert --}}
    @if(session('success'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="fixed top-6 right-6 z-50 flex items-center bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-2xl transition border border-emerald-400/30"
    >
        <svg class="w-5 h-5 mr-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill="currentColor" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
        </svg>
        <span class="text-sm font-bold">{{ __(session('success')) }}</span>
        <button @click="show = false" class="ml-4 text-white/80 hover:text-white font-black text-lg">&times;</button>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Edit Plot') }}
                </h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                    {{ __('Update plot details') }} #{{ $plot->code ?? $plot->id }}
                </p>
            </div>
            
            <a href="{{ route('plots.index') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> {{ __('Back to List') }}
            </a>
        </div>

        {{-- Edit Form --}}
        <form action="{{ route('plots.update', $plot) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Farmer Select --}}
            <div>
                <label for="farmer_id" class="block text-xs font-black uppercase text-gray-500 mb-2">
                    {{ __('Farmer') }} <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="farmer_id" 
                    id="farmer_id" 
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm font-medium text-gray-800 @error('farmer_id') border-rose-500 @enderror"
                    required
                >
                    <option value="">{{ __('Select Farmer') }}</option>
                    @foreach($farmers as $farmer)
                        <option value="{{ $farmer->id }}" {{ old('farmer_id', $plot->farmer_id) == $farmer->id ? 'selected' : '' }}>
                            {{ $farmer->name }}
                        </option>
                    @endforeach
                </select>
                @error('farmer_id')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Plot Location --}}
            <div>
                <label for="plot_location" class="block text-xs font-black uppercase text-gray-500 mb-2">
                    {{ __('Plot Location') }} <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="plot_location" 
                    id="plot_location" 
                    value="{{ old('plot_location', $plot->plot_location) }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm font-medium text-gray-800 @error('plot_location') border-rose-500 @enderror"
                    required
                >
                @error('plot_location')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Plot Size --}}
            <div>
                <label for="plot_size_rai" class="block text-xs font-black uppercase text-gray-500 mb-2">
                    {{ __('Plot Size (Rai)') }} <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="plot_size_rai" 
                    id="plot_size_rai" 
                    value="{{ old('plot_size_rai', $plot->plot_size_rai) }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm font-medium text-gray-800 @error('plot_size_rai') border-rose-500 @enderror"
                    required
                >
                @error('plot_size_rai')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-xs font-black uppercase text-gray-500 mb-2">
                    {{ __('Notes') }}
                </label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="3"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm font-medium text-gray-800"
                >{{ old('notes', $plot->notes) }}</textarea>
                @error('notes')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('plots.index') }}" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider rounded-xl transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-200 transition">
                    {{ __('Update Plot') }}
                </button>
            </div>
        </form>

    </div>
</div>
@endsection