@extends('layouts.app')

@section('title', __('Yield & Sales Predictor'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="dssDecisionSupport()">
    
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-700 text-white rounded-3xl p-6 md:p-8 shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-emerald-500/30 text-emerald-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-emerald-400/30">
                    Smart Farm Assistant
                </span>
                <span class="text-xs text-emerald-200">• Income & Yield Helper</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black mt-2 tracking-tight">Rubber Harvest & Income Planner</h2>
            <p class="text-xs md:text-sm text-emerald-100/90 mt-1 max-w-2xl">
                Calculate today's latex payout or plan your tapping schedule to hit your money goals.
            </p>
        </div>
        
        <button @click="loadPlotData()" :disabled="loading" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition border border-white/20 shadow-sm disabled:opacity-50 cursor-pointer shrink-0">
            <i class="fa-solid fa-arrows-rotate" :class="{'animate-spin': loading}"></i>
            <span x-text="loading ? 'Updating...' : 'Refresh Data'"></span>
        </button>
    </div>

    <!-- Mode Selector Tabs -->
    <div class="flex bg-gray-100 p-1.5 rounded-2xl max-w-md border border-gray-200 shadow-inner">
        <button @click="activeTab = 'calculator'" 
                :class="activeTab === 'calculator' ? 'bg-white text-emerald-800 shadow-sm font-extrabold' : 'text-gray-500 font-bold hover:text-gray-700'" 
                class="flex-1 py-2.5 text-xs rounded-xl transition text-center cursor-pointer flex items-center justify-center gap-2">
            <i class="fa-solid fa-calculator text-emerald-600"></i>
            <span>Today's Earnings</span>
        </button>
        <button @click="activeTab = 'forecaster'; if(!forecastResults) runForecast();" 
                :class="activeTab === 'forecaster' ? 'bg-white text-emerald-800 shadow-sm font-extrabold' : 'text-gray-500 font-bold hover:text-gray-700'" 
                class="flex-1 py-2.5 text-xs rounded-xl transition text-center cursor-pointer flex items-center justify-center gap-2">
            <i class="fa-solid fa-chart-line text-teal-600"></i>
            <span>Income Planner</span>
        </button>
    </div>

    <!-- Global Plot Selection Header -->
    <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-1/2">
            <div class="flex items-center gap-2 mb-1">
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider">Select Land Plot</label>
                <!-- Tooltip Badge -->
                <div class="relative flex items-center group cursor-pointer">
                    <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600 transition"></i>
                    <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-64 bg-gray-900 text-white text-[11px] font-medium p-2.5 rounded-xl shadow-xl z-30 leading-snug">
                        Select the land plot you are working on to view its past performance and location.
                    </div>
                </div>
            </div>
            <select x-model.number="selectedPlotId" @change="onPlotChange()" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 rounded-xl p-3 text-sm font-bold text-gray-800 transition">
                @forelse($plots ?? \App\Models\Plot::all() as $plot)
                    <option value="{{ $plot->id }}">
                        {{ $plot->code ?? 'Plot #' . $plot->id }} — {{ $plot->plot_location ?? 'Location' }} ({{ $plot->plot_size_rai ?? 0 }} Rai)
                    </option>
                @empty
                    <option value="" disabled selected>No plots available. Please add a plot first.</option>
                @endforelse
            </select>
        </div>

        <!-- Field Attributes Summary -->
        <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
            <div class="text-left">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Location</p>
                <p class="text-xs font-bold text-gray-800" x-text="plotSummary?.plot_location || '—'"></p>
            </div>
            <div class="text-left">
                <div class="flex items-center gap-1">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Land Size</p>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-[10px] text-gray-300 hover:text-emerald-600"></i>
                        <div class="absolute bottom-full mb-2 right-0 hidden group-hover:block w-48 bg-gray-900 text-white text-[10px] p-2 rounded-xl shadow-xl z-30">
                            1 Rai = 1,600 m².
                        </div>
                    </div>
                </div>
                <p class="text-xs font-bold text-emerald-700" x-text="(plotSummary?.plot_size_rai || 0) + ' Rai'"></p>
            </div>
            <div class="text-left">
                <div class="flex items-center gap-1">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Average Yield</p>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-[10px] text-gray-300 hover:text-emerald-600"></i>
                        <div class="absolute bottom-full mb-2 right-0 hidden group-hover:block w-52 bg-gray-900 text-white text-[10px] p-2 rounded-xl shadow-xl z-30">
                            Average dry rubber produced per Rai based on past harvests.
                        </div>
                    </div>
                </div>
                <p class="text-xs font-bold text-indigo-700" x-text="(plotSummary?.yield_density_kg_per_rai || 0) + ' kg/Rai'"></p>
            </div>
        </div>
    </div>

    <!-- TAB 1: TODAY'S EARNINGS (INSTANT CALCULATOR) -->
    <div x-show="activeTab === 'calculator'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        
        <!-- Input Panel -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-sm uppercase tracking-wider">
                        <i class="fa-solid fa-sliders text-emerald-600 mr-2"></i> Harvest Details
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Enter today's numbers to see your pay</p>
                </div>
                <span class="text-[10px] font-black uppercase text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Instant</span>
            </div>

            <!-- Volume Slider -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase">1. Raw Latex Weight</label>
                        <div class="relative flex items-center group cursor-pointer">
                            <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                            <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-56 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                                Total wet latex collected today in kilograms.
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md" x-text="volumeKg + ' kg'"></span>
                </div>
                <input type="range" min="10" max="1000" step="10" x-model.number="volumeKg" @input.debounce.300ms="calculateRevenue()" class="w-full accent-emerald-600 bg-gray-100 rounded-lg h-2 cursor-pointer">
                <div class="flex justify-between text-[10px] font-bold text-gray-400 mt-1">
                    <span>10 kg</span>
                    <span>500 kg</span>
                    <span>1,000 kg</span>
                </div>
            </div>

            <!-- DRC Slider -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase">2. Rubber Quality (DRC %)</label>
                        <div class="relative flex items-center group cursor-pointer">
                            <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-teal-600"></i>
                            <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-60 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                                Dry Rubber Content percentage (usually between 28% and 40%).
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-black text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-md" x-text="drcPercent + ' %'"></span>
                </div>
                <input type="range" min="10" max="50" step="0.5" x-model.number="drcPercent" @input.debounce.300ms="calculateRevenue()" class="w-full accent-teal-600 bg-gray-100 rounded-lg h-2 cursor-pointer">
                <div class="flex justify-between text-[10px] font-bold text-gray-400 mt-1">
                    <span>10% (Low)</span>
                    <span>32% (Average)</span>
                    <span>50% (High)</span>
                </div>
            </div>

            <!-- Buying Price -->
            <div>
                <div class="flex items-center gap-1.5 mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">3. Buying Price per kg</label>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                        <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-56 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                            Today's market buying price for dry rubber (in Thai Baht ฿).
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <input type="number" step="0.50" x-model.number="targetPrice" @input.debounce.300ms="calculateRevenue()" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl p-3 pl-9 text-base font-black text-gray-800">
                    <span class="absolute left-3.5 top-3.5 text-gray-400 font-black text-sm">฿</span>
                    <span class="absolute right-3.5 top-3.5 text-xs font-bold text-gray-400">/ kg</span>
                </div>
            </div>
        </div>

        <!-- Output KPI Display -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <!-- Pure Dry Rubber Card -->
                <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                        <i class="fa-solid fa-weight-hanging text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1">
                            <p class="text-[10px] font-extrabold uppercase text-gray-400">Dry Rubber Weight</p>
                            <div class="relative flex items-center group cursor-pointer">
                                <i class="fa-solid fa-circle-info text-[10px] text-gray-300"></i>
                                <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-48 bg-gray-900 text-white text-[10px] p-2 rounded-xl shadow-xl z-30">
                                    Raw Latex (kg) × DRC (%)
                                </div>
                            </div>
                        </div>
                        <p class="text-xl font-black text-gray-800" x-text="(revenueData?.estimated_dry_rubber_kg ?? 0) + ' kg'"></p>
                    </div>
                </div>

                <!-- Estimated Income Card -->
                <div class="bg-white rounded-3xl p-5 border border-emerald-100 bg-emerald-50/30 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-700 shrink-0">
                        <i class="fa-solid fa-coins text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase text-emerald-700">Estimated Payoff</p>
                        <p class="text-xl font-black text-emerald-700" x-text="'฿' + Number(revenueData?.projected_total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                    </div>
                </div>

                <!-- Yield Density Card -->
                <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                        <i class="fa-solid fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase text-gray-400">Plot Yield Rate</p>
                        <p class="text-xl font-black text-gray-800" x-text="(plotSummary?.yield_density_kg_per_rai ?? 0) + ' kg/Rai'"></p>
                    </div>
                </div>
            </div>

            <!-- Smart Selling Recommendation Card -->
            <div class="rounded-3xl p-6 border transition-all duration-300 shadow-sm"
                 :class="{
                     'bg-emerald-50 border-emerald-200 text-emerald-950': dssAdvisory.status === 'optimal',
                     'bg-amber-50 border-amber-200 text-amber-950': dssAdvisory.status === 'warning',
                     'bg-gray-50 border-gray-200 text-gray-800': dssAdvisory.status === 'standard'
                 }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full flex items-center gap-1.5"
                          :class="{
                              'bg-emerald-200/80 text-emerald-900': dssAdvisory.status === 'optimal',
                              'bg-amber-200/80 text-amber-900': dssAdvisory.status === 'warning',
                              'bg-gray-200 text-gray-700': dssAdvisory.status === 'standard'
                          }">
                        <i class="fa-solid fa-lightbulb"></i> Selling Advice
                    </span>
                    <span class="text-[11px] font-bold text-gray-500 uppercase">Recommended Action</span>
                </div>

                <div class="flex items-start gap-3 mt-2">
                    <div class="mt-1 shrink-0">
                        <template x-if="dssAdvisory.status === 'optimal'">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                        </template>
                        <template x-if="dssAdvisory.status === 'warning'">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl"></i>
                        </template>
                        <template x-if="dssAdvisory.status === 'standard'">
                            <i class="fa-solid fa-circle-info text-gray-500 text-xl"></i>
                        </template>
                    </div>
                    <div>
                        <p class="text-base font-bold leading-relaxed" x-text="dssAdvisory.message || 'Calculating recommendation...'"></p>
                        <p class="text-xs text-gray-500 mt-2">
                            * Note: Advice is based on rubber quality and prices to help you decide whether to sell now or process it for a higher profit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: INCOME PLANNER (TARGET FORECASTER) -->
    <div x-show="activeTab === 'forecaster'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in" x-cloak>

        <!-- Left Column: Forecast Inputs -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-sm uppercase tracking-wider">
                        <i class="fa-solid fa-bullseye text-teal-600 mr-2"></i> Goal Settings
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Plan your upcoming harvest goals</p>
                </div>
                <span class="text-[10px] font-black uppercase text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg">Planner</span>
            </div>

            <!-- Error Banner -->
            <template x-if="errorMessage">
                <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span x-text="errorMessage"></span>
                    </div>
                    <button @click="errorMessage = ''" class="text-red-400 hover:text-red-600">&times;</button>
                </div>
            </template>

            <!-- Target Harvest Date -->
            <div>
                <div class="flex items-center gap-1.5 mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Start Date</label>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-teal-600"></i>
                        <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-56 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                            The starting date for tapping.
                        </div>
                    </div>
                </div>
                <input type="date" x-model="forecastDate" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl p-3 text-sm font-bold text-gray-800">
            </div>

            <!-- Scheduled Tapping Days with Presets -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase">Tapping Days</label>
                        <div class="relative flex items-center group cursor-pointer">
                            <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-teal-600"></i>
                            <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-60 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                                Number of active tapping days planned (1 to 31 days).
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-black text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-md" x-text="tappingDays + ' Days'"></span>
                </div>
                <input type="range" min="1" max="31" step="1" x-model.number="tappingDays" class="w-full accent-teal-600 bg-gray-100 rounded-lg h-2 cursor-pointer">
                
                <!-- Quick Selection Presets -->
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase mr-1">Presets:</span>
                    <button @click="tappingDays = 3" class="px-2 py-0.5 bg-gray-100 hover:bg-teal-100 hover:text-teal-800 text-[10px] font-bold text-gray-600 rounded-md transition cursor-pointer">3 Days</button>
                    <button @click="tappingDays = 7" class="px-2 py-0.5 bg-gray-100 hover:bg-teal-100 hover:text-teal-800 text-[10px] font-bold text-gray-600 rounded-md transition cursor-pointer">7 Days</button>
                    <button @click="tappingDays = 14" class="px-2 py-0.5 bg-gray-100 hover:bg-teal-100 hover:text-teal-800 text-[10px] font-bold text-gray-600 rounded-md transition cursor-pointer">14 Days</button>
                    <button @click="tappingDays = 30" class="px-2 py-0.5 bg-gray-100 hover:bg-teal-100 hover:text-teal-800 text-[10px] font-bold text-gray-600 rounded-md transition cursor-pointer">1 Month</button>
                </div>
            </div>

            <!-- Target Revenue Goal -->
            <div>
                <div class="flex items-center gap-1.5 mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Target Income Goal (฿)</label>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                        <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-60 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                            How much money you want to make during this tapping period.
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <input type="number" step="100" x-model.number="targetRevenueGoal" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl p-3 pl-9 text-base font-black text-gray-800">
                    <span class="absolute left-3.5 top-3.5 text-gray-400 font-black text-sm">฿</span>
                </div>
            </div>

            <!-- Forecast DRC (%) -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase">Expected Quality (DRC %)</label>
                        <div class="relative flex items-center group cursor-pointer">
                            <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                            <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-56 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                                Expected average rubber content during this period.
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md" x-text="forecastDrc + ' %'"></span>
                </div>
                <input type="range" min="10" max="50" step="0.5" x-model.number="forecastDrc" class="w-full accent-emerald-600 bg-gray-100 rounded-lg h-2 cursor-pointer">
            </div>

            <!-- Target Market Price -->
            <div>
                <div class="flex items-center gap-1.5 mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Expected Market Price (฿/kg)</label>
                    <div class="relative flex items-center group cursor-pointer">
                        <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                        <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-56 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                            Expected buying price per kg during this period.
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <input type="number" step="0.50" min="1" x-model.number="forecastPrice" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl p-3 pl-9 text-base font-black text-gray-800">
                    <span class="absolute left-3.5 top-3.5 text-gray-400 font-black text-sm">฿</span>
                    <span class="absolute right-3.5 top-3.5 text-xs font-bold text-gray-400">/ kg</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button @click.prevent="runForecast()" :disabled="forecasting || !selectedPlotId" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-black py-3.5 rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                <i class="fa-solid" :class="forecasting ? 'fa-circle-notch animate-spin' : 'fa-chart-line'"></i>
                <span x-text="forecasting ? 'Calculating Goal...' : 'Calculate Goal Feasibility'"></span>
            </button>
        </div>

        <!-- Right Column: Forecast Output & Advisory -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Feasibility Progress Card -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-xs font-black text-gray-800 uppercase tracking-wider">Goal Feasibility</h4>
                            <div class="relative flex items-center group cursor-pointer">
                                <i class="fa-solid fa-circle-info text-xs text-gray-400 hover:text-emerald-600"></i>
                                <div class="absolute bottom-full mb-2 left-0 hidden group-hover:block w-64 bg-gray-900 text-white text-[11px] p-2.5 rounded-xl shadow-xl z-30">
                                    Shows how much of your income target you can reach based on past land yields.
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Projected Income vs. Your Money Goal</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black" 
                              :class="(forecastResults?.completion_rate || 0) >= 100 ? 'text-emerald-600' : 'text-amber-600'" 
                              x-text="Number(forecastResults?.completion_rate || 0).toFixed(1) + '%'"></span>
                        <p class="text-[10px] font-bold text-gray-400 uppercase" x-text="(forecastResults?.completion_rate || 0) >= 100 ? 'Goal Reachable' : 'Below Goal'"></p>
                    </div>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden p-0.5 border border-gray-200">
                    <div class="h-full rounded-full transition-all duration-500 flex items-center justify-end pr-2" 
                         :class="(forecastResults?.completion_rate || 0) >= 100 ? 'bg-emerald-500' : 'bg-amber-500'" 
                         :style="'width: ' + Math.min(forecastResults?.completion_rate || 0, 100) + '%'">
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <!-- Fresh Latex KPI -->
                <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <i class="fa-solid fa-bucket text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase text-gray-400">Est. Raw Latex</p>
                        <p class="text-xl font-black text-gray-800" x-text="(forecastResults?.projected_raw_latex_kg || 0) + ' kg'"></p>
                    </div>
                </div>

                <!-- Dry Rubber KPI -->
                <div class="bg-white rounded-3xl p-5 border border-teal-100 bg-teal-50/20 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 border border-teal-200 flex items-center justify-center text-teal-700 shrink-0">
                        <i class="fa-solid fa-box text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase text-teal-700">Est. Dry Rubber</p>
                        <p class="text-xl font-black text-teal-800" x-text="(forecastResults?.projected_dry_rubber_kg || 0) + ' kg'"></p>
                    </div>
                </div>

                <!-- Projected Revenue KPI -->
                <div class="bg-white rounded-3xl p-5 border border-emerald-100 bg-emerald-50/30 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-700 shrink-0">
                        <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase text-emerald-700">Projected Income</p>
                        <p class="text-xl font-black text-emerald-700" x-text="'฿' + Number(forecastResults?.projected_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                    </div>
                </div>
            </div>

            <!-- Decision Engine Forecast Advisory Card -->
            <div class="rounded-3xl p-6 border transition-all duration-300 shadow-sm"
                 :class="{
                     'bg-emerald-50 border-emerald-200 text-emerald-950': forecastResults?.advisory_status === 'optimal',
                     'bg-amber-50 border-amber-200 text-amber-950': forecastResults?.advisory_status === 'warning',
                     'bg-gray-50 border-gray-200 text-gray-800': forecastResults?.advisory_status === 'standard'
                 }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full flex items-center gap-1.5"
                          :class="{
                              'bg-emerald-200/80 text-emerald-900': forecastResults?.advisory_status === 'optimal',
                              'bg-amber-200/80 text-amber-900': forecastResults?.advisory_status === 'warning',
                              'bg-gray-200 text-gray-700': forecastResults?.advisory_status === 'standard'
                          }">
                        <i class="fa-solid fa-compass"></i> Harvest Advice
                    </span>
                    <span class="text-[11px] font-bold text-gray-500 uppercase">Feasibility Result</span>
                </div>

                <div class="flex items-start gap-3 mt-2">
                    <div class="mt-1 shrink-0">
                        <template x-if="forecastResults?.advisory_status === 'optimal'">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                        </template>
                        <template x-if="forecastResults?.advisory_status === 'warning'">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl"></i>
                        </template>
                        <template x-if="forecastResults?.advisory_status === 'standard'">
                            <i class="fa-solid fa-circle-info text-gray-500 text-xl"></i>
                        </template>
                    </div>
                    <div>
                        <p class="text-base font-bold leading-relaxed" x-text="forecastResults?.advisory_message || 'Run forecast to see advice...'"></p>
                        <p class="text-xs text-gray-500 mt-2">
                            * Note: Evaluates plot history to confirm if your money goal can be met within your scheduled tapping days.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dssDecisionSupport() {
    return {
        activeTab: 'calculator',
        loading: false,
        forecasting: false,
        errorMessage: '',
        plots: @json($plots ?? []),
        selectedPlotId: null,
        
        // Instant Calculator States
        volumeKg: 150,
        drcPercent: 32,
        targetPrice: 55.00,
        revenueData: null,
        plotSummary: null,
        dssAdvisory: { status: 'standard', message: '' },

        // Forecaster States
        forecastDate: new Date().toISOString().split('T')[0],
        tappingDays: 7,
        targetRevenueGoal: 10000,
        forecastDrc: 32.0,
        forecastPrice: 55.00,
        forecastResults: null,

        init() {
            if (this.plots.length > 0) {
                this.selectedPlotId = Number(this.plots[0].id);
                this.loadPlotData();
            }
        },

        onPlotChange() {
            this.selectedPlotId = Number(this.selectedPlotId);
            this.loadPlotData();
            if (this.activeTab === 'forecaster') {
                this.runForecast();
            }
        },

        async loadPlotData() {
            if (!this.selectedPlotId) return;
            this.loading = true;
            try {
                const res = await fetch(`/api/v1/dss/yield-per-plot/${this.selectedPlotId}`);
                const data = await res.json();
                if (data.success) {
                    this.plotSummary = data.data;
                }
            } catch (err) {
                console.error('Fetch Plot Error:', err);
            } finally {
                await this.calculateRevenue();
                this.loading = false;
            }
        },

        async calculateRevenue() {
            if (!this.selectedPlotId) return;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/api/v1/dss/calculate-revenue', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        plot_id: Number(this.selectedPlotId),
                        estimated_volume_kg: Number(this.volumeKg) || 0,
                        estimated_drc: Number(this.drcPercent) || 0,
                        target_price_per_kg: Number(this.targetPrice) || 0
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.revenueData = data.calculation;
                    this.dssAdvisory = data.dss_advisory;
                }
            } catch (err) {
                console.error('Revenue Calculation Error:', err);
            }
        },

        async runForecast() {
            if (!this.selectedPlotId) {
                this.errorMessage = 'Please select a rubber plot first.';
                return;
            }

            this.forecasting = true;
            this.errorMessage = '';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('/api/v1/dss/forecast-production', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        plot_id: Number(this.selectedPlotId),
                        target_harvest_date: this.forecastDate || new Date().toISOString().split('T')[0],
                        scheduled_tapping_days: Number(this.tappingDays) || 1,
                        target_revenue_goal: Number(this.targetRevenueGoal) || 0,
                        estimated_drc: Number(this.forecastDrc) || 0,
                        target_price_per_kg: Math.max(1, Number(this.forecastPrice) || 1)
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.forecastResults = data.forecast;
                } else {
                    if (data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        this.errorMessage = data.errors[firstErrorKey][0];
                    } else {
                        this.errorMessage = data.message || 'Validation failed.';
                    }
                }
            } catch (err) {
                console.error('Forecast Execution Error:', err);
                this.errorMessage = 'Network error or invalid server response.';
            } finally {
                this.forecasting = false;
            }
        }
    }
}
</script>
@endsection