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
                <select name="year" onchange="this.form.submit()" class="w-full p-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs font-bold text-gray-700">
                    <option value="">{{ __('All Production Years') }}</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }} ({{ $year + 543 }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-emerald-100 flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter text-xs"></i>
                {{ __('Filter') }}
            </button>
        </form>

        {{-- Main Transactions Section --}}
        <div class="space-y-4">
            <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm bg-white">
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
                                        {{ $t->plot?->plot_location ?? $t->location ?? __('N/A') }}
                                    </div>
                                    @php
                                        $topCode = $t->plot?->code ?? $t->plot_code ?? $t->code ?? null;
                                    @endphp
                                    @if(!empty($topCode))
                                        <span class="inline-block px-2 py-0.5 mt-1 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded">
                                            #{{ $topCode }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-gray-700">
                                    {{ 
                                        $t->farmer->name 
                                        ?? $t->plot->farmer->name 
                                        ?? $t->farmer_name 
                                        ?? $t->plot->farmer_name 
                                        ?? __('N/A') 
                                    }}
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

            {{-- Transactions Pagination Links --}}
            @if(method_exists($transactions, 'links'))
                <div class="pt-2">
                    {{ $transactions->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- PROMINENT SEPARATOR DIVIDER --}}
        <div class="relative py-8">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t-2 border-dashed border-gray-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 text-xs font-black uppercase tracking-widest text-gray-400 border border-gray-100 rounded-full py-1 shadow-sm">
                    <i class="fa-solid fa-layer-group text-emerald-500 mr-1.5"></i> Summary Breakdown
                </span>
            </div>
        </div>

        {{-- Totals per Plot Summary Section (Isolated Container Card) --}}
        <div class="bg-slate-50/70 p-6 md:p-7 rounded-3xl border border-slate-200/80 space-y-5 shadow-inner">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-chart-pie text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 tracking-tight">
                            {{ __('Totals per Plot') }}
                        </h3>
                        <p class="text-xs text-gray-500 font-medium">
                            {{ __('Aggregated production totals and financial revenue grouped by land plot') }}
                        </p>
                    </div>
                </div>

                @if(method_exists($totals, 'total'))
                    <span class="inline-flex items-center px-3 py-1 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 shadow-sm self-start sm:self-auto">
                        {{ __('Showing :count of :total plots', ['count' => $totals->count(), 'total' => number_format($totals->total())]) }}
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100/80 border-b border-gray-200 text-[11px] font-black uppercase tracking-wider text-gray-500">
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
                                    <div class="font-bold text-gray-800">
                                        {{ $summary->plot_location ?? __('N/A') }}
                                    </div>
                                    @php
                                        $summaryCode = $summary->plot_code ?? $summary->code ?? null;
                                    @endphp
                                    @if(!empty($summaryCode))
                                        <span class="inline-block px-2 py-0.5 mt-1 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded">
                                            #{{ $summaryCode }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-gray-600 font-semibold">
                                    {{ $summary->farmer_name ?? __('N/A') }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-black text-emerald-600 text-sm">
                                    {{ number_format($summary->total_dry_rubber ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-black text-gray-900 text-sm">
                                    ฿{{ number_format($summary->total_income ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-bold">
                                    {{ __('No plot summary data available.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Totals per Plot Pagination Links --}}
            @if(method_exists($totals, 'links'))
                <div class="pt-2">
                    {{ $totals->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection