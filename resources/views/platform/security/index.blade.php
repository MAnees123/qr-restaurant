@extends('layouts.admin')
@section('title', 'Security')
@section('header', 'Advanced Security')

@php $inp = 'w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold'; $card = 'bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8'; @endphp
@section('content')
@include('platform._errors')
<div class="max-w-5xl space-y-6">

    @if($recovery)
    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5">
        <p class="font-black text-amber-800 mb-2">Recovery codes (save them now, each works once and they are not shown again):</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-sm">@foreach($recovery as $c)<span class="bg-white rounded-lg px-3 py-2">{{ $c }}</span>@endforeach</div>
    </div>
    @endif

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-2">Two-factor authentication (your account)</h2>
        @if($enabled)
            <p class="text-sm text-emerald-700 font-bold mb-4">Enabled. {{ $codesLeft }} recovery code(s) left.</p>
            <div class="grid sm:grid-cols-2 gap-4">
                <form action="{{ route('admin.security.2fa.recovery') }}" method="POST" class="space-y-2">@csrf
                    <input type="password" name="password" placeholder="Your password" required class="{{ $inp }}">
                    <button class="bg-slate-800 text-white font-bold py-2 px-4 rounded-xl text-sm">Regenerate recovery codes</button>
                </form>
                <form action="{{ route('admin.security.2fa.disable') }}" method="POST" class="space-y-2" data-confirm="Disable two-factor authentication?">@csrf @method('DELETE')
                    <input type="password" name="password" placeholder="Your password" required class="{{ $inp }}">
                    <button class="bg-red-600 text-white font-bold py-2 px-4 rounded-xl text-sm">Disable 2FA</button>
                </form>
            </div>
        @elseif($setup)
            <p class="text-sm text-slate-500 mb-3">Scan the QR code with an authenticator app (Google Authenticator, Authy, 1Password...) or enter the key manually, then type the 6-digit code.</p>
            <div class="flex flex-wrap gap-6 items-start">
                <div class="bg-white p-2 border rounded-xl">{!! $setup['svg'] !!}</div>
                <div class="space-y-3 min-w-0">
                    <p class="text-xs text-slate-400 uppercase font-black">Manual key</p>
                    <code class="block font-mono text-sm break-all select-all">{{ $setup['secret'] }}</code>
                    <code class="block font-mono text-xs break-all text-slate-400 select-all">{{ $setup['uri'] }}</code>
                    <form action="{{ route('admin.security.2fa.confirm') }}" method="POST" class="flex gap-2">@csrf
                        <input type="text" name="code" inputmode="numeric" maxlength="10" required placeholder="123456" class="{{ $inp }}">
                        <button class="bg-emerald-600 text-white font-bold py-2 px-4 rounded-xl">Confirm</button>
                    </form>
                </div>
            </div>
        @else
            <p class="text-sm text-slate-500 mb-3">Add a second step to your login using a time-based one-time code.</p>
            <form action="{{ route('admin.security.2fa.start') }}" method="POST">@csrf
                <button class="bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl">Set up 2FA</button></form>
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-4">Restaurant security policy</h2>
        <form action="{{ route('admin.security.settings') }}" method="POST" class="space-y-4">@csrf @method('PUT')
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">IP allowlist (one per line, empty = any). Your IP: {{ $ip }}</label>
                <textarea name="ip_allowlist" rows="3" class="{{ $inp }} font-mono">{{ old('ip_allowlist', $settings->ip_allowlist) }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Checked at sign-in for this restaurant's staff accounts.</p>
            </div>
            <div class="grid sm:grid-cols-4 gap-4 items-end text-sm font-bold text-slate-600">
                <div><label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Min length</label>
                    <input type="number" name="password_min_length" min="6" max="64" value="{{ old('password_min_length', $settings->password_min_length) }}" class="{{ $inp }}"></div>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_mixed" value="1" @checked(old('password_require_mixed', $settings->password_require_mixed)) class="rounded"> Upper + lower</label>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_number" value="1" @checked(old('password_require_number', $settings->password_require_number)) class="rounded"> Number</label>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_symbol" value="1" @checked(old('password_require_symbol', $settings->password_require_symbol)) class="rounded"> Symbol</label>
            </div>
            <button class="bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl">Save policy</button>
        </form>
    </div>

    <div class="{{ $card }}">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="font-black text-slate-800 text-lg">Your active sessions</h2>
            <form action="{{ route('admin.security.sessions.others') }}" method="POST" data-confirm="Log out of all other devices?">@csrf @method('DELETE')
                <button class="text-red-600 font-black text-sm">Log out other devices</button></form>
        </div>
        <div class="divide-y divide-slate-100 text-sm">
        @forelse($sessions as $s)
            <div class="py-3 flex flex-wrap justify-between gap-2">
                <span class="text-slate-700 break-all">{{ \Illuminate\Support\Str::limit($s->agent, 80) }}</span>
                <span class="text-slate-500">{{ $s->ip }} &middot; {{ $s->last->diffForHumans() }} @if($s->current)<b class="text-emerald-600">(this device)</b>@endif</span>
            </div>
        @empty
            <p class="text-slate-400">No session records (requires SESSION_DRIVER=database).</p>
        @endforelse
        </div>
    </div>

    <div class="{{ $card }} overflow-x-auto">
        <h2 class="font-black text-slate-800 text-lg mb-4">Login attempts (latest 50, restaurant users)</h2>
        <table class="w-full text-left text-sm">
            <thead><tr class="text-xs font-black text-slate-400 uppercase"><th class="py-2 pr-4">When</th><th class="pr-4">Email</th><th class="pr-4">Result</th><th class="pr-4">IP</th><th>Device</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($attempts as $a)
                <tr><td class="py-2 pr-4 whitespace-nowrap">{{ $a->created_at?->format('Y-m-d H:i') }}</td><td class="pr-4">{{ $a->email }}</td>
                    <td class="pr-4 font-bold {{ $a->result === 'success' ? 'text-emerald-600' : 'text-red-600' }}">{{ $a->result }}</td>
                    <td class="pr-4">{{ $a->ip_address }}</td><td class="text-slate-500">{{ \Illuminate\Support\Str::limit($a->user_agent, 50) }}</td></tr>
            @empty
                <tr><td colspan="5" class="py-6 text-center text-slate-400">No attempts recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
