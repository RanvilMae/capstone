@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-green-600 mb-4">Staff Dashboard</h1>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-lg">Total Farmers in System: <span class="font-bold">{{ $farmerCount }}</span></p>
            <a href="{{ route('farmer.index') }}"
                class="mt-4 inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">View Farmers</a>
        </div>
    </div>
@endsection