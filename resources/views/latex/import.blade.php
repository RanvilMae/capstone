@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 relative overflow-hidden">
        
        <!-- Loading Overlay -->
        <div id="loading-overlay" class="hidden absolute inset-0 bg-white/90 backdrop-blur-sm z-50 flex-col items-center justify-center text-center p-6 transition-all duration-300">
            <div class="relative flex items-center justify-center mb-4">
                <div class="w-16 h-16 border-4 border-green-200 border-t-green-600 rounded-full animate-spin"></div>
                <svg class="w-8 h-8 text-green-600 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800">{{ __('Ingesting & Processing Spreadsheet...') }}</h3>
            <p class="text-sm text-gray-500 mt-2 max-w-sm">{{ __('Please wait while we parse records, link plots, calculate DRC metrics, and record transactions.') }}</p>
        </div>

        <!-- Header & Back Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-green-800">{{ __('Batch Spreadsheet Ingestion') }}</h2>
                <p class="text-gray-500 text-xs mt-1">{{ __('Upload production spreadsheets (.xlsx, .xls, .csv) to auto-populate production logs and calculate DRC metrics.') }}</p>
            </div>
            
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-all shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to Transactions') }}
            </a>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-800 text-sm">
                <p class="font-bold">{{ __('Success') }}</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-800 text-sm">
                <p class="font-bold">{{ __('Import Failed') }}</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-800 text-sm">
                <p class="font-bold mb-1">{{ __('Validation Errors') }}</p>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- File Upload Form -->
        <form id="upload-form" action="{{ route('latex.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoadingState(event)">
            @csrf
            
            <!-- Import Template Format Selector -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    {{ __('Select Data Format') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Format 1: Standard with Plot & Sample Code -->
                    <label class="relative flex flex-col p-4 bg-gray-50 border-2 border-green-600 rounded-2xl cursor-pointer hover:bg-green-50/50 transition-all format-option" id="label-standard">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="import_type" value="standard" checked class="text-green-600 focus:ring-green-500 h-4 w-4" onchange="toggleFormatOption('standard')">
                            <span class="font-bold text-sm text-gray-800">{{ __('Standard Format') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 pl-7">
                            {{ __('Includes Plot Code, Sample ID, DRC %, Net Weight, etc.') }}
                        </p>
                    </label>

                    <!-- Format 2: Farmer Direct Format (No Plot/Sample Codes) -->
                    <label class="relative flex flex-col p-4 bg-gray-50 border-2 border-gray-200 rounded-2xl cursor-pointer hover:bg-green-50/50 transition-all format-option" id="label-farmer_direct">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="import_type" value="farmer_direct" class="text-green-600 focus:ring-green-500 h-4 w-4" onchange="toggleFormatOption('farmer_direct')">
                            <span class="font-bold text-sm text-gray-800">{{ __('Farmer Direct Format') }}</span>
                            <span class="text-[10px] font-extrabold bg-green-100 text-green-700 px-2 py-0.5 rounded-full uppercase">{{ __('New') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 pl-7">
                            {{ __('Columns: Date, Farmer, Net Weight, DRC, Dry Rubber Weight, Amount, Wage, Labor.') }}
                        </p>
                    </label>
                </div>
            </div>

            <!-- Optional Plot Selection (Visible when Farmer Direct is chosen) -->
            <div id="plot-fallback-container" class="hidden p-4 bg-amber-50/80 border border-amber-200 rounded-2xl transition-all">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
                    <div class="w-full">
                        <label for="default_plot_id" class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">
                            {{ __('Default Fallback Plot') }}
                        </label>
                        <p class="text-xs text-amber-700 mb-3">
                            {{ __('Because this format lacks Plot Codes, records will be auto-matched by Farmer Name. If a farmer has multiple plots, selected fallback plot will be used.') }}
                        </p>
                        <select name="default_plot_id" id="default_plot_id" class="w-full bg-white border border-amber-300 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- {{ __('Auto-assign by Farmer Primary Plot') }} --</option>
                            @foreach($plots ?? [] as $plot)
                                <option value="{{ $plot->id }}">{{ $plot->plot_code }} - {{ $plot->name ?? 'Plot #'.$plot->id }} ({{ $plot->farmer->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Drop Zone File Input -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-green-500 transition-colors bg-gray-50/50 hover:bg-green-50/30">
                <svg class="mx-auto h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                
                <div class="mt-4">
                    <label for="excel_file" class="inline-block px-5 py-2.5 bg-green-700 text-white font-bold text-xs rounded-xl cursor-pointer hover:bg-green-800 shadow-md hover:shadow-lg transition-all">
                        {{ __('Choose File') }}
                    </label>
                    <input type="file" id="excel_file" name="excel_file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="displayFileName(this)">
                </div>

                <p id="file-name" class="mt-3 text-xs font-bold text-gray-500">{{ __('No file chosen') }}</p>
                <p class="mt-1 text-[11px] text-gray-400">{{ __('Supported Formats: .xlsx, .xls, .csv') }}</p>
            </div>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" id="submit-btn" class="inline-flex items-center justify-center px-6 py-2.5 bg-emerald-700 text-white font-bold text-xs rounded-xl hover:bg-emerald-800 shadow-md hover:shadow-lg transition">
                    <span id="btn-text">{{ __('Start Ingestion') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFormatOption(type) {
        const standardLabel = document.getElementById('label-standard');
        const farmerDirectLabel = document.getElementById('label-farmer_direct');
        const plotFallback = document.getElementById('plot-fallback-container');

        if (type === 'farmer_direct') {
            farmerDirectLabel.classList.remove('border-gray-200');
            farmerDirectLabel.classList.add('border-green-600', 'bg-green-50/30');
            
            standardLabel.classList.remove('border-green-600', 'bg-green-50/30');
            standardLabel.classList.add('border-gray-200');

            plotFallback.classList.remove('hidden');
        } else {
            standardLabel.classList.remove('border-gray-200');
            standardLabel.classList.add('border-green-600', 'bg-green-50/30');

            farmerDirectLabel.classList.remove('border-green-600', 'bg-green-50/30');
            farmerDirectLabel.classList.add('border-gray-200');

            plotFallback.classList.add('hidden');
        }
    }

    function displayFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : '{{ __("No file chosen") }}';
        const fileNameElement = document.getElementById('file-name');
        fileNameElement.textContent = fileName;
        
        if (input.files[0]) {
            fileNameElement.classList.remove('text-gray-500');
            fileNameElement.classList.add('text-green-700', 'font-black');
        }
    }

    function showLoadingState(event) {
        const fileInput = document.getElementById('excel_file');
        if (!fileInput.files.length) return;

        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    }
</script>
@endsection