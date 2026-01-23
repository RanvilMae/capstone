@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="grid grid-cols-5 gap-6 mb-6">


        <div class="col-span-4 p-6 bg-white shadow rounded-xl">
            <h1 class="mr-12 text-5xl font-bold text-green-800">
                Welcome, <u class="underline underline-offset-2">{{ auth()->user()->name }}!</u>
            </h1>
            <p class="mt-4 mb-8 text-2xl text-gray-500">Here’s all the latest updates for you.</p>
        </div>
        {{-- This div spans 1 column --}}
        <div class="p-6 bg-white shadow rounded-xl">
            <h3 class="text-4xl font-semibold text-left text-green-800">{{ $day }}</h3>
            <h3 class="text-xl font-semibold text-green-600">{{ $date }}</h3> <br>
            <h3 class="text-xl font-semibold text-center text-green-600">
                <span class="text-2xl font-bold text-green-700">{{ $icon }} {{ $temperature }}°C</span> <br>
                <span class="text-sm text-gray-600 capitalize">{{ $condition }}</span>
            </h3>
        </div>

        {{-- This div spans 2 columns --}}
    </div>

    <div class="mt-6 mb-8 ml-12">

        <hr />
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="p-4 bg-white shadow rounded-xl">
            <p class="text-sm text-gray-500">Total</p>
            <p class="mb-6 text-2xl font-bold text-gray-800"><i class="fa-solid fa-wheat-awn"></i> Farmers</p>
            <p class="font-bold text-center text-green-800 text-7xl">{{ $totalFarmers }}</p>
        </div>

        <div class="p-4 bg-white shadow rounded-xl">
            <p class="text-sm text-gray-500">Total</p>
            <p class="mb-6 text-2xl font-bold text-gray-800"><i class="fa-solid fa-glass-water-droplet"></i> Latex (kg)</p>
            <p class="font-bold text-center text-green-800 text-7xl">
                {{ number_format($totalWeight, 2) }}
            </p>
        </div>

        <div class="p-4 bg-white shadow rounded-xl">
            <p class="text-sm text-gray-500">Total</p>
            <p class="mb-6 text-2xl font-bold text-gray-800"><i class="fa-solid fa-tree"></i> Rubber Trees</p>
            <p class="font-bold text-center text-green-800 text-7xl">{{ $totalPlots }}</p>
        </div>

        <div class="p-4 bg-white shadow rounded-xl">
            <p class="text-sm text-gray-500">Total</p>
            <p class="mb-6 text-2xl font-bold text-gray-800"><i class="fa-solid fa-arrow-trend-up"></i> Top Contribution</p>
            <p class="font-bold text-center text-green-800 text-7xl">
                {{ $topPlot->contribution_percent ?? 0 }}%
            </p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-6">
        {{-- This div spans 1 column --}}
        <div class="p-6 mt-6 bg-white shadow rounded-xl">
        </div>

        {{-- This div spans 2 columns --}}
        <div class="col-span-2 p-6 mt-6 bg-white shadow rounded-xl">
            <h3 class="mb-4 font-semibold">Monthly DSS Recommendation</h3>

            <table class="w-full text-sm">
                <thead class="text-gray-500 border-b">
                    <tr>
                        <th class="py-2 text-left">Month</th>
                        <th class="text-center">DSS Score</th>
                        <th class="text-center">Recommendation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyDSS as $dss)
                        <tr class="border-b">
                            <td class="py-2">{{ $dss['month'] }}</td>
                            <td class="font-bold text-center">{{ $dss['score'] }}</td>
                            <td class="text-center">{{ $dss['recommendation'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- MAIN GRID --}}
    <div class="grid grid-cols-3 gap-6 mt-6">
        {{-- AREA CHART --}}
        <div class="col-span-2 p-6 bg-white shadow rounded-xl">
            <h3 class="mb-4 font-semibold">Latex Production Trend</h3>
            <canvas id="productionTrend"></canvas>
        </div>

        {{-- GAUGE --}}
        <div class="p-6 text-center bg-white shadow rounded-xl">
            <h3 class="mb-2 font-semibold">Latex Quality Index</h3>
            <canvas id="qualityGauge"></canvas>
            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ $qualityIndex }}%
            </p>
        </div>
    </div>

    {{-- CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    /* AREA CHART */
    const trendCtx = document.getElementById('productionTrend').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(99,102,241,0.4)');
    gradient.addColorStop(1, 'rgba(99,102,241,0.05)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                data: @json($chartData),
                fill: true,
                backgroundColor: gradient,
                borderColor: '#6366f1',
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true }}
        }
    });

    /* GAUGE */
    new Chart(document.getElementById('qualityGauge'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [{{ $qualityIndex }}, {{ 100 - $qualityIndex }}],
                backgroundColor: ['#22c55e', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            rotation: -90,
            circumference: 180,
            cutout: '80%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });
    </script>
@endsection
