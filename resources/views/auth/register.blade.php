<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-green-100 py-6">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-green-800">Register for LATER-X</h2>

            <!-- Success Alert (Shows after submitting registration) -->
            @if(session('status'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded text-green-700 text-sm">
                    <p class="font-bold">Registration Received!</p>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <!-- Info Banner: Approval Notice -->
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded text-blue-700 text-sm">
                <p class="font-medium">Please note:</p>
                <p>Accounts require administrator approval before you can log in.</p>
            </div>

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700 text-sm">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium">Password</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded transition-colors shadow">
                        Register
                    </button>
                </div>

                <!-- Login Link -->
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-green-600 hover:underline text-sm">
                        Already have an account? Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>