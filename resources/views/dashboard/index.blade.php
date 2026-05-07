@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">

    {{-- Welcome & DSS Advisory --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3 p-8 bg-gradient-to-r from-green-600 to-green-700 shadow-xl rounded-2xl text-white">
            <h1 class="text-3xl md:text-4xl font-extrabold">
                {{ __('Welcome') }}, <span class="border-b-4 border-white">{{ auth()->user()->name }}</span>!
            </h1>
            <p class="mt-4 text-lg opacity-90">{{ __('Here’s all the latest updates for you.') }}</p>
            
            {{-- Integrated DSS Advisory --}}
            <div class="mt-6 p-4 bg-white/20 backdrop-blur-md rounded-xl border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wider text-green-100">{{ __('Tapping Advisory') }}</p>
                        <h2 class="text-2xl font-bold">
                            @if($dssScore >= 7) {{ __('Optimal for Tapping') }}
                            @elseif($dssScore >= 4) {{ __('Proceed with Caution') }}
                            @else {{ __('High Risk of Washout') }} @endif
                        </h2>
                    </div>
                    <div class="text-center bg-white text-green-700 px-4 py-2 rounded-lg shadow-lg">
                        <span class="text-3xl font-black">{{ $dssScore }}</span><span class="text-sm">/10</span>
                        <p class="text-[10px] uppercase font-bold">{{ __('DSS Score') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Real-time Weather Card --}}
        <div class="lg:col-span-2 p-6 bg-white shadow-xl rounded-2xl flex flex-col justify-center items-center text-center border-t-4 border-green-500">
            <h3 class="text-3xl font-bold text-green-700">{{ __($day) }}</h3>
            <h3 class="text-lg text-green-600">{{ $date }}</h3>
            <div class="mt-4 flex items-center gap-4">
                <span class="text-5xl font-bold text-gray-800">{{ $temperature }}°C</span>
                <div class="text-left">
                    <span class="text-3xl">{{ $icon }}</span>
                    <p class="text-sm text-gray-500 capitalize">{{ __($condition) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI CARDS with Historical Comparison --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => __('Farmers'), 'icon' => 'fa-users', 'value' => $totalFarmers, 'trend' => null],
                ['label' => __('Latex (kg)'), 'icon' => 'fa-droplet', 'value' => number_format($totalWeight, 1), 'trend' => $growthRate],
                ['label' => __('Avg DRC'), 'icon' => 'fa-vial', 'value' => $qualityIndex . '%', 'trend' => null],
                ['label' => __('Total Plots'), 'icon' => 'fa-map-location-dot', 'value' => $totalPlots, 'trend' => null],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="p-6 bg-white shadow-xl rounded-2xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex justify-between items-start">
                <i class="fa-solid {{ $kpi['icon'] }} text-2xl text-green-600 bg-green-50 p-3 rounded-lg"></i>
                @if($kpi['trend'] !== null)
                    <span class="text-xs font-bold {{ $kpi['trend'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} {{ abs($kpi['trend']) }}%
                    </span>
                @endif
            </div>
            <p class="mt-4 text-gray-500 text-sm font-medium">{{ $kpi['label'] }}</p>
            <p class="text-3xl font-bold text-gray-800">{{ $kpi['value'] }}</p>
            @if($kpi['trend'] !== null)
                <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ __('vs 5-Year Average') }}</p>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Charts: Production vs Weather Correlation --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 p-6 bg-white shadow-xl rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-green-700">{{ __('Latex Production Trend') }}</h3>
                <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500 italic">{{ __('Objective 1: Yield-Weather Correlation') }}</span>
            </div>
            <canvas id="productionWeatherChart" class="w-full h-80"></canvas>
        </div>

        <div class="p-6 bg-white shadow-xl rounded-2xl flex flex-col">
            <h3 class="text-lg font-bold text-green-700 mb-4">{{ __('Monthly DSS Recommendation') }}</h3>
            <div class="space-y-4 overflow-y-auto max-h-[320px]">
                @foreach($monthlyDSS as $dss)
                <div class="flex items-center gap-4 p-3 rounded-xl {{ $dss['score'] >= 7 ? 'bg-green-50' : 'bg-red-50' }}">
                    <div class="text-2xl font-black {{ $dss['score'] >= 7 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $dss['score'] }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-800">{{ __($dss['month']) }}</p>
                        <p class="text-[11px] text-gray-600 leading-tight">{{ __($dss['recommendation']) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-6 mt-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-extrabold text-green-700">
            <i class="fa-solid fa-calendar-check mr-2"></i>{{ __('7-Day Harvesting Outlook') }}
        </h3>
        <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold uppercase">
            {{ __('DSS Active') }}
        </span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        @foreach($outlook as $day)
            <div class="flex flex-col items-center p-4 rounded-2xl border-2 transition-transform hover:scale-105
                {{ $day['color'] == 'green' ? 'border-green-200 bg-green-50' : '' }}
                {{ $day['color'] == 'yellow' ? 'border-yellow-200 bg-yellow-50' : '' }}
                {{ $day['color'] == 'red' ? 'border-red-200 bg-red-50' : '' }}">
                
                <p class="text-sm font-bold text-gray-600 mb-2">{{ __($day['day']) }}</p>
                
                {{-- Weather Icon based on rain --}}
                <span class="text-3xl mb-2">
                    {!! $day['rain'] > 5 ? '🌧️' : ($day['rain'] > 0 ? '🌦️' : '☀️') !!}
                </span>

                <p class="text-xs font-bold {{ $day['color'] == 'green' ? 'text-green-600' : ($day['color'] == 'yellow' ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ __($day['action']) }}
                </p>

                <div class="mt-3 w-full bg-gray-200 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $day['color'] == 'green' ? 'bg-green-500' : ($day['color'] == 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}" 
                         style="width: {{ $day['score'] * 10 }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dual Axis Chart: Latex Production (Line) vs Rainfall (Bars)
    // This directly addresses the documentation requirement for Correlation Analysis
    const ctx = document.getElementById('productionWeatherChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Rainfall (mm)',
                    data: @json($rainfallData),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'yRain',
                    type: 'bar'
                },
                {
                    label: 'Latex Yield (kg)',
                    data: @json($productionData),
                    borderColor: '#16a34a',
                    backgroundColor: '#16a34a',
                    tension: 0.4,
                    yAxisID: 'yYield',
                    type: 'line',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                yYield: { type: 'linear', position: 'left', title: { display: true, text: 'Latex (kg)' } },
                yRain: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Rainfall (mm)' } }
            }
        }
    });
</script>
@endsection