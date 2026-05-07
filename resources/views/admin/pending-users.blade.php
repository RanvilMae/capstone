@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-6">Pending Users</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left font-medium text-gray-700 border-b">Name</th>
                    <th class="py-3 px-4 text-left font-medium text-gray-700 border-b">Email</th>
                    <th class="py-3 px-4 text-left font-medium text-gray-700 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b">{{ $user->name }}</td>
                    <td class="py-3 px-4 border-b">{{ $user->email }}</td>
                    <td class="py-3 px-4 border-b space-x-2">
                        <form action="{{ route('admin.approve-user', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">Approve</button>
                        </form>

                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
