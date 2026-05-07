@extends('layouts.app')

@section('title', __('Farmers List'))

@section('content')
<div class="container p-6 mx-auto">

    <div class="p-8 space-y-6 bg-white shadow-xl rounded-2xl">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <h1 class="mb-4 text-3xl font-extrabold text-green-700 md:mb-0">{{ __('Farmers List') }}</h1>
            <a href="{{ route('main.farmer.create') }}"
               class="inline-flex items-center px-5 py-2 text-white transition duration-300 bg-green-600 shadow rounded-xl hover:bg-green-700">
                <i class="mr-2 fa-solid fa-plus"></i> {{ __('Add Farmer') }}
            </a>
        </div>

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

        {{-- Farmers Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-xl">
                <thead class="text-sm text-white uppercase bg-green-600">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Email') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Farm Location') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Farm Size') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm bg-white divide-y divide-gray-200">
                    @forelse($farmers as $farmer)
                        <tr class="transition duration-200 hover:bg-green-50">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $farmer->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->email }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->farm_location }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $farmer->farm_size }}</td>
                            <td class="flex flex-wrap justify-center gap-2 px-6 py-3">
                                {{-- Edit --}}
                                <a href="{{ route('main.farmer.edit', $farmer) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 text-white transition duration-200 bg-blue-600 rounded-lg hover:bg-blue-700">
                                    <i class="fa-solid fa-pen-to-square"></i> {{ __('Edit') }}
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('main.farmer.destroy', $farmer) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-white transition duration-200 bg-red-600 rounded-lg hover:bg-red-700"
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