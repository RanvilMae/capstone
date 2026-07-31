@extends('layouts.app')

@section('content')
<style>
    /* Clean styling when printing or saving as PDF */
    @media print {
        header, aside, .no-print, button, a {
            display: none !important;
        }
        body {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .max-w-7xl {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .shadow, .shadow-sm {
            box-shadow: none !important;
        }
        .border-collapse {
            width: 100% !important;
        }
    }
</style>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-lg shadow no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sales Report Generator</h1>
            <p class="text-sm text-gray-500">Filter sales reports by daily, monthly, quarterly, semestral, or yearly periods.</p>
        </div>

        <!-- Export Buttons -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Print / Export PDF Button -->
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2.5 bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-lg shadow transition cursor-pointer">
                <i class="fas fa-print mr-2"></i> {{ __('Print / PDF') }}
            </button>

            <!-- Export Excel Button -->
            <a href="{{ route('reports.fresh-rubber.export', request()->all()) }}" 
               class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                <i class="fas fa-file-excel mr-2"></i> {{ __('Download Excel') }}
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-lg shadow no-print" x-data="{ reportType: '{{ $type }}' }">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <!-- Report Frequency Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period Type</label>
                <select name="type" x-model="reportType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="daily">Daily</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semestral">Semestral (Half-Year)</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>

            <!-- Daily Field -->
            <div x-show="reportType === 'daily'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Month Field -->
            <div x-show="reportType === 'monthly'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                <select name="month" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quarter Field -->
            <div x-show="reportType === 'quarterly'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Quarter</label>
                <select name="quarter" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                    <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                    <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                    <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Q4 (Oct - Dec)</option>
                </select>
            </div>

            <!-- Semester Field -->
            <div x-show="reportType === 'semestral'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Semester</label>
                <select name="semester" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester 1 (Jan - Jun)</option>
                    <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester 2 (Jul - Dec)</option>
                </select>
            </div>

            <!-- Year Field -->
            <div x-show="reportType !== 'daily'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Year</label>
                <select name="year" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach(range(date('Y'), date('Y') - 10) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-green-700 text-white font-semibold py-2 px-4 rounded-md hover:bg-green-800 transition">
                    <i class="fas fa-filter mr-1"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>

    @php
        $sumVolume = $transactions->sum('volume_kg');
        $sumDryRubber = $transactions->sum('dry_rubber_weight_kg');
        $sumAmount = $transactions->sum('total_amount');
        $totalLabor = $transactions->count();
        $avgDrc = $sumVolume > 0 ? ($sumDryRubber / $sumVolume) * 100 : 0;
    @endphp

    <!-- Large Prominent Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Net Weight -->
        <div class="bg-white p-5 rounded-lg shadow border-l-4 border-blue-500">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">รวมน้ำหนักสุทธิ</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-gray-900">{{ number_format($sumVolume, 2) }}</span>
                <span class="text-sm font-medium text-gray-500">kg</span>
            </div>
        </div>

        <!-- Total Dry Rubber Weight -->
        <div class="bg-white p-5 rounded-lg shadow border-l-4 border-amber-500">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">รวมน้ำหนักยางแห้ง</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-gray-900">{{ number_format($sumDryRubber, 2) }}</span>
                <span class="text-sm font-medium text-gray-500">kg</span>
            </div>
        </div>

        <!-- Total Sales Amount -->
        <div class="bg-white p-5 rounded-lg shadow border-l-4 border-green-600">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">รวมจำนวนเงินทั้งหมด</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-3xl font-black text-green-700">฿{{ number_format($sumAmount, 2) }}</span>
                <span class="text-sm font-medium text-gray-500">บาท</span>
            </div>
        </div>

        <!-- Total Labor / Transactions -->
        <div class="bg-white p-5 rounded-lg shadow border-l-4 border-purple-500">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">จำนวนแรงงาน / รายการ</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-gray-900">{{ number_format($totalLabor) }}</span>
                <span class="text-sm font-medium text-gray-500">คน</span>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">รายงานการขายน้ำยางสด ({{ $periodLabel }})</h2>
            <span class="text-sm font-medium text-gray-600">ราคาเฉลี่ย: <strong class="text-green-700">{{ number_format($avgPrice, 2) }}</strong> บาท</span>
        </div>

        <table class="w-full text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-center font-bold text-gray-700">
                    <th class="border border-gray-300 p-3">ชื่อ สกุล</th>
                    <th class="border border-gray-300 p-3">น้ำหนักสุทธิ (kg)</th>
                    <th class="border border-gray-300 p-3">DRC (%)</th>
                    <th class="border border-gray-300 p-3">น้ำหนักยางแห้ง (kg)</th>
                    <th class="border border-gray-300 p-3">จำนวนเงิน (บาท)</th>
                    <th class="border border-gray-300 p-3">แรงงาน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2 font-medium">{{ $t->plot->farmer->name ?? $t->farmer_name ?? 'N/A' }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ number_format($t->volume_kg, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ number_format($t->dry_rubber_content, 2) }}%</td>
                        <td class="border border-gray-300 p-2 text-right">{{ number_format($t->dry_rubber_weight_kg, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-semibold text-gray-800">{{ number_format($t->total_amount, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-center">1</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border border-gray-300 p-6 text-center text-gray-500">No data found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
            <!-- Table Footer Totals Row -->
            @if($transactions->count() > 0)
                <tfoot>
                    <tr class="bg-green-50 font-bold text-gray-900 border-t-2 border-green-600 text-base">
                        <td class="border border-gray-300 p-3 text-center">รวมทั้งหมด (Total)</td>
                        <td class="border border-gray-300 p-3 text-right text-blue-700">{{ number_format($sumVolume, 2) }}</td>
                        <td class="border border-gray-300 p-3 text-right">{{ number_format($avgDrc, 2) }}%</td>
                        <td class="border border-gray-300 p-3 text-right text-amber-700">{{ number_format($sumDryRubber, 2) }}</td>
                        <td class="border border-gray-300 p-3 text-right text-green-700 text-lg">{{ number_format($sumAmount, 2) }}</td>
                        <td class="border border-gray-300 p-3 text-center">{{ number_format($totalLabor) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</div>
@endsection