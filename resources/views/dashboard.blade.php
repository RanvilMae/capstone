<x-app-layout>
    <div class="min-h-screen bg-green-100 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Dashboard Header -->
            <h1 class="text-3xl font-bold text-green-800 mb-6">LATER-X Dashboard</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-gray-500 font-semibold">Total Rubber Farms</h2>
                    <p class="text-2xl font-bold text-green-700">{{ $totalFarms }}</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-gray-500 font-semibold">Weather Alerts Today</h2>
                    <p class="text-2xl font-bold text-green-700">{{ $weatherAlerts->count() }}</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-gray-500 font-semibold">Recent Market Trends</h2>
                    <p class="text-2xl font-bold text-green-700">{{ $marketTrends->count() }}</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-gray-500 font-semibold">Recent Interventions</h2>
                    <p class="text-2xl font-bold text-green-700">{{ $interventions->count() }}</p>
                </div>
            </div>

            <!-- Market Trends Chart -->
            <div class="bg-white rounded shadow p-4 mb-8">
                <h2 class="text-xl font-bold text-green-700 mb-4">Market Prices (Latest)</h2>
                <canvas id="marketChart"></canvas>
            </div>

            <!-- Weather Alerts -->
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-xl font-bold text-green-700 mb-4">Today's Weather Alerts</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($weatherAlerts as $alert)
                        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded shadow">
                            <h3 class="text-green-800 font-semibold">{{ $alert->title }}</h3>
                            <p class="text-gray-700 text-sm">{{ $alert->description }}</p>
                            <p class="text-gray-500 text-xs mt-2">{{ \Carbon\Carbon::parse($alert->date)->format('M d, Y') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600 col-span-full">No weather alerts for today.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('marketChart').getContext('2d');
        const marketChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($marketTrends->pluck('product')),
                datasets: [{
                    label: 'Price (₱)',
                    data: @json($marketTrends->pluck('price')),
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderColor: 'rgba(21,128,61,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
