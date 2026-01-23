@extends('layouts.app')

@section('content')
    <div class="container p-6 mx-auto bg-white shadow rounded-xl">
        <h1 class="mb-4 text-2xl font-bold">User Management</h1>

        @if(session('success'))
            <div class="flex items-center px-4 py-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg shadow" role="alert">
                <svg class="w-5 h-5 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7V7h2v4h-2zm0 4h2v2h-2v-2z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Add User Button -->
        <a href="{{ route('admin.users.create-user') }}"
            class="inline-block px-4 py-2 mb-4 text-white bg-green-600 rounded hover:bg-green-700">
            <i class="fa-solid fa-plus"></i> Add User
        </a>

        <table class="min-w-full bg-white border border-gray-200 rounded">
            <thead class="text-white bg-green-600">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b @if($user->trashed()) bg-gray-100 @endif">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-2">
                            @if($user->trashed())
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-white bg-red-600 rounded">
                                    <i class="fa-solid fa-xmark"></i> Rejected
                                </span>
                            @elseif($user->is_approved)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-white bg-green-700 rounded">
                                    <i class="fa-solid fa-check"></i> Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-white bg-yellow-600 rounded">
                                    <i class="fa-solid fa-hourglass-half"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="flex flex-wrap items-center gap-2 px-4 py-2">
                            @if(!$user->trashed())
                                <!-- Edit User -->
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="flex items-center gap-1 px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <!-- Soft Delete (Reject) User -->
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-1 px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                </form>

                                <!-- Approve if pending -->
                                @if(!$user->is_approved)
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="flex items-center gap-1 px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endif
                            @else
                                <!-- Restore deleted/rejected user -->
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="flex items-center gap-1 px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700">
                                        <i class="fa-solid fa-rotate-left"></i> Restore
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection