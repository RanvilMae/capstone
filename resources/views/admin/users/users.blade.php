@extends('layouts.app')

@section('title', __('User Management'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-7xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header & Primary Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('User Management') }}
                </h1>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                    {{ __('Manage platform access, roles, and user approvals.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create-user') }}" 
                   class="inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200 group">
                    <i class="fa-solid fa-plus mr-2 text-xs transition-transform duration-200 group-hover:scale-110"></i>
                    {{ __('Add User') }}
                </a>
            </div>
        </div>

        {{-- Quick Stats Overview --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 text-emerald-700 flex items-center justify-center text-xl font-black shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">{{ __('Total Users') }}</span>
                    <h3 class="text-2xl font-black text-gray-800">
                        {{ method_exists($users, 'total') ? number_format($users->total()) : number_format($users->count()) }}
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

        {{-- Search & Filter Toolbar --}}
        <form method="GET" action="{{ route('admin.users') }}" class="p-4 bg-gray-50/80 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="w-full md:w-96 relative">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search by name, email, or role...') }}" 
                        class="w-full pl-10 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    @if(request('search'))
                        <a href="{{ route('admin.users') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
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
                    <a href="{{ route('admin.users') }}" class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>

        {{-- Users Table --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4">{{ __('User Info') }}</th>
                        <th class="px-6 py-4">{{ __('Role') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150 group @if($user->trashed()) bg-gray-50/50 @endif">
                            
                            {{-- User Name & Email --}}
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-black flex items-center justify-center shrink-0 uppercase text-xs border border-emerald-200">
                                        {{ mb_substr($user->name ?? 'U', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-emerald-700 transition-colors flex items-center gap-2">
                                            <span>{{ $user->name }}</span>
                                            @if($user->trashed())
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 border border-rose-200">
                                                    {{ __('Trashed') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-gray-500 font-normal text-[11px]">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200">
                                    <i class="fa-solid fa-user-shield mr-1.5 text-gray-400"></i>
                                    {{ __(ucfirst($user->role)) }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                @if($user->trashed())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-xmark mr-1"></i> {{ __('Rejected') }}
                                    </span>
                                @elseif($user->is_approved && $user->approved)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-1 text-emerald-500"></i> {{ __('Approved') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-hourglass-half mr-1 text-amber-500"></i> {{ __('Pending') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Action Controls --}}
                            <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if(!$user->trashed())
                                        {{-- Approve --}}
                                        @if(!($user->is_approved && $user->approved))
                                            <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        title="{{ __('Approve User') }}"
                                                        class="p-2 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-xl transition duration-150">
                                                    <i class="fa-solid fa-check text-sm"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           title="{{ __('Edit User') }}"
                                           class="p-2 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-xl transition duration-150">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>

                                        {{-- Reject / Delete --}}
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="{{ __('Reject User') }}"
                                                    class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150"
                                                    onclick="return confirm('{{ __('Are you sure you want to reject this user?') }}')">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Restore --}}
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="{{ __('Restore User') }}"
                                                    class="p-2 text-sky-600 hover:text-sky-800 hover:bg-sky-50 rounded-xl transition duration-150">
                                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-user-slash"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">{{ __('No users found') }}</p>
                                    <p class="text-xs text-gray-400">{{ __('Try adjusting your search criteria or register a new user.') }}</p>
                                    <a href="{{ route('admin.users.create-user') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-600 hover:text-emerald-700">
                                        <i class="fa-solid fa-plus text-[10px]"></i> {{ __('Add User') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($users, 'links'))
            <div class="pt-2">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection