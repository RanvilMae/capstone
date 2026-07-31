@extends('layouts.app')

@section('title', __('Latex Transactions'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-7xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header & Action Buttons --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Latex Transactions') }}
                </h1>
                @if(isset($transactions) && method_exists($transactions, 'total'))
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                        {{ __('Showing :count of :total total transactions', ['count' => $transactions->count(), 'total' => number_format($transactions->total())]) }}
                    </p>
                @endif
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('latex.import') }}" 
                   class="inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200 group">
                    <i class="fa-solid fa-file-import mr-2 text-sm transition-transform duration-200 group-hover:-translate-y-0.5"></i>
                    {{ __('Import Latex Data') }}
                </a>
            </div>
        </div>

        {{-- Success & Error Alert Messages --}}
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

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
                <ul class="list-disc pl-5 text-xs font-bold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter Form --}}
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50/80 rounded-2xl border border-gray-100">
            <div>
                <select name="plot_id" class="w-full p-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs font-bold text-gray-700">
                    <option value="">{{ __('All Plots') }}</option>
                    @foreach($plots as $plot)
                        <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                            [{{ $plot->code ?? __('NO-CODE') }}] {{ $plot->plot_location }} - {{ $plot->farmer->name ?? __('N/A') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="farmer_id" class="w-full p-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs font-bold text-gray-700">
                    <option value="">{{ __('All Farmers') }}</option>
                    @foreach($farmers as $farmer)
                        <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                            {{ $farmer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="production_year_id" class="w-full p-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs font-bold text-gray-700">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach($years ?? [] as $year)
                        <option value="{{ $year->id }}" {{ request('production_year_id') == $year->id ? 'selected' : '' }}>
                            {{ __('FY') }} {{ $year->year_label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-emerald-100 flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter text-xs"></i>
                {{ __('Filter') }}
            </button>
        </form>

        {{-- Main Transactions Table --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black uppercase tracking-wider text-gray-400">
                        <th class="px-4 py-4">{{ __('Date') }}</th>
                        <th class="px-4 py-4">{{ __('Plot') }}</th>
                        <th class="px-4 py-4">{{ __('Farmer') }}</th>
                        <th class="px-4 py-4 text-right">{{ __('Volume (kg)') }}</th>
                        <th class="px-4 py-4 text-right">{{ __('DRC (%)') }}</th>
                        <th class="px-4 py-4 text-right">{{ __('Dry Rubber (kg)') }}</th>
                        <th class="px-4 py-4 text-right">{{ __('Price/kg') }}</th>
                        <th class="px-4 py-4 text-right">{{ __('Total Amount') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('Quality Status') }}</th>
                        <th class="px-4 py-4">{{ __('Entered By') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                            <td class="px-4 py-3.5 whitespace-nowrap font-bold text-gray-800">
                                {{ substr($t->transaction_date, 0, 10) }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-gray-800">
                                    {{ $t->plot->plot_location ?? $t->location ?? __('N/A') }}
                                </div>
                                <div class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-mono font-black bg-emerald-50 text-emerald-700 border border-emerald-200/50">
                                    #{{ $t->plot->code ?? $t->plot_code ?? $t->code ?? __('N/A') }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-gray-700">
                                {{ $t->plot->farmer->name ?? $t->farmer_name ?? __('N/A') }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap font-bold text-gray-800">
                                {{ number_format($t->volume_kg ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap font-black text-sky-600">
                                {{ number_format($t->dry_rubber_content ?? 0, 2) }}%
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap font-black text-emerald-600">
                                {{ number_format($t->dry_rubber_weight_kg ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap font-semibold text-gray-600">
                                ฿{{ number_format($t->price_per_kg ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap font-black text-gray-900 text-sm">
                                ฿{{ number_format($t->total_amount ?? 0, 2) }}
                            </td>
                            
                            {{-- Dynamic Quality Classification --}}
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php
                                    $qStr = strtolower($t->quality_classification ?? '');
                                    $drc = $t->dry_rubber_content ?? 0;
                                    
                                    if (str_contains($qStr, 'high') || $drc >= 35) {
                                        $label = $t->quality_classification ?? 'High Quality';
                                        $color = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    } elseif (str_contains($qStr, 'standard') || ($drc >= 28 && $drc < 35)) {
                                        $label = $t->quality_classification ?? 'Standard Quality';
                                        $color = 'bg-sky-50 text-sky-700 border-sky-200';
                                    } elseif (str_contains($qStr, 'low') || ($drc > 0 && $drc < 28)) {
                                        $label = $t->quality_classification ?? 'Low Quality';
                                        $color = 'bg-rose-50 text-rose-700 border-rose-200';
                                    } else {
                                        $label = 'Unclassified';
                                        $color = 'bg-gray-50 text-gray-600 border-gray-200';
                                    }
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $color }}">
                                    {{ __($label) }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-500 font-medium">
                                {{ $t->user->name ?? $t->entered_by ?? __('System') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">{{ __('No transactions recorded yet.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if(method_exists($transactions, 'links'))
            <div class="pt-2">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif

        {{-- Totals per Plot Summary Section --}}
        <div class="pt-6 border-t border-gray-100">
            <h3 class="mb-4 text-lg font-black text-gray-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-600"></i>
                {{ __('Totals per Plot') }}
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black uppercase tracking-wider text-gray-400">
                            <th class="px-6 py-4">{{ __('Plot') }}</th>
                            <th class="px-6 py-4">{{ __('Farmer') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Dry Rubber (kg)') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Total Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                        @forelse($totals as $summary)
                            <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                                <td class="px-6 py-3.5">
                                    <div class="font-bold text-gray-800">{{ $summary->plot_location ?? __('N/A') }}</div>
                                    @if(isset($summary->plot_code) || isset($summary->code))
                                        <div class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[10px] font-mono font-black bg-emerald-50 text-emerald-700 border border-emerald-200/50">
                                            #{{ $summary->plot_code ?? $summary->code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-gray-600 font-semibold">{{ $summary->farmer_name ?? __('N/A') }}</td>
                                <td class="px-6 py-3.5 text-right font-black text-emerald-600 text-sm">
                                    {{ number_format($summary->total_dry_rubber ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-black text-gray-900 text-sm">
                                    ฿{{ number_format($summary->total_income ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-400 font-bold">
                                    {{ __('No plot summary data.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection