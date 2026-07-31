@extends('layouts.app')

@section('title', __('Plots List'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-7xl animate-fade-in">

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

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Plots List') }}
                </h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                    {{ __('Manage registered rubber land plots') }}
                </p>
            </div>
            
            <a href="{{ route('plots.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200 group">
                <i class="fa-solid fa-plus mr-2 text-sm transition-transform duration-200 group-hover:scale-110"></i> 
                {{ __('Add Plot') }}
            </a>
        </div>

        {{-- Search & Filter Bar --}}
        <form method="GET" action="{{ route('plots.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 bg-gray-50/80 rounded-2xl border border-gray-100">
            {{-- Search Keyword Input --}}
            <div class="md:col-span-8 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by plot code, location, or notes...') }}"
                    class="w-full pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none text-xs font-bold text-gray-700 placeholder-gray-400 transition"
                >
            </div>

            {{-- Action Buttons --}}
            <div class="md:col-span-4 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-emerald-100 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    {{ __('Search') }}
                </button>

                @if(request()->has('search') && request('search') !== '')
                    <a href="{{ route('plots.index') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-600 font-bold text-xs rounded-xl transition flex items-center justify-center" title="{{ __('Clear Search') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Table Container --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4">{{ __('Plot Code') }}</th>
                        <th class="px-6 py-4">{{ __('Farmer') }}</th>
                        <th class="px-6 py-4">{{ __('Location') }}</th>
                        <th class="px-6 py-4">{{ __('Plot Size (Rai)') }}</th>
                        <th class="px-6 py-4">{{ __('Notes') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-medium text-gray-700">
                    @forelse($plots as $plot)
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                            {{-- Plot Code --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono">
                                    <i class="fa-solid fa-hashtag mr-1 text-[10px] opacity-70"></i>
                                    {{ $plot->code ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Farmer Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($plot->farmer->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $plot->farmer->name ?? __('Unknown') }}</span>
                                </div>
                            </td>

                            {{-- Location --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="fa-solid fa-location-dot text-emerald-500 mr-2 text-xs"></i>
                                    <span>{{ $plot->plot_location }}</span>
                                </div>
                            </td>

                            {{-- Plot Size --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">{{ number_format($plot->plot_size_rai, 2) }}</span>
                                <span class="text-xs text-gray-400 font-semibold uppercase ml-0.5">{{ __('Rai') }}</span>
                            </td>

                            {{-- Notes --}}
                            <td class="px-6 py-4 max-w-xs truncate text-gray-500 text-xs">
                                {{ $plot->notes ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('plots.edit', $plot) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 text-amber-600 bg-amber-50 hover:bg-amber-500 hover:text-white rounded-xl transition-all duration-200 shadow-sm"
                                       title="{{ __('Edit Plot') }}">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('plots.destroy', $plot) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 text-rose-600 bg-rose-50 hover:bg-rose-500 hover:text-white rounded-xl transition-all duration-200 shadow-sm"
                                                title="{{ __('Delete Plot') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this plot?') }}')">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">{{ __('No plots found.') }}</p>
                                    <p class="text-xs text-gray-400">
                                        @if(request()->filled('search'))
                                            {{ __('No results matching your query.') }}
                                        @else
                                            {{ __('Get started by creating a new plot entry.') }}
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($plots, 'links'))
            <div class="pt-4">
                {{ $plots->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>
@endsection