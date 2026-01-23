@extends('layouts.app')

@section('title', 'Edit Market Price')
@section('content')
    <div class="container max-w-md p-6 mx-auto bg-white rounded-lg shadow">
        <h2 class="mb-6 text-2xl font-bold">Edit Market Price</h2>

        @if ($errors->any())
            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                <ul class="pl-5 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.market-prices.update', $marketPrice) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block mb-1 font-medium">Date</label>
                <input type="date" name="date" class="w-full p-2 border rounded" value="{{ $marketPrice->date->format('Y-m-d') }}" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Price per kg (Baht)</label>
                <input type="number" step="0.01" name="price_per_kg" class="w-full p-2 border rounded" value="{{ $marketPrice->price_per_kg }}" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Source (optional)</label>
                <input type="text" name="source" class="w-full p-2 border rounded" value="{{ $marketPrice->source }}">
            </div>
            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Update Price</button>
            <a href="{{ route('admin.market-prices.index') }}" class="px-4 py-2 ml-4 text-white bg-gray-500 rounded hover:bg-gray-600">Cancel</a>
        </form>
    </div>
@endsection
