@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        {{-- Dynamic Title based on Role --}}
        <h2 class="mb-6 text-3xl font-extrabold text-green-700">
            {{ __('Add Latex Transaction') }} 
            <span class="text-red-500">*</span> 
            ({{ auth()->user()->hasRole('admin') ? __('Admin') : __('Staff') }})
        </h2>

        {{-- Success Alert --}}
        @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)" 
            class="fixed top-6 right-6 z-50 flex items-center bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transition transform duration-300"
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transform ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
        >
            <svg class="w-5 h-5 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill="currentColor" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
            </svg>
            <span>{{ __(session('success')) }}</span>
            <button @click="show = false" class="ml-4 text-white hover:text-gray-200">&times;</button>
        </div>
        @endif

        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="block mb-2 font-semibold text-gray-700">
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Location') }} <span class="text-red-500">*</span></label>
                <input type="text" name="location" required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                       placeholder="{{ __('Enter location') }}">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Plot') }} <span class="text-red-500">*</span></label>
                <select name="plot_id" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                    @foreach($plots as $plot)
                        <option value="{{ $plot->id }}">
                            {{ $plot->plot_location }} - {{ __('Farmer') }}: {{ $plot->farmer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Transaction Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">{{ __('Fresh Weight (kg)') }} <span class="text-red-500">*</span></label>
                    <input id="freshWeight" type="number" step="0.01" name="volume_kg" required
                           class="w-full p-3 border border-green-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-400 focus:outline-none calc"
                           placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for ($i = 1; $i <= 3; $i++)
                <div class="bg-green-50 p-4 rounded-xl shadow-inner border-l-4 border-green-400 space-y-3 transition-all duration-300 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-green-700 mb-2">{{ __('Sample') }} {{ $i }}</h3>
                    <div>
                        <label class="block mb-1 text-gray-700">{{ __('DRC (%)') }} <span class="text-red-500">*</span></label>
                        <input name="drc_sample_{{ $i }}" type="number" step="0.01" required
                               class="w-full p-2 border border-gray-300 rounded-lg calc drc"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block mb-1 text-gray-700">{{ __('Dry Weight (kg)') }}</label>
                        <input name="dry_sample_{{ $i }}" type="number" step="0.01"
                               class="w-full p-2 border border-gray-300 rounded-lg calc drysample bg-gray-50"
                               placeholder="0.00" readonly>
                    </div>
                </div>
                @endfor
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-green-100 p-4 rounded-xl shadow-inner border-l-4 border-green-500 transition-all duration-300 hover:shadow-md">
                    <label class="block mb-2 font-semibold text-green-800">{{ __('Average DRC (%)') }} <span class="text-red-500">*</span></label>
                    <input id="avgDRC" name="dry_rubber_content"
                           class="w-full p-3 border border-gray-300 rounded-lg bg-green-50 font-semibold text-green-700"
                           readonly required>
                </div>

                <div class="bg-green-100 p-4 rounded-xl shadow-inner border-l-4 border-green-500 transition-all duration-300 hover:shadow-md">
                    <label class="block mb-2 font-semibold text-green-800">{{ __('Dry Rubber Weight (kg)') }}</label>
                    <input id="dryWeight" name="dry_rubber_weight_kg"
                           class="w-full p-3 border border-gray-300 rounded-lg bg-green-50 font-semibold text-green-700"
                           readonly>
                </div>
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">{{ __('Price per kg (Baht)') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="price_per_kg" required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                       placeholder="0.00">
            </div>

            <button type="submit"
                    class="w-full py-3 mt-4 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors duration-300">
                {{ __('Save Transaction') }}
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

    let avg = drcs.reduce((a,b)=>a+b,0) / drcs.length;
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