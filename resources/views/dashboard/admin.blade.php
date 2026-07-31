@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-8 animate-fade-in p-4 md:p-6" x-data="{ openModal: false }">

    @if(isset($yieldWarning))
    <div class="flex items-center p-4 mb-6 border-l-4 border-rose-500 bg-rose-50 rounded-r-xl animate-pulse">
        <div class="flex-shrink-0 text-rose-600 mr-3">
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
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Welcome Banner --}}
        <div class="lg:col-span-3 p-8 bg-gradient-to-br from-emerald-600 via-green-700 to-green-800 shadow-2xl rounded-3xl text-white relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                    {{ __('Welcome back') }}, <span class="text-emerald-200 underline decoration-wavy decoration-2 underline-offset-8">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="mt-4 text-lg opacity-80 font-medium leading-relaxed">{{ __('LATER-X Decision Support is monitoring Krabi latex production trends.') }}</p>
                
                {{-- Integrated DSS Advisory --}}
                <div class="mt-8 inline-flex items-center gap-6 p-3 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-inner">
                    <div class="flex flex-col items-center justify-center bg-white text-emerald-900 w-20 h-20 rounded-xl shadow-2xl">
                        <span class="text-3xl font-black leading-none">{{ $dssScore }}</span>
                        <span class="text-[10px] uppercase font-bold tracking-tighter">{{ __('Score') }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-100 opacity-70">{{ __('Tapping Advisory') }}</p>
                        <h2 class="text-2xl font-black">
                            @if($dssScore >= 7) {{ __('Optimal for Tapping') }}
                            @elseif($dssScore >= 4) {{ __('Proceed with Caution') }}
                            @else {{ __('High Risk of Washout') }} @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Real-time Weather Card --}}
        <div class="lg:col-span-2 p-8 bg-white shadow-xl rounded-3xl flex flex-col justify-between items-center text-center border border-gray-100 relative group overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-600"></div>
            <div>
                <h3 class="text-2xl font-black text-gray-800 tracking-tight">{{ __($day) }}</h3>
                <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest">{{ $date }}</p>
            </div>
            
            <div class="flex items-center gap-6 my-4">
                <span class="text-6xl font-black text-gray-800">{{ $temperature }}°C</span>
                <div class="text-left border-l-2 border-gray-100 pl-6">
                    <span class="text-4xl drop-shadow-md">{{ $icon }}</span>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-tighter mt-1">{{ __($condition) }}</p>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] italic">{{ __('Live Research Coordinates: Krabi, TH') }}</p>
        </div>
    </div>

    {{-- KPI Cards (Updated with Icon Header + Title) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => __('Registered Farmers'), 'icon' => 'fa-users', 'value' => $totalFarmers, 'trend' => null],
                ['label' => __('Total Latex Yield'), 'icon' => 'fa-droplet', 'value' => number_format($totalWeight, 1) . ' kg', 'trend' => $growthRate],
                ['label' => __('Average DRC'), 'icon' => 'fa-vial', 'value' => $qualityIndex . '%', 'trend' => null],
                ['label' => __('Total Rubber Plots'), 'icon' => 'fa-map-location-dot', 'value' => $totalPlots, 'trend' => null],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="p-6 bg-white shadow-lg rounded-3xl border border-gray-50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                {{-- Icon and Title Row --}}
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                        <i class="fa-solid {{ $kpi['icon'] }} text-lg"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-gray-500">{{ $kpi['label'] }}</span>
                </div>

                {{-- Trend Indicator --}}
                @if($kpi['trend'] !== null)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $kpi['trend'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} {{ abs($kpi['trend']) }}%
                    </span>
                @endif
            </div>

            {{-- Main Value Display --}}
            <div class="mt-4 pt-2">
                <p class="text-3xl font-black text-gray-800 tracking-tight">{{ $kpi['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Analytics Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 bg-white p-8 rounded-3xl shadow-xl border border-gray-50">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">{{ __('Production Trend Correlation') }}</h3>
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
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-6">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cloud-showers-heavy text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Rainfall Bars') }}</p>
                        <p class="text-xs font-bold text-gray-700 leading-tight">{{ __('Monthly average precipitation (mm).') }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-line text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Latex Yield Line') }}</p>
                        <p class="text-xs font-bold text-gray-700 leading-tight">{{ __('Total dry rubber weight (kg).') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                        <span class="text-lg">💡</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-emerald-600 tracking-wider">{{ __('System Insight') }}</p>
                        <p class="text-[11px] font-medium text-gray-600 leading-tight">{{ $userAdvice }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 bg-gray-900 p-8 rounded-3xl shadow-2xl text-white">
            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-emerald-400 mb-6">{{ __('Monthly Analysis') }}</h3>
            <div class="space-y-3 overflow-y-auto max-h-[450px] custom-scrollbar pr-2">
                @foreach($monthlyDSS as $dss)
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-gray-800 border border-white/5">
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
    <div class="bg-white shadow-xl rounded-3xl p-8 border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">
                <i class="fa-solid fa-calendar-check mr-2 text-emerald-600"></i>{{ __('7-Day Harvesting Outlook') }}
            </h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
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
                    
                    <span class="text-4xl mb-4 drop-shadow-sm">
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
            <div class="p-8 bg-gradient-to-br from-gray-900 to-emerald-900 text-white relative">
                <button @click="openModal = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                    <i class="fa-solid fa-circle-xmark text-2xl"></i>
                </button>
                <p class="text-xs font-black uppercase tracking-widest text-emerald-400 mb-2">{{ __('Detailed Analytics') }}</p>
                <h2 class="text-3xl font-black">{{ __('Today in Krabi') }}</h2>
            </div>

            <div class="p-8 space-y-6">
                {{-- Environmental Matrix --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <i class="fa-solid fa-temperature-high text-orange-500 mb-1"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Temp') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $temperature }}°C</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <i class="fa-solid fa-droplet text-blue-500 mb-1"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Rain') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['rain'] ?? 0 }}mm</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <i class="fa-solid fa-water text-emerald-500 mb-1"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Humid') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['humidity'] ?? 'N/A' }}%</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <i class="fa-solid fa-wind text-teal-500 mb-1"></i>
                        <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Wind') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ $outlook[0]['wind'] ?? 'N/A' }} <span class="text-[8px]">m/s</span></p>
                    </div>
                </div>

                {{-- Scientific Rationale --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ __('LATER-X Decision Logic') }}
                    </h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="p-4 rounded-3xl border-2 {{ $dssScore >= 7 ? 'border-emerald-100 bg-emerald-50' : 'border-rose-100 bg-rose-50' }}">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-lg font-black {{ $dssScore >= 7 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $dssScore }}
                                </div>
                                <p class="text-xs font-black text-gray-800 uppercase tracking-tight">{{ __('Reliability Index (ISO 25010)') }}</p>
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

                <button @click="openModal = false" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-lg shadow-emerald-200">
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