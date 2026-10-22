@extends('layouts.admin')

@section('header', 'Account Security')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <!-- Header Section -->
    <div class="rounded-2xl p-6 sm:p-10 text-white relative overflow-hidden shadow-lg" style="background:linear-gradient(135deg,#312e81,#4f46e5)">
        <div class="absolute top-0 right-0 p-8">
            <svg class="w-48 h-48 text-white/5 absolute -right-12 -top-12" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"></path></svg>
        </div>
        
        <div class="relative z-10">
            <h3 class="text-xs font-semibold text-indigo-200 uppercase tracking-widest mb-3">Security & Privacy</h3>
            <h1 class="text-3xl sm:text-4xl font-bold mb-3">Manage Password</h1>
            <p class="text-indigo-100 max-w-md leading-relaxed">Ensure your account is using a long, random password to stay secure.</p>
        </div>
    </div>

    <!-- Password Update Form -->
    <div class="card overflow-hidden">
        <div class="px-6 py-5 sm:px-8 border-b border-slate-100 bg-slate-50/60">
            <h2 class="text-lg font-semibold text-slate-800">Update Password</h2>
            <p class="text-sm text-slate-500 mt-1">Change your secret credentials</p>
        </div>
        
        <div class="p-6 sm:p-8">
            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div class="grid grid-cols-1 gap-6">
                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label for="current_password" class="block text-sm font-medium text-slate-700">Current Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="current_password" name="current_password" type="password" class="input !pl-12" autocomplete="current-password">
                        </div>
                        @if($errors->updatePassword->has('current_password'))
                            <p class="text-xs text-red-600 font-medium mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </div>
                            <input id="password" name="password" type="password" class="input !pl-12" autocomplete="new-password">
                        </div>
                        @if($errors->updatePassword->has('password'))
                            <p class="text-xs text-red-600 font-medium mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="input !pl-12" autocomplete="new-password">
                        </div>
                        @if($errors->updatePassword->has('password_confirmation'))
                            <p class="text-xs text-red-600 font-medium mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn btn-primary">
                        Update password
                    </button>

                    @if (session('status') === 'password-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition.opacity.duration.300ms
                            x-init="setTimeout(() => show = false, 3000)"
                            class="text-sm font-semibold text-emerald-600"
                        >Saved Successfully.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Info (Optional/Secondary) -->
    <div class="card card-hover p-6 sm:p-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-800">Need help?</h3>
            <p class="text-sm text-slate-500">Contact support if you've forgotten your current credentials.</p>
        </div>
        <a href="mailto:support@antigravity.ai" class="btn btn-secondary">Contact Support</a>
    </div>
</div>
@endsection
