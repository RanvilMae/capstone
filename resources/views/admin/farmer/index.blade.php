@extends('layouts.app')

@section('content')
    <div class="container p-6 mx-auto bg-white shadow rounded-xl">
        <h1 class="mb-4 text-2xl font-bold">Farmers List</h1>

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="flex items-center px-4 py-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg shadow" role="alert">
                <svg class="w-5 h-5 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Add Farmer Button -->
        <a href="{{ route('admin.farmer.create') }}"
            class="inline-flex items-center px-4 py-2 mb-4 text-white bg-green-600 rounded hover:bg-green-700">
            <i class="mr-2 fa-solid fa-plus"></i> Add Farmer
        </a>

        <table class="min-w-full bg-white border border-gray-200 rounded">
            <thead class="text-white bg-green-600">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Phone</th>
                    <th class="px-4 py-2">Address</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($farmers as $farmer)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $farmer->name }}</td>
                        <td class="px-4 py-2">{{ $farmer->email }}</td>
                        <td class="px-4 py-2">{{ $farmer->phone }}</td>
                        <td class="px-4 py-2">{{ $farmer->address }}</td>
                        <td class="flex flex-wrap items-center gap-2 px-4 py-2">
                            <!-- Edit -->
                            <a href="{{ route('admin.farmer.edit', $farmer) }}"
                                class="flex items-center gap-1 px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <!-- Delete -->
                            <form action="{{ route('admin.farmer.destroy', $farmer) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center gap-1 px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this farmer?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection