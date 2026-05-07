@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="p-4 space-y-8 animate-fade-in md:p-6" x-data="{ openModal: false }">

    @if(isset($yieldWarning))
    <div class="flex items-center p-4 mb-6 border-l-4 border-rose-500 bg-rose-50 rounded-r-xl animate-pulse">
        <div class="flex-shrink-0 mr-3 text-rose-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-rose-800">{{ $yieldWarning['title'] }}</h3>
            <p class="text-xs text-rose-700">{{ $yieldWarning['message'] }}</p>
        </div>
    </div>
    @endif
    
    {{-- Header Section: Welcome & Real-time Weather --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        {{-- Welcome Banner --}}
        <div class="relative p-8 overflow-hidden text-white shadow-2xl lg:col-span-3 bg-gradient-to-br from-emerald-600 via-green-700 to-green-800 rounded-3xl">
            <div class="relative z-10">
                <h1 class="text-3xl font-black tracking-tight md:text-4xl">
                    {{ __('Welcome back') }}, <span class="underline text-emerald-200 decoration-wavy decoration-2 underline-offset-8">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="mt-4 text-lg font-medium leading-relaxed opacity-80">{{ __('LATER-X Decision Support is monitoring Krabi latex production trends.') }}</p>
                
                {{-- Integrated DSS Advisory --}}
                <div class="inline-flex items-center gap-6 p-3 mt-8 border shadow-inner bg-white/10 backdrop-blur-xl rounded-2xl border-white/20">
                    <div class="flex flex-col items-center justify-center w-20 h-20 bg-white shadow-2xl text-emerald-900 rounded-xl">
                        <span class="text-3xl font-black leading-none">{{ $dssScore }}</span>
                        <span class="text-[10px] uppercase font-bold tracking-tighter">{{ __('Score') }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-emerald-100 opacity-70">{{ __('Tapping Advisory') }}</p>
                        <h2 class="text-2xl font-black">
                            @if($dssScore >= 7) {{ __('Optimal for Tapping') }}
                            @elseif($dssScore >= 4) {{ __('Proceed with Caution') }}
                            @else {{ __('High Risk of Washout') }} @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="absolute w-48 h-48 rounded-full -right-12 -top-12 bg-white/10 blur-3xl"></div>
        </div>

        {{-- Real-time Weather Card --}}
        <div class="relative flex flex-col items-center justify-between p-8 overflow-hidden text-center bg-white border border-gray-100 shadow-xl lg:col-span-2 rounded-3xl group">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-600"></div>
            <div>
                <h3 class="text-2xl font-black tracking-tight text-gray-800">{{ __($day) }}</h3>
                <p class="text-sm font-bold tracking-widest uppercase text-emerald-600">{{ $date }}</p>
            </div>
            
            <div class="flex items-center gap-6 my-4">
                <span class="text-6xl font-black text-gray-800">{{ $temperature }}°C</span>
                <div class="pl-6 text-left border-l-2 border-gray-100">
                    <span class="text-4xl drop-shadow-md">{{ $icon }}</span>
                    <p class="mt-1 text-xs font-black tracking-tighter text-gray-400 uppercase">{{ __($condition) }}</p>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] italic">{{ __('Live Research Coordinates: Krabi, TH') }}</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $kpis = [
                ['label' => __('Farmers'), 'icon' => 'fa-users', 'value' => $totalFarmers, 'trend' => null],
                ['label' => __('Latex (kg)'), 'icon' => 'fa-droplet', 'value' => number_format($totalWeight, 1), 'trend' => $growthRate],
                ['label' => __('Avg DRC'), 'icon' => 'fa-vial', 'value' => $qualityIndex . '%', 'trend' => null],
                ['label' => __('Total Plots'), 'icon' => 'fa-map-location-dot', 'value' => $totalPlots, 'trend' => null],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="p-6 transition-all duration-300 bg-white border shadow-lg rounded-3xl border-gray-50 hover:shadow-2xl hover:-translate-y-2 group">
            <div class="flex items-start justify-between">
                <div class="p-3 transition-colors rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white">
                    <i class="fa-solid {{ $kpi['icon'] }} text-xl"></i>
                </div>
                @if($kpi['trend'] !== null)
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black {{ $kpi['trend'] >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                        {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} {{ abs($kpi['trend']) }}%
                    </span>
                @endif
            </div>
            <p class="mt-6 text-gray-400 text-[10px] font-black uppercase tracking-widest">{{ $kpi['label'] }}</p>
            <p class="mt-1 text-3xl font-black text-gray-800">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Analytics Row --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <div class="p-8 bg-white border shadow-xl lg:col-span-8 rounded-3xl border-gray-50">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black tracking-tight text-gray-800 uppercase">{{ __('Production Trend Correlation') }}</h3>
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">
                        {{ __('Objective 1: Yield vs Rainfall') }} 
                        <span class="mx-2 text-gray-300">|</span> 
                        <span class="text-blue-500">r = {{ number_format($correlationScore, 2) }} ({{ $correlationStrength }})</span>
                    </p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="productionWeatherChart"></canvas>
            </div>
            <div class="grid grid-cols-1 gap-4 pt-6 mt-8 border-t border-gray-100 md:grid-cols-3">
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 shrink-0">
                        <i class="text-blue-500 fa-solid fa-cloud-showers-heavy"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Rainfall Bars') }}</p>
                        <p class="text-xs font-bold leading-tight text-gray-700">{{ __('Monthly average precipitation (mm).') }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 shrink-0">
                        <i class="fa-solid fa-chart-line text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Latex Yield Line') }}</p>
                        <p class="text-xs font-bold leading-tight text-gray-700">{{ __('Total dry rubber weight (kg).') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 border border-gray-100 rounded-2xl bg-gray-50">
                    <div class="flex items-center justify-center w-10 h-10 bg-white shadow-sm rounded-xl shrink-0">
                        <span class="text-lg">💡</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-emerald-600 tracking-wider">{{ __('System Insight') }}</p>
                        <p class="text-[11px] font-medium text-gray-600 leading-tight">{{ $userAdvice }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8 text-white bg-gray-900 shadow-2xl lg:col-span-4 rounded-3xl">
            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-emerald-400 mb-6">{{ __('Monthly Analysis') }}</h3>
            <div class="space-y-3 overflow-y-auto max-h-[450px] custom-scrollbar pr-2">
                @foreach($monthlyDSS as $dss)
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-gray-800 border rounded-xl border-white/5">
                        <span class="text-xl font-black {{ $dss['score'] >= 7 ? 'text-emerald-400' : ($dss['score'] >= 4 ? 'text-amber-400' : 'text-rose-400') }}">
                            {{ str_pad($dss['score'], 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider">{{ __($dss['month']) }}</p>
                        <p class="text-[10px] font-bold text-gray-500 uppercase">{{ __($dss['recommendation']) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 7-Day Harvesting Outlook --}}
    <div class="p-8 bg-white border border-gray-100 shadow-xl rounded-3xl">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black tracking-tight text-gray-800">
                <i class="mr-2 fa-solid fa-calendar-check text-emerald-600"></i>{{ __('7-Day Harvesting Outlook') }}
            </h3>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-7">
            @foreach($outlook as $item)
                @php
                    $isToday = $item['day'] === \Carbon\Carbon::now()->translatedFormat('D');
                @endphp
                
                <div 
                    @if($isToday) @click="openModal = true" @endif
                    class="flex flex-col items-center p-6 rounded-3xl border-2 transition-all relative overflow-hidden 
                    {{ $isToday ? 'cursor-pointer ring-4 ring-emerald-500/20 border-emerald-500 z-10 scale-105 shadow-2xl bg-white' : 'opacity-80 hover:opacity-100 hover:-translate-y-1' }}
                    {{ !$isToday && $item['color'] == 'green' ? 'border-emerald-100 bg-emerald-50/50' : '' }}
                    {{ !$isToday && $item['color'] == 'yellow' ? 'border-amber-100 bg-amber-50/50' : '' }}
                    {{ !$isToday && $item['color'] == 'red' ? 'border-rose-100 bg-rose-50/50' : '' }}">
                    
                    @if($isToday)
                        <div class="absolute top-0 left-0 w-full bg-emerald-500 text-[9px] text-white font-black uppercase text-center py-1 tracking-widest animate-pulse">
                            {{ __('Today') }}
                        </div>
                    @endif
                    
                    <p class="text-[10px] font-black {{ $isToday ? 'text-emerald-600' : 'text-gray-400' }} uppercase tracking-widest mb-3 {{ $isToday ? 'mt-2' : '' }}">
                        {{ __($item['day']) }}
                    </p>
                    
                    <span class="mb-4 text-4xl drop-shadow-sm">
                        {!! $item['rain'] > 5 ? '🌧️' : ($item['rain'] > 0 ? '🌦️' : '☀️') !!}
                    </span>

                    <p class="text-[10px] text-center font-black uppercase leading-tight {{ $item['color'] == 'green' ? 'text-emerald-600' : ($item['color'] == 'yellow' ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ __($item['recommendation']) }}
                    </p>

                    @if($isToday)
                        <span class="mt-4 text-[8px] font-black text-emerald-500 uppercase animate-bounce">{{ __('View Details') }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" 
         style="display: none;">
        
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100" @click.away="openModal = false">
            <div class="relative p-8 text-white bg-gradient-to-br from-gray-900 to-emerald-900">
                <button @click="openModal = false" class="absolute transition-colors top-6 right-6 text-white/50 hover:text-white">
                    <i class="text-2xl fa-solid fa-circle-xmark"></i>
                </button>
                <p class="mb-2 text-xs font-black tracking-widest uppercase text-emerald-400">{{ __('Detailed Analytics') }}</p>
                <h2 class="text-3xl font-black">{{ __('Today in Krabi') }}</h2>
            </div>

            <div class="p-8 space-y-6">
                {{-- Environmental Matrix --}}
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <div class="p-3 text-center border border-gray-100 bg-gray-50 rounded-2xl">
                        <i class="mb-1 text-orange-500 fa-solid fa-temperature-high"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Temp') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $temperature }}°C</p>
                    </div>
                    <div class="p-3 text-center border border-gray-100 bg-gray-50 rounded-2xl">
                        <i class="mb-1 text-blue-500 fa-solid fa-droplet"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Rain') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['rain'] ?? 0 }}mm</p>
                    </div>
                    <div class="p-3 text-center border border-gray-100 bg-gray-50 rounded-2xl">
                        <i class="mb-1 fa-solid fa-water text-emerald-500"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Humid') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['humidity'] ?? 'N/A' }}%</p>
                    </div>
                    <div class="p-3 text-center border border-gray-100 bg-gray-50 rounded-2xl">
                        <i class="mb-1 text-teal-500 fa-solid fa-wind"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Wind') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['wind'] ?? 'N/A' }} <span class="text-[8px]">m/s</span></p>
                    </div>
                </div>

                {{-- Scientific Rationale --}}
                <div class="space-y-4">
                    <h4 class="flex items-center gap-2 text-xs font-black tracking-widest text-gray-400 uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ __('LATER-X Decision Logic') }}
                    </h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="p-4 rounded-3xl border-2 {{ $dssScore >= 7 ? 'border-emerald-100 bg-emerald-50' : 'border-rose-100 bg-rose-50' }}">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-lg font-black {{ $dssScore >= 7 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $dssScore }}
                                </div>
                                <p class="text-xs font-black tracking-tight text-gray-800 uppercase">{{ __('Reliability Index (ISO 25010)') }}</p>
                            </div>
                            <p class="text-[11px] text-gray-600 leading-tight">
                                @if($dssScore >= 7) 
                                    {{ __('Optimal conditions. Low humidity risk and stable winds detected.') }}
                                @else 
                                    {{ __('Rain washout risk is high. Humidity and wind levels may impact latex quality.') }} 
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <button @click="openModal = false" class="w-full py-4 text-xs font-black tracking-widest text-white uppercase transition-all shadow-lg bg-emerald-600 hover:bg-emerald-700 rounded-2xl shadow-emerald-200">
                    {{ __('Close Analytics') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    tension: 0.4,
                    yAxisID: 'yYield',
                    type: 'line',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yYield: { 
                    position: 'left', 
                    title: { display: true, text: 'Latex (kg)', font: { weight: 'bold' } }
                },
                yRain: { 
                    position: 'right', 
                    grid: { drawOnChartArea: false }, 
                    title: { display: true, text: 'Rainfall (mm)', font: { weight: 'bold' } }
                }
            }
        }
    });
</script>
@endsection