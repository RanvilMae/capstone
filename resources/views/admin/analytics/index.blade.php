@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
    <h2 class="mb-4 text-2xl font-bold">Rubber Production Analytics</h2>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        <!-- Monthly Production Trend -->
        <div class="p-4 bg-white rounded shadow">
            <h3 class="mb-2 text-lg font-semibold">Monthly Production Trend (kg)</h3>
            <canvas id="monthlyProductionChart"></canvas>
        </div>

        <!-- Seasonal Pattern -->
        <div class="p-4 bg-white rounded shadow">
            <h3 class="mb-2 text-lg font-semibold">Seasonal Production Pattern (kg)</h3>
            <canvas id="seasonalPatternChart"></canvas>
        </div>

        <!-- Quality Evaluation -->
        <div class="p-4 bg-white rounded shadow md:col-span-2">
            <h3 class="mb-2 text-lg font-semibold">Average Latex Quality (DRC%) by Plot</h3>
            <canvas id="qualityChart"></canvas>
        </div>

    </div>

    <div class="p-6 mt-8 bg-white rounded-lg shadow">
        <h2 class="mb-4 text-2xl font-bold">Revenue Forecast</h2>

        @if($latestPrice)
            <p class="mb-4">Using latest market price: <strong>{{ number_format($latestPrice->price_per_kg, 2) }}
                    Baht/kg</strong> (Date: {{ $latestPrice->date->format('Y-m-d') }})</p>
        @else
            <p class="mb-4 text-red-500">No market price data available. Please add manually to see revenue forecasts.</p>
        @endif

        <canvas id="revenueChart" class="w-full h-64"></canvas>
    </div>

    <div class="p-6 mt-8 bg-white rounded-lg shadow">
        <h2 class="mb-4 text-2xl font-bold">Recommendations</h2>

        @foreach($qualityByPlot as $plot)
            <div class="p-4 mb-4 border rounded">
                <h3 class="font-semibold">{{ $plot['plot_name'] }} (Farmer: {{ $plot['farmer_name'] }})</h3>
                <p>Average Dry Rubber Content (DRC): <strong>{{ $plot['avg_drc'] }}%</strong></p>

                @if($plot['avg_drc'] < 30)
                    <p class="text-red-600">Recommendation: Improve latex quality by adjusting tapping schedule or fertilizer use.
                    </p>
                @elseif($plot['avg_drc'] < 40)
                    <p class="text-yellow-600">Recommendation: Latex quality is moderate. Monitor closely for improvements.</p>
                @else
                    <p class="text-green-600">Recommendation: Excellent latex quality. Maintain current practices.</p>
                @endif
            </div>
        @endforeach
    </div>


    <script>
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($revenueByPlot->pluck('plot_name')),
                datasets: [{
                    label: 'Estimated Revenue (Baht)',
                    data: @json($revenueByPlot->pluck('estimated_revenue')),
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: 'rgba(37,99,235,1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    </script>

    <script>
    const monthlyCtx = document.getElementById('monthlyProductionChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($monthlyProduction->toArray())),
            datasets: [{
                label: 'Dry Rubber Weight (kg)',
                data: @json(array_values($monthlyProduction->toArray())),
                borderColor: 'rgba(34,197,94,1)',
                backgroundColor: 'rgba(34,197,94,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    const seasonalCtx = document.getElementById('seasonalPatternChart').getContext('2d');
    new Chart(seasonalCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($seasonalPattern->toArray())),
            datasets: [{
                label: 'Dry Rubber Weight (kg)',
                data: @json(array_values($seasonalPattern->toArray())),
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    const qualityCtx = document.getElementById('qualityChart').getContext('2d');
    new Chart(qualityCtx, {
        type: 'bar',
        data: {
            const qualityCtx = document.getElementById('qualityChart').getContext('2d');
            new Chart(qualityCtx, {
                type: 'bar',
                data: {
                    labels: @json($qualityByPlot->map(fn($q) => $q['plot_name'] . ' (' . $q['farmer_name'] . ')')),
                    datasets: [{
                        label: 'Average DRC (%)',
                        data: @json($qualityByPlot->pluck('avg_drc')),
                        backgroundColor: 'rgba(251,191,36,0.7)',
                        borderColor: 'rgba(245,158,11,1)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
            datasets: [{
                label: 'Average DRC (%)',
                data: @json($qualityByPlot->pluck('avg_drc')),
                backgroundColor: 'rgba(251,191,36,0.7)',
                borderColor: 'rgba(245,158,11,1)',
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    </script>
@endsection
