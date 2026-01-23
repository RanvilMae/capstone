<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            Farmer Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- My Farms -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-green-700 font-semibold text-lg mb-4">My Farms</h3>
                <ul class="space-y-2">
                    @forelse ($farms as $farm)
                        <li class="border-b py-2">{{ $farm->name ?? 'Unnamed Farm' }} - {{ $farm->location ?? 'No location' }}</li>
                    @empty
                        <li>You have no farms yet.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Weather Alerts -->
            <div class="bg-green-100 p-6 rounded shadow">
                <h3 class="text-green-800 font-semibold text-lg mb-4">Weather Alerts Today</h3>
                <ul class="space-y-2">
                    @forelse ($weatherAlerts as $alert)
                        <li class="py-2">{{ $alert->description ?? 'No description' }} ({{ $alert->date }})</li>
                    @empty
                        <li>No weather alerts today.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Market Trends -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-green-700 font-semibold text-lg mb-4">Recent Market Trends</h3>
                <ul class="space-y-2">
                    @forelse ($marketTrends as $trend)
                        <li class="border-b py-2">{{ $trend->crop_name ?? 'N/A' }} - ₱{{ $trend->price ?? '0' }}</li>
                    @empty
                        <li>No market trends available.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recommendations / Interventions -->
            <div class="bg-green-100 p-6 rounded shadow">
                <h3 class="text-green-800 font-semibold text-lg mb-4">Recommended Interventions</h3>
                <ul class="space-y-2">
                    @forelse ($recommendations as $rec)
                        <li class="py-2">{{ $rec->name ?? 'N/A' }} - {{ $rec->description ?? 'No description' }}</li>
                    @empty
                        <li>No recommendations at this time.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
