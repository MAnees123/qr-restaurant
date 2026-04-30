@extends('layouts.guest')
@section('title', 'Login - QR Restaurant')

@section('content')
<div class="min-h-screen flex">

    <!-- LEFT SIDE -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gray-100 p-6">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

            <!-- Logo -->
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-indigo-500 rounded-lg mx-auto mb-3"></div>
                <h2 class="text-2xl font-bold text-gray-800">Welcome Back 👋</h2>
                <p class="text-gray-500 text-sm">Login to your account</p>
            </div>

            <!-- Session -->
            @if(session('status'))
                <div class="mb-4 text-green-600 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Enter your email">

                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm text-gray-600">Password</label>
                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Enter your password">

                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember -->
                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>

                    <a href="{{ route('password.request') }}" class="text-indigo-500 hover:underline">
                        Forgot password?
                    </a>
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-5">
                <hr class="flex-1">
                <span class="px-3 text-gray-400 text-sm">OR</span>
                <hr class="flex-1">
            </div>

            <!-- Social -->
            <div class="space-y-3">
                <button class="w-full border py-2 rounded-lg hover:bg-gray-50 flex justify-center items-center gap-2">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5">
                    Continue with Google
                </button>

                <button class="w-full border py-2 rounded-lg hover:bg-gray-50 flex justify-center items-center gap-2">
                    <img src="https://www.svgrepo.com/show/448224/facebook.svg" class="w-5">
                    Continue with Facebook
                </button>
            </div>

            <!-- Register -->
            <p class="text-center text-sm text-gray-500 mt-5">
                Don’t have an account?
                <a href="{{ route('register') }}" class="text-indigo-500 hover:underline">
                    Sign up
                </a>
            </p>

        </div>
    </div>

    <!-- RIGHT SIDE (LIKE IMAGE) -->
    <div class="hidden md:flex w-1/2 bg-indigo-100 items-center justify-center relative">

        <div class="text-center max-w-md">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">Welcome Back!</h2>
            <p class="text-gray-600">
                Login to access your dashboard, manage your data and stay updated.
            </p>

            <!-- Fake Dashboard Card -->
            <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
                <div class="h-3 w-20 bg-indigo-200 rounded mb-4"></div>

                <div class="space-y-3">
                    <div class="h-2 bg-gray-200 rounded"></div>
                    <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                </div>

                <div class="mt-6 flex gap-2">
                    <div class="w-4 h-16 bg-indigo-300 rounded"></div>
                    <div class="w-4 h-20 bg-indigo-400 rounded"></div>
                    <div class="w-4 h-24 bg-indigo-500 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Background Shapes -->
        <div class="absolute top-10 right-10 w-32 h-32 bg-indigo-200 rounded-full opacity-50"></div>
    </div>

</div>
@endsection
