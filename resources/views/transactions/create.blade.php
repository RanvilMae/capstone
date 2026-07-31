@extends('layouts.app')

@section('title', __('Add Latex Transaction'))

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-5xl animate-fade-in">

    {{-- Success Alert --}}
    @if(session('success'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="fixed top-6 right-6 z-50 flex items-center bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-2xl transition transform duration-300 border border-emerald-400/30"
        x-transition:enter="transform ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transform ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
    >
        <svg class="w-5 h-5 mr-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill="currentColor" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
        </svg>
        <span class="text-sm font-bold">{{ __(session('success')) }}</span>
        <button @click="show = false" class="ml-4 text-white/80 hover:text-white font-black text-lg">&times;</button>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100">
        {{-- Title Section --}}
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    {{ __('Add Latex Transaction') }}
                </h2>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                    {{ __('Logged in as') }}: {{ auth()->user()->hasRole('admin') ? __('Administrator') : __('Staff Member') }}
                </p>
            </div>
            <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
            </div>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Location Field --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                        {{ __('Location') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="location" required
                            class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm"
                            placeholder="{{ __('Enter collection location') }}">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                {{-- Plot Dropdown (Includes Plot Code) --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                        {{ __('Plot & Farmer') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="plot_id" required
                            class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm appearance-none">
                            <option value="" disabled selected>{{ __('Select Rubber Plot') }}</option>
                            @foreach($plots as $plot)
                                <option value="{{ $plot->id }}">
                                    [{{ $plot->code ?? 'NO-CODE' }}] {{ $plot->plot_location }} — {{ __('Farmer') }}: {{ $plot->farmer->name ?? __('Unknown') }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Date Input --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                        {{ __('Transaction Date') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" name="transaction_date" required
                            class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-medium text-gray-800 text-sm"
                            value="{{ date('Y-m-d') }}">
                        <i class="fa-solid fa-calendar-day absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                {{-- Fresh Weight --}}
                <div>
                    <label class="block mb-2 text-xs font-black uppercase tracking-wider text-emerald-700">
                        {{ __('Fresh Weight (kg)') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="freshWeight" type="number" step="0.01" name="volume_kg" required
                            class="w-full p-3.5 pl-11 bg-emerald-50/50 border border-emerald-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-black text-gray-800 text-sm calc"
                            placeholder="0.00">
                        <i class="fa-solid fa-weight-hanging absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            {{-- 3-Sample Testing Cards --}}
            <div>
                <label class="block mb-3 text-xs font-black uppercase tracking-wider text-gray-500">
                    {{ __('DRC Field Samples') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="bg-gray-50/80 p-5 rounded-3xl border border-gray-100 space-y-4 relative overflow-hidden group hover:border-emerald-200 hover:bg-emerald-50/30 transition-all">
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                            <span class="text-xs font-black uppercase tracking-wider text-gray-600">{{ __('Sample') }} {{ $i }}</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <div>
                            <label class="block mb-1 text-[11px] font-bold uppercase text-gray-500">{{ __('DRC (%)') }} <span class="text-rose-500">*</span></label>
                            <input name="drc_sample_{{ $i }}" type="number" step="0.01" required
                                class="w-full p-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-bold text-gray-800 calc drc"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block mb-1 text-[11px] font-bold uppercase text-gray-400">{{ __('Dry Weight (kg)') }}</label>
                            <input name="dry_sample_{{ $i }}" type="number" step="0.01"
                                class="w-full p-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 calc drysample"
                                placeholder="0.00" readonly>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Aggregated Calculation Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div class="p-5 bg-gradient-to-br from-emerald-500 to-green-600 rounded-3xl text-white shadow-lg">
                    <label class="block mb-1 text-xs font-black uppercase tracking-wider text-emerald-100">{{ __('Average DRC (%)') }}</label>
                    <input id="avgDRC" name="dry_rubber_content"
                        class="w-full bg-transparent border-0 text-3xl font-black text-white focus:outline-none placeholder-white/50"
                        placeholder="0.00%" readonly required>
                </div>

                <div class="p-5 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl text-white shadow-lg">
                    <label class="block mb-1 text-xs font-black uppercase tracking-wider text-emerald-100">{{ __('Calculated Dry Rubber Weight (kg)') }}</label>
                    <input id="dryWeight" name="dry_rubber_weight_kg"
                        class="w-full bg-transparent border-0 text-3xl font-black text-white focus:outline-none placeholder-white/50"
                        placeholder="0.00 kg" readonly>
                </div>
            </div>

            {{-- Pricing Field --}}
            <div>
                <label class="block mb-2 text-xs font-black uppercase tracking-wider text-gray-500">
                    {{ __('Price per kg (THB ฿)') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" step="0.01" name="price_per_kg" required
                        class="w-full p-3.5 pl-11 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all font-bold text-gray-800 text-sm"
                        placeholder="0.00">
                    <i class="fa-solid fa-baht-sign absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- Submit Action --}}
            <button type="submit"
                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl transition-all shadow-xl shadow-emerald-200">
                <i class="fa-solid fa-check-circle mr-2"></i>{{ __('Save Transaction') }}
            </button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.calc').forEach(input => {
    input.addEventListener('input', calculate);
});

function calculate() {
    let fresh = parseFloat(document.getElementById('freshWeight').value) || 0;

    let drcs = [...document.querySelectorAll('.drc')]
        .map(x => parseFloat(x.value))
        .filter(x => !isNaN(x));

    if (drcs.length === 0) return;

    let avg = drcs.reduce((a, b) => a + b, 0) / drcs.length;
    let dry = fresh * (avg / 100);

    document.getElementById('avgDRC').value = avg.toFixed(2);
    document.getElementById('dryWeight').value = dry.toFixed(2);

    document.querySelectorAll('.drysample').forEach((input, index) => {
        if (!isNaN(drcs[index])) {
            input.value = (fresh * (drcs[index] / 100)).toFixed(2);
        }
    });
}
</script>
@endsection