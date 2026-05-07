@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h2 class="mb-6 text-3xl font-extrabold text-green-700">{{ __('Latex Transactions') }}</h2>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <select name="plot_id" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                <option value="">{{ __('All Plots') }}</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                        {{ $plot->plot_location }} ({{ $plot->plot_size_rai }} {{ __('Rai') }}) - {{ $plot->farmer->name }}
                    </option>
                @endforeach
            </select>

            <select name="farmer_id" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                <option value="">{{ __('All Farmers') }}</option>
                @foreach($plots->pluck('farmer')->unique('id') as $farmer)
                    <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                        {{ $farmer->name }}
                    </option>
                @endforeach
            </select>

            <select name="production_year_id" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                <option value="">{{ __('All Years') }}</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ request('production_year_id') == $year->id ? 'selected' : '' }}>
                        {{ __('FY') }} {{ $year->year_label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors duration-300">
                {{ __('Filter') }}
            </button>
        </form>

        <div class="overflow-x-auto rounded-xl shadow-inner border border-gray-200">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-green-100 text-green-700">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Plot') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Farmer') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Volume (kg)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('DRC (%)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Dry Rubber (kg)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Price/kg') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Total Amount') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Entered By') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transactions as $t)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-2">{{ $t->transaction_date }}</td>
                            <td class="px-4 py-2">{{ $t->plot->plot_location }}</td>
                            <td class="px-4 py-2">{{ $t->plot->farmer->name }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($t->volume_kg, 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($t->dry_rubber_content, 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($t->volume_kg * ($t->dry_rubber_content / 100), 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($t->price_per_kg, 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($t->total_amount, 2) }}</td>
                            <td class="px-4 py-2">{{ $t->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->withQueryString()->links() }}
        </div>

        <h3 class="mt-8 mb-4 text-xl font-bold text-green-700">{{ __('Totals per Plot') }}</h3>
        <div class="overflow-x-auto rounded-xl shadow-inner border border-gray-200">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-green-100 text-green-700">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Plot') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Farmer') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Volume (kg)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('DRC (%)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Dry Rubber (kg)') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Price/kg') }}</th>
                        <th class="px-4 py-2 text-right">{{ __('Total Amount') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Entered By') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($totals as $plotId => $total)
                        @php $plot = $plots->find($plotId); @endphp
                        @if($plot)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-2">{{ $plot->plot_location }}</td>
                            <td class="px-4 py-2">{{ $plot->farmer->name }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($total['dry_rubber_weight'], 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($total['total_income'], 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection