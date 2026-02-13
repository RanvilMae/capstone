@extends('layouts.app')

@section('title', __('Add Farmer'))

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6 max-w-2xl mx-auto">
        {{-- Page Header --}}
        <h1 class="text-3xl font-extrabold text-green-700">{{ __('Add New Farmer') }}</h1>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 7000)" 
                class="fixed top-6 right-6 z-50 flex flex-col bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg space-y-2 transition transform duration-300"
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
            >
                <div class="flex justify-between items-center">
                    <strong class="font-semibold">{{ __('Please fix the following errors') }}:</strong>
                    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">&times;</button>
                </div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Farmer Form --}}
        <form action="{{ route('farmer.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name & Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                </div>
            </div>

            {{-- Phone & Address --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Address') }}</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
            </div>

            {{-- Farm Location & Size --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Farm Location') }}</label>
                    <input type="text" name="farm_location" value="{{ old('farm_location') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Farm Size (ha)') }}</label>
                    <input type="number" step="0.01" name="farm_size" value="{{ old('farm_size') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Notes') }}</label>
                <textarea name="notes"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                    rows="4">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition duration-300">
                {{ __('Add Farmer') }}
            </button>
        </form>
    </div>
</div>
@endsection