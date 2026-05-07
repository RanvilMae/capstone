@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')

<div class="space-y-6">

    {{-- Welcome & Weather --}}
    <div class="grid grid-cols-5 gap-6">
        <div class="col-span-4 p-6 bg-white shadow-xl rounded-2xl">
            <h1 class="text-5xl font-bold text-green-800">Welcome, <u>{{ auth()->user()->name }}</u>!</h1>
            <p class="mt-4 text-2xl text-gray-500">Here’s all the latest updates for you.</p>
        </div>
        <div class="p-6 bg-white shadow-xl rounded-2xl text-center">
            <h3 class="text-4xl font-semibold text-green-800">{{ $day }}</h3>
            <h3 class="text-xl text-green-600">{{ $date }}</h3>
            <p class="mt-4 text-2xl font-bold text-green-700">{{ $icon }} {{ $temperature }}°C</p>
            <p class="text-gray-600 capitalize">{{ $condition }}</p>
        </div>
    </div>

    <hr class="border-gray-300">

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-4 gap-6">
        <x-kpi-card label="Total" title="Farmers" :value="$totalFarmers" icon='<i class="fa-solid fa-wheat-awn"></i>' />
        <x-kpi-card label="Total" title="Latex (kg)" :value="number_format($totalWeight, 2)" icon='<i class="fa-solid fa-glass-water-droplet"></i>' />
        <x-kpi-card label="Total" title="Rubber Trees" :value="$totalPlots" icon='<i class="fa-solid fa-tree"></i>' />
        <x-kpi-card label="Total" title="Top Contribution" :value="$topPlot->contribution_percent ?? 0" icon='<i class="fa-solid fa-arrow-trend-up"></i>' />
    </div>

    {{-- DSS Recommendation Table --}}
    <div class="p-6 bg-white shadow-xl rounded-2xl">
        <h3 class="text-xl font-semibold mb-4">Monthly DSS Recommendations</h3>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-2 px-3 text-left">Month</th>
                    <th class="py-2 px-3 text-center">DSS Score</th>
                    <th class="py-2 px-3 text-center">Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyDSS as $dss)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $dss['month'] }}</td>
                        <td class="py-2 px-3 text-center font-bold">{{ $dss['score'] }}</td>
                        <td class="py-2 px-3 text-center">{{ $dss['recommendation'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 p-6 bg-white shadow-xl rounded-2xl">
            <h3 class="font-semibold mb-4">Latex Production Trend</h3>
            <canvas id="productionTrend"></canvas>
        </div>
        <div class="p-6 text-center bg-white shadow-xl rounded-2xl">
            <h3 class="font-semibold mb-2">Latex Quality Index</h3>
            <canvas id="qualityGauge"></canvas>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $qualityIndex }}%</p>
        </div>
    </div>

</div>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Production Trend
    const trendCtx = document.getElementById('productionTrend').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0,0,0,300);
    gradient.addColorStop(0, 'rgba(34,197,94,0.4)');
    gradient.addColorStop(1, 'rgba(34,197,94,0.05)');
    new Chart(trendCtx, {
        type: 'line',
        data: { labels: @json($chartLabels), datasets:[{ data:@json($chartData), fill:true, backgroundColor:gradient, borderColor:'#16a34a', tension:0.4, pointRadius:0 }]},
        options: { plugins:{ legend:{ display:false }}, scales:{ y:{ beginAtZero:true }}}
    });

    // Quality Gauge
    new Chart(document.getElementById('qualityGauge'), {
        type: 'doughnut',
        data: { datasets:[{ data:[{{ $qualityIndex }}, {{ 100-$qualityIndex }}], backgroundColor:['#22c55e','#e5e7eb'], borderWidth:0 }]},
        options:{ rotation:-90, circumference:180, cutout:'80%', plugins:{ legend:{display:false}, tooltip:{enabled:false}} }
    });
</script>
@endsection
