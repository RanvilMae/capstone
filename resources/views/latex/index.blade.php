@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-4 sm:py-8 px-3 sm:px-6">
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-100 overflow-hidden">
        <!-- Header Section -->
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Batch Spreadsheet Ingestion</h2>
        <p class="text-xs sm:text-sm md:text-base text-gray-600 mb-6">
            Upload multi-year Excel production archives (.xlsx, .xls, .csv) to auto-populate production logs and calculate DRC metrics.
        </p>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-4 p-3 sm:p-4 bg-green-50 border-l-4 border-green-500 rounded text-green-700">
                <p class="font-medium text-sm sm:text-base">Success</p>
                <p class="text-xs sm:text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 sm:p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-700">
                <p class="font-medium text-sm sm:text-base">Import Failed</p>
                <p class="text-xs sm:text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- File Upload Form -->
        <form action="{{ route('latex.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mb-8">
            @csrf
            
            <!-- Upload Dropzone -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-8 text-center hover:border-blue-500 transition-colors">
                <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>

                <div class="mt-3 sm:mt-4">
                    <label for="excel_file" class="inline-block w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-blue-700 active:bg-blue-800 transition">
                        Choose Excel File
                    </label>
                    <input type="file" id="excel_file" name="excel_file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="displayFileName(this)">
                </div>

                <p id="file-name" class="mt-3 text-xs sm:text-sm text-gray-500 truncate max-w-xs mx-auto">
                    No file chosen
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 active:bg-green-800 transition">
                    Start Ingestion
                </button>
            </div>
        </form>

        <!-- Strictly Mobile Viewport-Bound Table -->
        @if(isset($logs) && count($logs) > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Recent Ingestion Logs</h3>
            
            <!-- This container locks to screen width and isolated scrolling inside -->
            <div class="w-full max-w-full overflow-x-auto touch-pan-x border border-gray-200 rounded-lg shadow-sm">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 min-w-[80px]">Batch ID</th>
                            <th class="px-4 py-3 min-w-[160px]">File Name</th>
                            <th class="px-4 py-3 min-w-[140px]">Records</th>
                            <th class="px-4 py-3 min-w-[120px]">DRC Metric</th>
                            <th class="px-4 py-3 min-w-[100px]">Status</th>
                            <th class="px-4 py-3 min-w-[140px]">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-600">
                        @foreach($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">#{{ $log->id }}</td>
                            <td class="px-4 py-3">{{ $log->filename }}</td>
                            <td class="px-4 py-3">{{ $log->records_count }}</td>
                            <td class="px-4 py-3">{{ $log->drc_score }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                            <td class="px-4 py-3">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function displayFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
        const nameElement = document.getElementById('file-name');
        
        nameElement.textContent = fileName;
        
        if (input.files[0]) {
            nameElement.classList.remove('text-gray-500');
            nameElement.classList.add('text-blue-600', 'font-medium');
        } else {
            nameElement.classList.remove('text-blue-600', 'font-medium');
            nameElement.classList.add('text-gray-500');
        }
    }
</script>
@endsection