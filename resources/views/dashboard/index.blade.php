@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">

    {{-- Welcome & Weather --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-4 p-8 bg-gradient-to-r from-green-100 to-green-200 shadow-xl rounded-2xl">
            <h1 class="text-4xl md:text-5xl font-extrabold text-green-800">
                {{ __('Welcome') }}, <span class="underline decoration-green-700 underline-offset-4">{{ auth()->user()->name }}</span>!
            </h1>
            <p class="mt-4 text-xl md:text-2xl text-gray-600">{{ __('Here’s all the latest updates for you.') }}</p>
        </div>
        <div class="p-6 bg-white shadow-xl rounded-2xl flex flex-col justify-center items-center text-center">
            <h3 class="text-3xl md:text-4xl font-bold text-green-700">{{ __($day) }}</h3>
            <h3 class="text-lg md:text-xl text-green-600">{{ $date }}</h3>
            <div class="mt-4">
                <span class="text-2xl md:text-3xl font-bold text-green-800">{{ $icon }} {{ $temperature }}°C</span>
                <p class="text-sm md:text-base text-gray-500 capitalize">{{ __($condition) }}</p>
            </div>
        </div>
    </div>

    <hr class="border-gray-300 my-6">

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => __('Farmers'), 'icon' => 'fa-wheat-awn', 'value' => $totalFarmers],
                ['label' => __('Latex (kg)'), 'icon' => 'fa-glass-water-droplet', 'value' => number_format($totalWeight, 2)],
                ['label' => __('Rubber Trees'), 'icon' => 'fa-tree', 'value' => $totalPlots],
                ['label' => __('Top Contribution'), 'icon' => 'fa-arrow-trend-up', 'value' => ($topPlot->contribution_percent ?? 0) . '%'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="p-6 bg-white shadow-xl rounded-2xl hover:shadow-2xl transition-shadow duration-300">
            <p class="text-sm text-gray-400">{{ __('Total') }}</p>
            <p class="mt-2 text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fa-solid {{ $kpi['icon'] }} text-green-600"></i> {{ __($kpi['label']) }}
            </p>
            <p class="mt-4 text-center text-green-700 text-5xl font-bold">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Monthly DSS Recommendation --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-3 p-6 bg-white shadow-xl rounded-2xl overflow-x-auto">
            <h3 class="text-xl font-semibold mb-4 text-green-700">{{ __('Monthly DSS Recommendation') }}</h3>
            <table class="w-full text-sm md:text-base">
                <thead class="bg-green-100 text-green-700 uppercase text-left">
                    <tr>
                        <th class="py-2 px-4">{{ __('Month') }}</th>
                        <th class="py-2 px-4 text-center">{{ __('DSS Score') }}</th>
                        <th class="py-2 px-4 text-center">{{ __('Recommendation') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyDSS as $dss)
                    <tr class="border-b hover:bg-green-50 transition-colors">
                        <td class="py-2 px-4">{{ __($dss['month']) }}</td>
                        <td class="py-2 px-4 text-center font-bold">{{ $dss['score'] }}</td>
                        <td class="py-2 px-4 text-center">{{ __($dss['recommendation']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Analytics Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2 p-6 bg-white shadow-xl rounded-2xl">
            <h3 class="text-lg font-semibold mb-4 text-green-700">{{ __('Latex Production Trend') }}</h3>
            <canvas id="productionTrend" class="w-full h-64 md:h-80"></canvas>
        </div>

        <div class="p-6 bg-white shadow-xl rounded-2xl flex flex-col justify-center items-center">
            <h3 class="text-lg font-semibold text-green-700 mb-2">{{ __('Latex Quality Index') }}</h3>
            <canvas id="qualityGauge" class="w-32 h-32 md:w-48 md:h-48"></canvas>
            <p class="mt-4 text-3xl md:text-4xl font-bold text-green-600">{{ $qualityIndex }}%</p>
        </div>
    </div>

    {{-- Top Contributors & Monthly Quality Trend --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="p-6 bg-white shadow-xl rounded-2xl">
            <h3 class="text-lg font-semibold mb-4 text-green-700">{{ __('Top Contributors') }}</h3>
            <ul class="space-y-2">
                @foreach($topContributors as $contributor)
                <li class="flex justify-between bg-green-50 p-3 rounded-lg hover:bg-green-100 transition-colors">
                    <span>{{ $contributor->name }}</span>
                    <span class="font-bold text-green-700">{{ number_format($contributor->total_latex, 2) }} {{ __('kg') }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="p-6 bg-white shadow-xl rounded-2xl">
            <h3 class="text-lg font-semibold mb-4 text-green-700">{{ __('Monthly Quality Trend') }}</h3>
            <canvas id="qualityTrend" class="w-full h-64 md:h-80"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Production Trend (Multiple Farmers)
    const trendCtx = document.getElementById('productionTrend').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: @json($chartData)
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // Quality Gauge
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
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    });

    // Monthly Quality Trend
    new Chart(document.getElementById('qualityTrend'), {
        type: 'line',
        data: {
            labels: @json($qualityLabels),
            datasets: [{
                label: 'Quality Index',
                data: @json($qualityData),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(34,197,94,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
</script>
@endsection
