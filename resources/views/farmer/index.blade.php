@extends('layouts.app')

@section('title', __('Farmers List'))

@section('content')
<div class="container mx-auto p-6">

    <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <h1 class="text-3xl font-extrabold text-green-700 mb-4 md:mb-0">{{ __('Farmers List') }}</h1>
            <a href="{{ route('farmer.create') }}"
               class="inline-flex items-center px-5 py-2 text-white bg-green-600 rounded-xl hover:bg-green-700 shadow transition duration-300">
                <i class="fa-solid fa-plus mr-2"></i> {{ __('Add Farmer') }}
            </a>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)" 
            class="fixed top-6 right-6 z-50 flex items-center bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transition transform duration-300"
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

        {{-- Farmers Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-xl">
                <thead class="bg-green-600 text-white uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Email') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Phone') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Address') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($farmers as $farmer)
                        <tr class="hover:bg-green-50 transition duration-200">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $farmer->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->email }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->phone }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->address }}</td>
                            <td class="px-6 py-3 flex justify-center gap-2 flex-wrap">
                                {{-- Edit --}}
                                <a href="{{ route('farmer.edit', $farmer) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-200">
                                    <i class="fa-solid fa-pen-to-square"></i> {{ __('Edit') }}
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('farmer.destroy', $farmer) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-200"
                                        onclick="return confirm('{{ __('Are you sure you want to delete this farmer?') }}')">
                                        <i class="fa-solid fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No farmers found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection