@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="container p-6 mx-auto bg-white shadow rounded-xl">
        <div class="container px-4 py-8 mx-auto">
            <h1 class="mb-6 text-2xl font-bold">Add New User</h1>

            @if ($errors->any())
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                    <ul class="pl-5 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="max-w-md p-6 bg-white rounded shadow-md">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block mb-1 font-medium">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block mb-1 font-medium">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>

                <div class="mb-4">
                    <label for="role" class="block mb-1 font-medium">Role</label>
                    <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                        <option value="">Select Role</option><option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>Director</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="password" class="block mb-1 font-medium">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-1 font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>

                <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Add User</button>
            </form>
        </div>
    </div>
@endsection
