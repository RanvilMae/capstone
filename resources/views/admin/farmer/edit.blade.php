@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Farmer</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ auth()->user()->hasRole('admin') ? route('admin.farmer.update', $farmer) : route('farmer.update', $farmer) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" value="{{ $farmer->name }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="{{ $farmer->email }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Contact Number</label>
            <input type="text" name="contact_number" value="{{ $farmer->contact_number }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Address</label>
            <input type="text" name="address" value="{{ $farmer->address }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update Farmer</button>
    </form>
@endsection
