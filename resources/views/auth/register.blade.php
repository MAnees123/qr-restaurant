@extends('layouts.guest')
@section('title', 'Register - QR Restaurant')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-6 bg-stone-50">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-stone-100">
        <!-- Header Section -->
        <div class="bg-amber-500 p-8 text-center">
            <h1 class="text-2xl font-bold text-white">Create Account</h1>
            <p class="text-amber-100 text-sm mt-1">Get your restaurant started today</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-xl border-stone-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm px-4 py-3">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-xl border-stone-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm px-4 py-3">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl border-stone-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm px-4 py-3">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border-stone-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm px-4 py-3">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-colors flex justify-center items-center">
                        Register Account
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:text-amber-500">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
