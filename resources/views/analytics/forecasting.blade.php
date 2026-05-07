@extends('layouts.app')

@section('title', 'Forecasting & Recommendations')
@section('content')
    <div class="p-6 bg-white shadow rounded-xl">
        <h2 class="mb-6 text-2xl font-bold">Forecasting & Recommendations</h2>

        <table class="min-w-full border border-gray-300">
            <thead class="bg-green-100">
                <tr>
                    <th class="px-4 py-2 border">Plot</th>
                    <th class="px-4 py-2 border">Farmer</th>
                    <th class="px-4 py-2 border">Forecast Dry Rubber (kg)</th>
                    <th class="px-4 py-2 border">Forecast DRC (%)</th>
                    <th class="px-4 py-2 border">Forecast Revenue (Baht)</th>
                    <th class="px-4 py-2 border">Best Month for Tapping</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forecastData as $f)
                    <tr class="text-center">
                        <td class="px-4 py-2 border">{{ $f['plot_name'] }}</td>
                        <td class="px-4 py-2 border">{{ $f['farmer_name'] }}</td>
                        <td class="px-4 py-2 border">{{ $f['forecast_dry_rubber'] }}</td>
                        <td class="px-4 py-2 border">{{ $f['forecast_drc'] }}</td>
                        <td class="px-4 py-2 border">{{ $f['forecast_revenue'] }}</td>
                        <td class="px-4 py-2 border">{{ $f['best_month'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
