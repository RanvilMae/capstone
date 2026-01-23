@extends('layouts.app')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">Add New Farmer</h1>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.farmer.store') }}" method="POST" class="max-w-lg p-6 bg-white rounded shadow-md">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded"
                    required>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Farm Location</label>
                <input type="text" name="farm_location" value="{{ old('farm_location') }}"
                    class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Farm Size (ha)</label>
                <input type="number" step="0.01" name="farm_size" value="{{ old('farm_size') }}"
                    class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Notes</label>
                <textarea name="notes" class="w-full px-3 py-2 border rounded">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                Add Farmer
            </button>
        </form>
    </div>
@endsection