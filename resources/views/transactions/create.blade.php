<!-- resources/views/transactions/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container p-6 mx-auto bg-white shadow rounded-xl">
    <h2 class="mb-4 text-2xl font-bold">Add Latex Transaction (Admin)</h2>

    @if(session('success'))
        <div class="p-2 mb-4 bg-green-200 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1">Plot</label>
            <select name="plot_id" class="w-full p-2 border rounded">
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}">
                        {{ $plot->plot_location }} ({{ $plot->plot_size_rai }} Rai) - Farmer: {{ $plot->farmer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Transaction Date</label>
            <input type="date" name="transaction_date" class="w-full p-2 border rounded" required>
        </div>

        <div>
            <label class="block mb-1">Volume (kg)</label>
            <input type="number" step="0.01" name="volume_kg" class="w-full p-2 border rounded" required>
        </div>

        <div>
            <label class="block mb-1">Dry Rubber Content (%)</label>
            <input type="number" step="0.01" name="dry_rubber_content" class="w-full p-2 border rounded" required>
        </div>

        <div>
            <label class="block mb-1">Price per kg (Baht)</label>
            <input type="number" step="0.01" name="price_per_kg" class="w-full p-2 border rounded" required>
        </div>

        <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded">Save Transaction</button>
    </form>
</div>
@endsection
