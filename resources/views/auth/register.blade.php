<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-green-100">
        <div class="bg-white p-8 rounded shadow w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-green-800">Register for LATER-X</h2>

            @if(session('status'))
                <div class="mb-4 text-green-600 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input id="name" type="text" name="name" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input id="email" type="email" name="email" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-2 rounded transition-colors">
                        Register
                    </button>
                </div>

                <!-- Login Link -->
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-green-600 hover:underline">
                        Already have an account? Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
