@extends('layouts.app')

@section('content')
    <div class="container p-6 mx-auto bg-white shadow rounded-xl">
        <h2 class="mb-4 text-2xl font-bold">Latex Transactions</h2>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-4 gap-4 mb-6">
            <select name="plot_id" class="p-2 border rounded">
                <option value="">All Plots</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                        {{ $plot->plot_location }} ({{ $plot->plot_size_rai }} Rai) - {{ $plot->farmer->name }}
                    </option>
                @endforeach
            </select>

            <select name="farmer_id" class="p-2 border rounded">
                <option value="">All Farmers</option>
                @foreach($plots->pluck('farmer')->unique('id') as $farmer)
                    <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                        {{ $farmer->name }}
                    </option>
                @endforeach
            </select>

            <select name="production_year_id" class="p-2 border rounded">
                <option value="">All Years</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ request('production_year_id') == $year->id ? 'selected' : '' }}>
                        FY {{ $year->year_label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded">Filter</button>
        </form>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Plot</th>
                        <th class="p-2 border">Farmer</th>
                        <th class="p-2 border">Volume (kg)</th>
                        <th class="p-2 border">DRC (%)</th>
                        <th class="p-2 border">Dry Rubber (kg)</th>
                        <th class="p-2 border">Price/kg</th>
                        <th class="p-2 border">Total Amount</th>
                        <th class="p-2 border">Entered By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                        <tr class="border-b">
                            <td class="p-2 border">{{ $t->transaction_date }}</td>
                            <td class="p-2 border">{{ $t->plot->plot_location }}</td>
                            <td class="p-2 border">{{ $t->plot->farmer->name }}</td>
                            <td class="p-2 border">{{ $t->volume_kg }}</td>
                            <td class="p-2 border">{{ $t->dry_rubber_content }}</td>
                            <td class="p-2 border">{{ number_format($t->volume_kg * ($t->dry_rubber_content / 100), 2) }}</td>
                            <td class="p-2 border">{{ $t->price_per_kg }}</td>
                            <td class="p-2 border">{{ number_format($t->total_amount, 2) }}</td>
                            <td class="p-2 border">{{ $t->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->withQueryString()->links() }}
        </div>

        <!-- Totals per Plot -->
        <h3 class="mt-8 mb-4 text-xl font-bold">Totals per Plot</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">Plot</th>
                        <th class="p-2 border">Farmer</th>
                        <th class="p-2 border">Dry Rubber Weight (kg)</th>
                        <th class="p-2 border">Total Income (Baht)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totals as $plotId => $total)
                        @php $plot = $plots->find($plotId); @endphp
                        <tr class="border-b">
                            <td class="p-2 border">{{ $plot->plot_location }}</td>
                            <td class="p-2 border">{{ $plot->farmer->name }}</td>
                            <td class="p-2 border">{{ number_format($total['dry_rubber_weight'], 2) }}</td>
                            <td class="p-2 border">{{ number_format($total['total_income'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
