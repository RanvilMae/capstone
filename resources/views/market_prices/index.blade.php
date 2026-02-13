@extends('layouts.app')

@section('title', 'Market Prices')
@section('content')
    <div class="container p-6 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Market Prices</h2>
            <a href="{{ route('admin.market-prices.create') }}"
                class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Add Price</a>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-200 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price
                            (Baht/kg)</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Source
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($prices as $price)
                        <tr>
                            <td class="px-6 py-4">{{ $price->date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">{{ number_format($price->price_per_kg, 2) }}</td>
                            <td class="px-6 py-4">{{ $price->source ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.market-prices.edit', $price) }}"
                                    class="px-2 py-1 text-white bg-blue-500 rounded hover:bg-blue-600">Edit</a>
                                <form action="{{ route('admin.market-prices.destroy', $price) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-white bg-red-500 rounded hover:bg-red-600"
                                        onclick="return confirm('Are you sure you want to delete this price?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $prices->links() }}
        </div>
    </div>
@endsection