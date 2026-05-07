<div class="p-4 bg-white shadow rounded-xl hover:shadow-xl transition duration-300">
    {{-- Translate the label (e.g., "Total") --}}
    <p class="text-sm text-gray-500">{{ __($label) }}</p>
    
    {{-- Translate the title (e.g., "Farmers") --}}
    <p class="mb-6 text-2xl font-bold text-gray-800">
        {!! $icon !!} {{ __($title) }}
    </p>
    
    <p class="font-bold text-center text-green-800 text-7xl">{{ $value }}</p>
</div>