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
            <p class="text-sm text-gray-500 mt-2 max-w-sm">{{ __('Please wait while we parse records, calculate DRC metrics, and run AI quality predictions.') }}</p>
        </div>

        <!-- Header & Back Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-green-700">{{ __('Batch Spreadsheet Ingestion') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('Upload multi-year Excel production archives (.xlsx, .xls, .csv) to auto-populate production logs and calculate DRC metrics.') }}</p>
            </div>
            
            <a href="{{ route('latex.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-all shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to Transactions') }}
            </a>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700">
                <p class="font-bold">{{ __('Success') }}</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700">
                <p class="font-bold">{{ __('Import Failed') }}</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700">
                <p class="font-bold mb-1">{{ __('Validation Errors') }}</p>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- File Upload Form -->
        <form id="upload-form" action="{{ route('latex.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoadingState(event)">
            @csrf
            
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 transition-colors bg-gray-50/50 hover:bg-green-50/30">
                <svg class="mx-auto h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                
                <div class="mt-4">
                    <label for="excel_file" class="inline-block px-5 py-2.5 bg-green-600 text-white font-bold text-sm rounded-xl cursor-pointer hover:bg-green-700 shadow-md hover:shadow-lg transition-all duration-200">
                        {{ __('Choose File') }}
                    </label>
                    <input type="file" id="excel_file" name="excel_file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="displayFileName(this)">
                </div>

                <p id="file-name" class="mt-3 text-sm font-semibold text-gray-600">{{ __('No file chosen') }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ __('Supported formats: .xlsx, .xls, .csv') }}</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('latex.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" id="submit-btn" class="inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white font-bold text-sm rounded-xl hover:bg-emerald-700 shadow-md hover:shadow-lg transition">
                    <span id="btn-text">{{ __('Start Ingestion') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function displayFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : '{{ __("No file chosen") }}';
        const fileNameElement = document.getElementById('file-name');
        fileNameElement.textContent = fileName;
        
        if (input.files[0]) {
            fileNameElement.classList.remove('text-gray-600');
            fileNameElement.classList.add('text-green-700');
        }
    }

    function showLoadingState(event) {
        const fileInput = document.getElementById('excel_file');
        if (!fileInput.files.length) return;

        // Display full modal spinner
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        // Disable submit button to prevent double-submissions
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    }
</script>
@endsection