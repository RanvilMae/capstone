@extends('layouts.app')

@section('title', __('Farmers List'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-7xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header & Action Buttons --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Farmers Management') }}
                </h1>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                    {{ __('View, search, and manage registered farmers and their land details.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('main.farmer.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200 group">
                    <i class="fa-solid fa-plus mr-2 text-xs transition-transform duration-200 group-hover:scale-110"></i>
                    {{ __('Add New Farmer') }}
                </a>
            </div>
        </div>

        {{-- Stats Quick View Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 text-emerald-700 flex items-center justify-center text-xl font-black shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">{{ __('Total Farmers') }}</span>
                    <h3 class="text-2xl font-black text-gray-800">
                        {{ method_exists($farmers, 'total') ? number_format($farmers->total()) : number_format($farmers->count()) }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- Success Alert Toast --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 text-sm font-bold">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ __(session('success')) }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-black">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 text-sm font-bold">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <span>{{ __(session('error')) }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-black">&times;</button>
            </div>
        @endif

        {{-- Search Toolbar --}}
        <form method="GET" action="{{ route('main.farmer.index') }}" class="p-4 bg-gray-50/80 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="w-full md:w-96 relative">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search by name, email, or location...') }}" 
                        class="w-full pl-10 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    @if(request('search'))
                        <a href="{{ route('main.farmer.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-circle-xmark text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-emerald-100 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter text-xs"></i>
                    {{ __('Search') }}
                </button>
                @if(request('search'))
                    <a href="{{ route('main.farmer.index') }}" class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>

        {{-- Farmers Table --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4">{{ __('Farmer Name') }}</th>
                        <th class="px-6 py-4">{{ __('Contact Info') }}</th>
                        <th class="px-6 py-4">{{ __('Farm Location') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('Farm Size') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                    @forelse($farmers as $farmer)
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150 group">
                            {{-- Name + Avatar --}}
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-black flex items-center justify-center shrink-0 uppercase text-xs border border-emerald-200">
                                        {{ mb_substr($farmer->name ?? 'F', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">
                                            {{ $farmer->name }}
                                        </div>
                                        @if(isset($farmer->code))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-mono font-black bg-gray-100 text-gray-600 border border-gray-200">
                                                #{{ $farmer->code }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-6 py-3.5 whitespace-nowrap text-gray-600 font-semibold">
                                @if($farmer->email)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-envelope text-gray-400 text-xs"></i>
                                        <span>{{ $farmer->email }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic font-normal">{{ __('N/A') }}</span>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="px-6 py-3.5 whitespace-nowrap text-gray-600 font-semibold">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-emerald-500 text-xs"></i>
                                    <span>{{ $farmer->farm_location ?? __('Not specified') }}</span>
                                </div>
                            </td>

                            {{-- Size --}}
                            <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-ruler-combined mr-1 text-emerald-500"></i>
                                    {{ $farmer->farm_size ? $farmer->farm_size . ' ' . __('Rai') : __('N/A') }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Edit --}}
                                    <a href="{{ route('main.farmer.edit', $farmer) }}"
                                       title="{{ __('Edit Farmer') }}"
                                       class="p-2 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('main.farmer.destroy', $farmer) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="{{ __('Delete Farmer') }}"
                                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this farmer?') }}')">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-user-slash"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">{{ __('No farmers found') }}</p>
                                    <p class="text-xs text-gray-400">{{ __('Try adjusting your search criteria or add a new farmer.') }}</p>
                                    <a href="{{ route('main.farmer.create') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-600 hover:text-emerald-700">
                                        <i class="fa-solid fa-plus text-[10px]"></i> {{ __('Add Farmer') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($farmers, 'links'))
            <div class="pt-2">
                {{ $farmers->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection