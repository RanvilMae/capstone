@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Batch Spreadsheet Ingestion</h2>
        <p class="text-gray-600 mb-6">Upload multi-year Excel production archives (.xlsx, .xls, .csv) to auto-populate production logs and calculate DRC metrics.</p>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded text-green-700">
                <p class="font-medium">Success</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-700">
                <p class="font-medium">Import Failed</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- File Upload Form -->
        <form action="{{ route('latex.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <label for="excel_file" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white font-medium rounded-lg cursor-pointer hover:bg-blue-700 transition">
                    Choose Excel File
                </label>
                <input type="file" id="excel_file" name="excel_file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="displayFileName(this)">
                <p id="file-name" class="mt-2 text-sm text-gray-500">No file chosen</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    Start Ingestion
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function displayFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
        document.getElementById('file-name').textContent = fileName;
    }
</script>
@endsection