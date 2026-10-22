@extends('layouts.guest')
@section('title', 'Forgot Password - QR Restaurant')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-6 bg-slate-50">
    <div class="card w-full max-w-md overflow-hidden shadow-xl">
        <!-- Header Section -->
        <div class="p-8 text-center" style="background:linear-gradient(135deg,#4f46e5,#6366f1)">
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-indigo-100 text-sm mt-1">We'll send you a link to reset it</p>
        </div>

        <div class="p-8">
            <div class="mb-6 text-sm text-slate-600">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input !py-3">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex flex-col gap-4">
                    <button type="submit" class="btn btn-primary w-full !py-3">
                        Email Password Reset Link
                    </button>
                    <a href="{{ route('login') }}" class="text-center text-sm text-slate-500 hover:text-indigo-600">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
