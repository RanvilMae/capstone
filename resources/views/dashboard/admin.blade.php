@extends('layouts.app')

@section('title', __('Dashboard - LATER-X Precision Latex DSS'))

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-8 animate-fade-in p-4 md:p-6" x-data="{ 
    openModal: false, 
    activeTab: 'overview',
    selectedDay: {
        day: '{{ is_array($outlook) ? ($outlook[0]['day'] ?? 'Today') : ($outlook->first()['day'] ?? 'Today') }}',
        temp: {{ $temperature }},
        rain: {{ is_array($outlook) ? ($outlook[0]['rain'] ?? 0) : ($outlook->first()['rain'] ?? 0) }},
        humidity: {{ is_array($outlook) ? ($outlook[0]['humidity'] ?? 0) : ($outlook->first()['humidity'] ?? 0) }},
        wind: {{ is_array($outlook) ? ($outlook[0]['wind'] ?? 0) : ($outlook->first()['wind'] ?? 0) }},
        score: {{ $dssScore }},
        color: '{{ is_array($outlook) ? ($outlook[0]['color'] ?? 'green') : ($outlook->first()['color'] ?? 'green') }}',
        recommendation: '{{ is_array($outlook) ? ($outlook[0]['recommendation'] ?? 'Standard') : ($outlook->first()['recommendation'] ?? 'Standard') }}'
    }
}">

    {{-- Alert Notification Banner --}}
    @if(isset($yieldWarning))
    <div class="flex items-start p-4 mb-6 border-l-4 border-rose-500 bg-rose-50 rounded-r-xl shadow-sm animate-pulse">
        <div class="flex-shrink-0 text-rose-600 mr-3 mt-0.5">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-rose-800">{{ $yieldWarning['title'] }}</h3>
                <span class="text-[10px] font-mono font-bold bg-rose-200 text-rose-800 px-2 py-0.5 rounded uppercase">{{ __('High Priority Alert') }}</span>
            </div>
            <p class="text-xs text-rose-700 mt-1 leading-relaxed">{{ $yieldWarning['message'] }}</p>
            <p class="text-[10px] text-rose-600 font-semibold mt-1">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ __('Impact: Tapping during active precipitation risks latex dilution and bark necrosis.') }}
            </p>
        </div>
    </div>
    @endif
    
    {{-- Header Section: Welcome Banner & Live Weather Matrix --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Welcome Banner --}}
        <div class="lg:col-span-3 p-8 bg-gradient-to-br from-emerald-600 via-green-700 to-green-800 shadow-2xl rounded-3xl text-white relative overflow-hidden flex flex-col justify-between">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest text-emerald-100 border border-white/10">
                        {{ __('Krabi DSS Engine v2.4') }}
                    </span>
                    <span class="text-[10px] text-emerald-200 font-mono">{{ __('Updated:') }} {{ now()->format('H:i T') }}</span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                    {{ __('Welcome back') }}, <span class="text-emerald-200 underline decoration-wavy decoration-2 underline-offset-8">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="mt-3 text-sm opacity-90 font-medium leading-relaxed max-w-xl">
                    {{ __('LATER-X Decision Support system is currently evaluating real-time atmospheric moisture, soil humidity, and latex yield trends across regional plots in Krabi Province.') }}
                </p>
                
                {{-- Integrated DSS Advisory Box --}}
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-6 p-4 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-inner">
                    <div class="flex flex-col items-center justify-center bg-white text-emerald-900 w-20 h-20 shrink-0 rounded-xl shadow-2xl">
                        <span class="text-3xl font-black leading-none">{{ $dssScore }}</span>
                        <span class="text-[9px] uppercase font-bold tracking-tighter text-emerald-700 mt-0.5">{{ __('Score / 10') }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-xs font-bold uppercase tracking-widest text-emerald-200 opacity-90">{{ __('Real-Time Tapping Advisory') }}</p>
                            <span class="inline-block w-2 h-2 rounded-full {{ $dssScore >= 7 ? 'bg-emerald-300' : ($dssScore >= 4 ? 'bg-amber-300' : 'bg-rose-300') }} animate-ping"></span>
                        </div>
                        <h2 class="text-2xl font-black text-white">
                            @if($dssScore >= 7) {{ __('Optimal Conditions for Tapping') }}
                            @elseif($dssScore >= 4) {{ __('Proceed with Caution') }}
                            @else {{ __('High Risk of Washout') }} @endif
                        </h2>
                        <p class="text-[11px] text-emerald-100 opacity-80 mt-1">
                            @if($dssScore >= 7)
                                {{ __('Low rainfall probability with favorable dry rubber content potential. Standard tapping protocols recommended.') }}
                            @elseif($dssScore >= 4)
                                {{ __('Moderate atmospheric moisture detected. Monitor rain radar closely before applying stimulants.') }}
                            @else
                                {{ __('High likelihood of rain-induced latex overflow. Postpone tapping to avoid financial loss and panel damage.') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        {{-- Real-time Weather & Microclimate Card --}}
        <div class="lg:col-span-2 p-8 bg-white shadow-xl rounded-3xl flex flex-col justify-between items-center text-center border border-gray-100 relative group overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-600"></div>
            
            <div class="w-full flex justify-between items-center text-left">
                <div>
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight">{{ __($day) }}</h3>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest">{{ $date }}</p>
                </div>
                <span class="px-2.5 py-1 bg-gray-100 rounded-lg text-[10px] font-mono font-bold text-gray-500 uppercase">{{ __('Live Telemetry') }}</span>
            </div>
            
            <div class="flex items-center gap-6 my-4">
                <span class="text-6xl font-black text-gray-800 tracking-tight">{{ $temperature }}°C</span>
                <div class="text-left border-l-2 border-gray-100 pl-6">
                    <span class="text-4xl drop-shadow-md">{{ $icon }}</span>
                    <p class="text-xs font-black text-gray-500 uppercase tracking-wider mt-1">{{ __($condition) }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">{{ __('Evapotranspiration Rate: Normal') }}</p>
                </div>
            </div>

            {{-- Expanded Weather Telemetry Indicators --}}
            <div class="w-full grid grid-cols-3 gap-2 pt-4 border-t border-gray-100 text-left">
                <div class="p-2 rounded-xl bg-gray-50">
                    <p class="text-[9px] font-black uppercase text-gray-400">{{ __('Precipitation') }}</p>
                    <p class="text-xs font-bold text-gray-700">{{ is_array($outlook) ? ($outlook[0]['rain'] ?? 0) : ($outlook->first()['rain'] ?? 0) }} mm</p>
                </div>
                <div class="p-2 rounded-xl bg-gray-50">
                    <p class="text-[9px] font-black uppercase text-gray-400">{{ __('Relative Humid.') }}</p>
                    <p class="text-xs font-bold text-gray-700">{{ is_array($outlook) ? ($outlook[0]['humidity'] ?? 'N/A') : ($outlook->first()['humidity'] ?? 'N/A') }}%</p>
                </div>
                <div class="p-2 rounded-xl bg-gray-50">
                    <p class="text-[9px] font-black uppercase text-gray-400">{{ __('Wind Speed') }}</p>
                    <p class="text-xs font-bold text-gray-700">{{ is_array($outlook) ? ($outlook[0]['wind'] ?? 'N/A') : ($outlook->first()['wind'] ?? 'N/A') }} m/s</p>
                </div>
            </div>

            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] italic mt-4">{{ __('Live Research Coordinates: Krabi, TH (8.0863° N, 98.9063° E)') }}</p>
        </div>
    </div>

    {{-- KPI Cards with Analytical Context --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                [
                    'label' => __('Registered Farmers'), 
                    'icon' => 'fa-users', 
                    'value' => $totalFarmers, 
                    'trend' => null, 
                    'subtext' => __('Active smallholders in Krabi area'),
                    'detail' => __('100% verified via provincial registry')
                ],
                [
                    'label' => __('Total Latex Yield'), 
                    'icon' => 'fa-droplet', 
                    'value' => number_format($totalWeight, 1) . ' kg', 
                    'trend' => $growthRate, 
                    'subtext' => __('Cumulative dry rubber output'),
                    'detail' => __('Compared against previous 30-day window')
                ],
                [
                    'label' => __('Average DRC Index'), 
                    'icon' => 'fa-vial', 
                    'value' => $qualityIndex . '%', 
                    'trend' => null, 
                    'subtext' => __('Dry Rubber Content benchmark'),
                    'detail' => $qualityIndex >= 33 ? __('Grade A Quality (>30% standard)') : __('Below optimal latex density')
                ],
                [
                    'label' => __('Monitored Plots'), 
                    'icon' => 'fa-map-location-dot', 
                    'value' => $totalPlots, 
                    'trend' => null, 
                    'subtext' => __('Mapped rubber plantations'),
                    'detail' => __('Geofenced for microclimate tracking')
                ],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="p-6 bg-white shadow-lg rounded-3xl border border-gray-50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                            <i class="fa-solid {{ $kpi['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-gray-500 block leading-tight">{{ $kpi['label'] }}</span>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $kpi['subtext'] }}</span>
                        </div>
                    </div>

                    @if($kpi['trend'] !== null)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $kpi['trend'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} {{ abs($kpi['trend']) }}%
                        </span>
                    @endif
                </div>

                <div class="mt-4 pt-2">
                    <p class="text-3xl font-black text-gray-800 tracking-tight">{{ $kpi['value'] }}</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-semibold">
                <span>{{ $kpi['detail'] }}</span>
                <i class="fa-solid fa-circle-info text-gray-300"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Analytics Section: Correlation Chart & Monthly Trend Analysis --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Chart Container --}}
        <div class="lg:col-span-8 bg-white p-8 rounded-3xl shadow-xl border border-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">{{ __('Production Trend Correlation') }}</h3>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-mono font-bold">{{ __('Pearson Analysis') }}</span>
                    </div>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest mt-1">
                        {{ __('Objective 1: Latex Yield vs Monthly Rainfall Dynamics') }} 
                        <span class="mx-2 text-gray-300">|</span> 
                        <span class="text-blue-600 font-extrabold">r = {{ number_format($correlationScore, 2) }} ({{ $correlationStrength }})</span>
                    </p>
                </div>

                {{-- Chart Legend Header --}}
                <div class="flex items-center gap-4 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200/60 text-xs font-bold shrink-0">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span>
                        <span class="text-gray-700">{{ __('Latex Yield (kg)') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-blue-300 border border-blue-500 inline-block"></span>
                        <span class="text-gray-700">{{ __('Rainfall (mm)') }}</span>
                    </div>
                </div>
            </div>

            <div class="h-64 relative">
                <canvas id="productionWeatherChart"></canvas>
            </div>

            {{-- Chart Insights --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-6">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cloud-showers-heavy text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Rainfall Dynamics (mm)') }}</p>
                        <p class="text-xs font-bold text-gray-700 leading-tight">{{ __('Monthly cumulative precipitation recorded via local gauges.') }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-line text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">{{ __('Latex Yield (kg)') }}</p>
                        <p class="text-xs font-bold text-gray-700 leading-tight">{{ __('Aggregated dry rubber output collected per harvest cycle.') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                        <span class="text-lg">💡</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-emerald-700 tracking-wider">{{ __('Agronomic Insight') }}</p>
                        <p class="text-[11px] font-medium text-gray-700 leading-tight">{{ $userAdvice }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly DSS Recommendations Sidebar --}}
        <div class="lg:col-span-4 bg-gray-900 p-8 rounded-3xl shadow-2xl text-white flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-emerald-400">{{ __('Monthly DSS Forecast') }}</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ __('Historical score progression') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-white/10 rounded text-[9px] font-mono text-emerald-300">{{ __('12-Mo Cycle') }}</span>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-[420px] custom-scrollbar pr-2">
                    @foreach($monthlyDSS as $dss)
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/[0.03] border border-white/10 hover:bg-white/[0.07] transition-all">
                        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-gray-800 border border-white/5 shadow-inner">
                            <span class="text-xl font-black {{ $dss['score'] >= 7 ? 'text-emerald-400' : ($dss['score'] >= 4 ? 'text-amber-400' : 'text-rose-400') }}">
                                {{ str_pad($dss['score'], 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-black uppercase tracking-wider text-white truncate">{{ __($dss['month']) }}</p>
                                <span class="text-[9px] font-mono px-1.5 py-0.5 rounded {{ $dss['score'] >= 7 ? 'bg-emerald-500/20 text-emerald-300' : ($dss['score'] >= 4 ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                                    {{ $dss['score'] >= 7 ? __('Optimal') : ($dss['score'] >= 4 ? __('Moderate') : __('Risk')) }}
                                </span>
                            </div>
                            <p class="text-[10px] font-medium text-gray-400 uppercase mt-1 truncate">{{ __($dss['recommendation']) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/10 text-[10px] text-gray-400 flex items-center justify-between">
                <span>{{ __('Algorithm: LATER-X Weighted Index') }}</span>
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
            </div>
        </div>
    </div>

    {{-- 7-Day Harvesting Outlook Section --}}
    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                    {{ __('7-Day Harvesting Outlook') }}
                </h3>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    {{ __('Predictive tapping feasibility based on regional meteorological models and localized rain forecasts.') }}
                </p>
            </div>

            {{-- Status Badges Legend --}}
            <div class="flex flex-wrap items-center gap-2 bg-gray-50 p-2.5 rounded-2xl border border-gray-200/60 text-[11px] font-bold">
                <span class="text-gray-400 uppercase tracking-wider text-[9px] font-black mr-1">{{ __('Status Key:') }}</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ __('Feasible / Optimal') }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> {{ __('Caution / Watch') }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-100 text-rose-800 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> {{ __('High Risk / Rain-out') }}
                </span>
            </div>
        </div>

        {{-- 7-Day Horizontal Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($outlook as $item)
                @php
                    $isToday = $item['day'] === \Carbon\Carbon::now()->translatedFormat('D');
                @endphp
                
                <div 
                    @click="
                        selectedDay = {
                            day: '{{ $item['day'] }}',
                            temp: {{ $item['temp'] }},
                            rain: {{ $item['rain'] }},
                            humidity: {{ $item['humidity'] }},
                            wind: {{ $item['wind'] }},
                            score: {{ $item['score'] ?? $dssScore }},
                            color: '{{ $item['color'] }}',
                            recommendation: '{{ __($item['recommendation']) }}'
                        };
                        openModal = true;
                    "
                    class="flex flex-col items-center p-5 rounded-3xl border-2 transition-all cursor-pointer relative overflow-hidden 
                    {{ $isToday ? 'ring-4 ring-emerald-500/20 border-emerald-500 z-10 scale-105 shadow-2xl bg-white' : 'opacity-90 hover:opacity-100 hover:-translate-y-1 bg-white' }}
                    {{ !$isToday && $item['color'] == 'green' ? 'border-emerald-100 hover:border-emerald-300' : '' }}
                    {{ !$isToday && $item['color'] == 'yellow' ? 'border-amber-100 hover:border-amber-300' : '' }}
                    {{ !$isToday && $item['color'] == 'red' ? 'border-rose-100 hover:border-rose-300' : '' }}"
                >
                    @if($isToday)
                        <div class="absolute top-0 left-0 w-full bg-emerald-500 text-[9px] text-white font-black uppercase text-center py-0.5 tracking-widest animate-pulse">
                            {{ __('Today') }}
                        </div>
                    @endif
                    
                    <p class="text-[10px] font-black {{ $isToday ? 'text-emerald-600 mt-2' : 'text-gray-400' }} uppercase tracking-widest mb-1">
                        {{ __($item['day']) }}
                    </p>
                    
                    <span class="text-3xl my-2 drop-shadow-sm">
                        {!! $item['rain'] > 5 ? '🌧️' : ($item['rain'] > 0 ? '🌦️' : '☀️') !!}
                    </span>

                    <span class="text-[10px] font-mono font-bold text-gray-500 mb-2">
                        {{ $item['rain'] }} mm
                    </span>

                    <p class="text-[10px] text-center font-black uppercase leading-tight 
                        {{ $item['color'] == 'green' ? 'text-emerald-600' : ($item['color'] == 'yellow' ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ __($item['recommendation']) }}
                    </p>

                    <span class="mt-3 text-[8px] font-black text-gray-400 hover:text-emerald-500 uppercase tracking-wider">
                        {{ __('Details') }} &rarr;
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Decision Support System per Plot Table Section --}}
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xl border border-gray-100" 
        x-data="{
            searchQuery: '',
            perPage: 10,
            currentPage: 1,
            plots: @js($plotDSS),
            
            // Plot Detail Modal State
            selectedPlot: null,
            showPlotModal: false,

            openPlotDetails(plot) {
                this.selectedPlot = plot;
                this.showPlotModal = true;
            },

            closePlotDetails() {
                this.showPlotModal = false;
                this.selectedPlot = null;
            },
            
            get filteredPlots() {
                if (!this.searchQuery.trim()) {
                    return this.plots;
                }
                const query = this.searchQuery.toLowerCase();
                return this.plots.filter(plot => 
                    (plot.plot_code && plot.plot_code.toLowerCase().includes(query)) ||
                    (plot.plot_location && plot.plot_location.toLowerCase().includes(query)) ||
                    (plot.farmer_name && plot.farmer_name.toLowerCase().includes(query)) ||
                    (plot.peak_month && plot.peak_month.toLowerCase().includes(query))
                );
            },

            get paginatedPlots() {
                const limit = parseInt(this.perPage);
                const start = (this.currentPage - 1) * limit;
                const end = start + limit;
                return this.filteredPlots.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.filteredPlots.length / parseInt(this.perPage)) || 1;
            },

            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },

            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },

            resetPage() {
                this.currentPage = 1;
            }
        }">
        
        {{-- Table Header & Controls --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-gray-100 mb-6">
            <div>
                <h3 class="text-xl font-black text-gray-800 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-brain text-emerald-600"></i>
                    {{ __('Decision Support Analysis per Plot & Farmer') }}
                </h3>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    {{ __('Select a plot row to view detailed DSS analytics, 7-day harvesting outlooks, and agronomic recommendations.') }}
                </p>
            </div>

            {{-- Controls --}}
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                <div class="relative w-full sm:w-72">
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input="resetPage()"
                        placeholder="{{ __('Search plot, farmer, or peak month...') }}" 
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition-all"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                        {{ __('Show') }}
                    </label>
                    <select 
                        x-model="perPage" 
                        @change="resetPage()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all cursor-pointer"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-black uppercase tracking-wider text-gray-400">
                        <th class="px-4 py-3.5">{{ __('Plot Code & Location') }}</th>
                        <th class="px-4 py-3.5">{{ __('Farmer Name') }}</th>
                        <th class="px-4 py-3.5 text-center">{{ __('Peak Month') }}</th>
                        <th class="px-4 py-3.5 text-right">{{ __('Dry Rubber (kg)') }}</th>
                        <th class="px-4 py-3.5 text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                    <template x-for="item in paginatedPlots" :key="item.plot_code">
                        <tr class="hover:bg-emerald-50/50 transition-colors cursor-pointer" @click="openPlotDetails(item)">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-gray-800" x-text="item.plot_location"></div>
                                <span class="inline-block px-2 py-0.5 mt-0.5 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded" x-text="'#' + item.plot_code"></span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-700 whitespace-nowrap" x-text="item.farmer_name"></td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[11px]">
                                    <i class="fa-solid fa-calendar-check text-[10px]"></i>
                                    <span x-text="item.peak_month || 'N/A'"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-black text-emerald-600 whitespace-nowrap" x-text="Number(item.total_dry_weight || 0).toFixed(2)"></td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap" @click.stop>
                                <button 
                                    @click="openPlotDetails(item)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[11px] transition-all shadow-sm shadow-emerald-200"
                                >
                                    <i class="fa-solid fa-calendar-week text-[10px]"></i>
                                    {{ __('View Outlook') }}
                                </button>
                            </td>
                        </tr>
                    </template>
                    
                    {{-- Empty State --}}
                    <tr x-show="filteredPlots.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 font-bold">
                            <i class="fa-solid fa-folder-open text-2xl text-gray-300 mb-2 block"></i>
                            {{ __('No matching plot, farmer, or peak month records found.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Controls --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-gray-100" x-show="filteredPlots.length > 0">
            <p class="text-xs text-gray-500 font-medium">
                {{ __('Showing') }} 
                <span class="font-bold text-gray-800" x-text="((currentPage - 1) * parseInt(perPage)) + 1"></span> 
                {{ __('to') }} 
                <span class="font-bold text-gray-800" x-text="Math.min(currentPage * parseInt(perPage), filteredPlots.length)"></span> 
                {{ __('of') }} 
                <span class="font-bold text-gray-800" x-text="filteredPlots.length"></span> 
                {{ __('entries') }}
            </p>

            <div class="flex items-center gap-2">
                <button 
                    @click="prevPage()" 
                    :disabled="currentPage === 1"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:bg-emerald-600 hover:text-white': currentPage > 1 }"
                    class="px-3.5 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 transition-all"
                >
                    <i class="fa-solid fa-chevron-left mr-1"></i> {{ __('Previous') }}
                </button>

                <span class="text-xs font-bold text-gray-600 px-2">
                    <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                </span>

                <button 
                    @click="nextPage()" 
                    :disabled="currentPage === totalPages"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:bg-emerald-600 hover:text-white': currentPage < totalPages }"
                    class="px-3.5 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 transition-all"
                >
                    {{ __('Next') }} <i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>

        {{-- MODAL: PLOT DETAILS, DSS SCORE, DRC %, & 7-DAY OUTLOOK --}}
        <template x-teleport="body">
            <div 
                x-show="showPlotModal" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto"
                x-cloak
            >
                <div 
                    @click.away="closePlotDetails()"
                    class="bg-white rounded-3xl max-w-2xl w-full p-6 md:p-8 shadow-2xl border border-gray-100 relative overflow-hidden transform transition-all"
                >
                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded" x-text="'#' + (selectedPlot?.plot_code || '')"></span>
                                <span class="text-xs font-semibold text-gray-400" x-text="selectedPlot?.farmer_name"></span>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 mt-1" x-text="(selectedPlot?.plot_location || '') + ' Detailed Analysis'"></h3>
                        </div>
                        <button @click="closePlotDetails()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="mt-6 space-y-6">
                        {{-- Agronomic DSS Status & Score Banner --}}
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start gap-3">
                            <div class="p-2.5 rounded-xl text-white font-bold text-sm shrink-0" 
                                :class="{
                                    'bg-emerald-600': (selectedPlot?.dss_score ?? 0) >= 7,
                                    'bg-amber-500': (selectedPlot?.dss_score ?? 0) >= 4 && (selectedPlot?.dss_score ?? 0) < 7,
                                    'bg-rose-600': (selectedPlot?.dss_score ?? 0) < 4
                                }">
                                <i class="fa-solid fa-lightbulb"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ __('DSS Recommendation') }}</h4>
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-full"
                                        :class="{
                                            'bg-emerald-100 text-emerald-800': (selectedPlot?.dss_score ?? 0) >= 7,
                                            'bg-amber-100 text-amber-800': (selectedPlot?.dss_score ?? 0) >= 4 && (selectedPlot?.dss_score ?? 0) < 7,
                                            'bg-rose-100 text-rose-800': (selectedPlot?.dss_score ?? 0) < 4
                                        }"
                                        x-text="'Score: ' + (selectedPlot?.dss_score || 0) + ' / 10'"></span>
                                </div>
                                <p class="text-xs font-bold text-gray-700 mt-1" x-text="selectedPlot?.dss_recommendation || 'No recommendation available.'"></p>
                            </div>
                        </div>

                        {{-- 7-Day Harvesting Outlook Section --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-black text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-week text-emerald-600"></i>
                                    {{ __('7-Day Harvesting Outlook') }}
                                </h4>
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                    {{ __('Predicted Yield vs Weather') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-7 gap-1.5 text-center">
                                <template x-for="(day, index) in (selectedPlot?.outlook || [
                                    { day: 'Mon', yield: '18kg', status: 'Optimal', rain: '10%' },
                                    { day: 'Tue', yield: '20kg', status: 'Optimal', rain: '0%' },
                                    { day: 'Wed', yield: '12kg', status: 'Rain Risk', rain: '75%' },
                                    { day: 'Thu', yield: '0kg', status: 'Rest Day', rain: '90%' },
                                    { day: 'Fri', yield: '16kg', status: 'Moderate', rain: '30%' },
                                    { day: 'Sat', yield: '22kg', status: 'Optimal', rain: '5%' },
                                    { day: 'Sun', yield: '19kg', status: 'Optimal', rain: '15%' }
                                ])" :key="index">
                                    <div class="p-2 rounded-xl border border-gray-100 flex flex-col items-center justify-between h-28"
                                        :class="{
                                            'bg-emerald-50/60 border-emerald-200': day.status === 'Optimal',
                                            'bg-amber-50/60 border-amber-200': day.status === 'Moderate',
                                            'bg-rose-50/60 border-rose-200': day.status === 'Rain Risk' || day.status === 'Rest Day'
                                        }">
                                        <span class="text-[10px] font-black text-gray-500 uppercase" x-text="day.day"></span>
                                        
                                        <i class="fa-solid text-sm my-1" 
                                        :class="{
                                            'fa-sun text-amber-500': day.rain === '0%' || day.rain === '5%' || day.rain === '10%',
                                            'fa-cloud-sun text-emerald-600': day.rain === '15%' || day.rain === '30%',
                                            'fa-cloud-showers-heavy text-rose-500': parseInt(day.rain) > 50
                                        }"></i>

                                        <div>
                                            <div class="text-[11px] font-black text-gray-800" x-text="day.yield"></div>
                                            <div class="text-[9px] font-bold text-gray-400" x-text="day.rain + ' rain'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Metrics Details Summary Grid --}}
                        <div class="grid grid-cols-3 gap-3 pt-2">
                            <div class="p-3 bg-gray-50 rounded-2xl text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ __('Total Dry Rubber') }}</span>
                                <span class="text-sm font-black text-emerald-600" x-text="Number(selectedPlot?.total_dry_weight || 0).toFixed(2) + ' kg'"></span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-2xl text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ __('Avg DRC (%)') }}</span>
                                <span class="text-sm font-black text-sky-600" x-text="Number(selectedPlot?.avg_drc || 0).toFixed(2) + '%'"></span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-2xl text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ __('Peak Harvest') }}</span>
                                <span class="text-sm font-black text-indigo-600" x-text="selectedPlot?.peak_month || 'N/A'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- DYNAMIC HARVESTING OUTLOOK DETAIL MODAL --}}
    <template x-teleport="body">
        <div 
            x-show="openModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto"
            x-cloak
        >
            <div 
                @click.away="openModal = false"
                class="bg-white rounded-3xl max-w-2xl w-full p-6 md:p-8 shadow-2xl border border-gray-100 relative overflow-hidden transform transition-all"
            >
                {{-- Modal Header --}}
                <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded">
                                {{ __('MICROCLIMATE ANALYTICS') }}
                            </span>
                            <span class="text-xs font-semibold text-gray-400" x-text="selectedDay.day"></span>
                        </div>
                        <h3 class="text-xl font-black text-gray-800 mt-1">
                            {{ __('Harvesting Conditions Analytics') }}
                        </h3>
                    </div>
                    <button @click="openModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    {{-- Agronomic DSS Status Banner --}}
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start gap-3">
                        <div 
                            class="p-2.5 rounded-xl text-white font-bold text-sm shrink-0"
                            :class="{
                                'bg-emerald-600': selectedDay.score >= 7,
                                'bg-amber-500': selectedDay.score >= 4 && selectedDay.score < 7,
                                'bg-rose-600': selectedDay.score < 4
                            }"
                        >
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">
                                    {{ __('Decision Support Recommendation') }}
                                </h4>
                                <span 
                                    class="text-xs font-black px-2.5 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800': selectedDay.score >= 7,
                                        'bg-amber-100 text-amber-800': selectedDay.score >= 4 && selectedDay.score < 7,
                                        'bg-rose-100 text-rose-800': selectedDay.score < 4
                                    }"
                                >
                                    {{ __('Score:') }} <span x-text="selectedDay.score"></span> / 10
                                </span>
                            </div>
                            <p class="text-xs font-bold text-gray-700 mt-1" x-text="selectedDay.recommendation"></p>
                        </div>
                    </div>

                    {{-- DSS Score Index Legend --}}
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">{{ __('DSS Score Index Legend') }}</p>
                        <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold">
                            <div class="flex items-center gap-1.5 text-emerald-800 bg-emerald-50 p-1.5 rounded-lg border border-emerald-200">
                                <span class="font-black">7 - 10:</span> {{ __('Safe to Tap') }}
                            </div>
                            <div class="flex items-center gap-1.5 text-amber-800 bg-amber-50 p-1.5 rounded-lg border border-amber-200">
                                <span class="font-black">4 - 6:</span> {{ __('Tap with Rain Guard') }}
                            </div>
                            <div class="flex items-center gap-1.5 text-rose-800 bg-rose-50 p-1.5 rounded-lg border border-rose-200">
                                <span class="font-black">0 - 3:</span> {{ __('Suspend Tapping') }}
                            </div>
                        </div>
                    </div>

                    {{-- Microclimate Metrics Grid --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                {{ __('Atmospheric Variables') }}
                            </h4>
                            <span class="text-[10px] font-bold text-gray-400">
                                <i class="fa-solid fa-circle-info mr-0.5"></i> {{ __('Live Open-Meteo Feed') }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <i class="fa-solid fa-temperature-high text-orange-500 mb-1"></i>
                                <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Temperature (°C)') }}</p>
                                <p class="text-sm font-black text-gray-800"><span x-text="selectedDay.temp"></span>°C</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <i class="fa-solid fa-droplet text-blue-500 mb-1"></i>
                                <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Rainfall Vol. (mm)') }}</p>
                                <p class="text-sm font-black text-gray-800"><span x-text="selectedDay.rain"></span> mm</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <i class="fa-solid fa-water text-emerald-500 mb-1"></i>
                                <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Relative Humidity (%)') }}</p>
                                <p class="text-sm font-black text-gray-800"><span x-text="selectedDay.humidity"></span>%</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <i class="fa-solid fa-wind text-teal-500 mb-1"></i>
                                <p class="text-[9px] font-black text-gray-400 uppercase">{{ __('Wind Speed (m/s)') }}</p>
                                <p class="text-sm font-black text-gray-800"><span x-text="selectedDay.wind"></span> <span class="text-[8px]">m/s</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Actions --}}
                    <button 
                        @click="openModal = false" 
                        class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-lg shadow-emerald-200"
                    >
                        {{ __('Close Analytics View') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Chart Execution Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvasElement = document.getElementById('productionWeatherChart');
        if (!canvasElement) return;

        const ctx = canvasElement.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [
                    {
                        label: 'Rainfall (mm)',
                        data: @json($rainfallData ?? []),
                        backgroundColor: 'rgba(54, 162, 235, 0.35)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'yRain',
                        type: 'bar'
                    },
                    {
                        label: 'Latex Yield (kg)',
                        data: @json($productionData ?? []),
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
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
                layout: {
                    padding: {
                        top: 5,
                        bottom: 0
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + (context.dataset.type === 'bar' ? ' mm' : ' kg');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    yYield: { 
                        position: 'left', 
                        title: { display: true, text: 'Latex Yield (kg)', font: { weight: 'bold', size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    yRain: { 
                        position: 'right', 
                        grid: { drawOnChartArea: false }, 
                        title: { display: true, text: 'Rainfall (mm)', font: { weight: 'bold', size: 10 } },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endsection