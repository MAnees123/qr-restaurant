@extends('layouts.guest')
@section('title', 'Forgot Password - QR Restaurant')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-6 bg-stone-50">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-stone-100">
        <!-- Header Section -->
        <div class="bg-amber-500 p-8 text-center">
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-amber-100 text-sm mt-1">We'll send you a link to reset it</p>
        </div>

        <div class="p-8">
            <div class="mb-6 text-sm text-slate-600">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border-stone-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm px-4 py-3">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex flex-col gap-4">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-colors flex justify-center items-center">
                        Email Password Reset Link
                    </button>
                    <a href="{{ route('login') }}" class="text-center text-sm text-slate-500 hover:text-amber-600">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
