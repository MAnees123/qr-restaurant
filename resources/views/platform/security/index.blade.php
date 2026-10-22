@extends('layouts.admin')
@section('title', 'Security')
@section('header', 'Advanced Security')

@php $inp = 'input'; $card = 'card p-6 sm:p-8'; @endphp
@section('content')
@include('platform._errors')
<div class="max-w-5xl space-y-6">

    @if($recovery)
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-5">
        <p class="font-semibold text-amber-800 mb-2">Recovery codes (save them now, each works once and they are not shown again):</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-sm">@foreach($recovery as $c)<span class="bg-white border border-amber-100 rounded-lg px-3 py-2">{{ $c }}</span>@endforeach</div>
    </div>
    @endif

    <div class="{{ $card }}">
        <h2 class="font-semibold text-slate-800 text-lg mb-2">Two-factor authentication (your account)</h2>
        @if($enabled)
            <p class="text-sm text-emerald-700 font-semibold mb-4"><x-ui.badge type="success">Enabled</x-ui.badge>  {{ $codesLeft }} recovery code(s) left.</p>
            <div class="grid sm:grid-cols-2 gap-4">
                <form action="{{ route('admin.security.2fa.recovery') }}" method="POST" class="space-y-2">@csrf
                    <input type="password" name="password" placeholder="Your password" required class="{{ $inp }}">
                    <button class="btn btn-sm btn-secondary">Regenerate recovery codes</button>
                </form>
                <form action="{{ route('admin.security.2fa.disable') }}" method="POST" class="space-y-2" data-confirm="Disable two-factor authentication?">@csrf @method('DELETE')
                    <input type="password" name="password" placeholder="Your password" required class="{{ $inp }}">
                    <button class="btn btn-sm btn-danger">Disable 2FA</button>
                </form>
            </div>
        @elseif($setup)
            <p class="text-sm text-slate-500 mb-3">Scan the QR code with an authenticator app (Google Authenticator, Authy, 1Password...) or enter the key manually, then type the 6-digit code.</p>
            <div class="flex flex-wrap gap-6 items-start">
                <div class="bg-white p-2 border rounded-xl">{!! $setup['svg'] !!}</div>
                <div class="space-y-3 min-w-0">
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Manual key</p>
                    <code class="block font-mono text-sm break-all select-all">{{ $setup['secret'] }}</code>
                    <code class="block font-mono text-xs break-all text-slate-400 select-all">{{ $setup['uri'] }}</code>
                    <form action="{{ route('admin.security.2fa.confirm') }}" method="POST" class="flex gap-2">@csrf
                        <input type="text" name="code" inputmode="numeric" maxlength="10" required placeholder="123456" class="{{ $inp }}">
                        <button class="btn btn-primary">Confirm</button>
                    </form>
                </div>
            </div>
        @else
            <p class="text-sm text-slate-500 mb-3">Add a second step to your login using a time-based one-time code.</p>
            <form action="{{ route('admin.security.2fa.start') }}" method="POST">@csrf
                <button class="btn btn-primary">Set up 2FA</button></form>
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-semibold text-slate-800 text-lg mb-4">Restaurant security policy</h2>
        <form action="{{ route('admin.security.settings') }}" method="POST" class="space-y-4">@csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">IP allowlist (one per line, empty = any). Your IP: {{ $ip }}</label>
                <textarea name="ip_allowlist" rows="3" class="{{ $inp }} font-mono">{{ old('ip_allowlist', $settings->ip_allowlist) }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Checked at sign-in for this restaurant's staff accounts.</p>
            </div>
            <div class="grid sm:grid-cols-4 gap-4 items-end text-sm font-medium text-slate-600">
                <div><label class="block text-sm font-medium text-slate-700 mb-2">Min length</label>
                    <input type="number" name="password_min_length" min="6" max="64" value="{{ old('password_min_length', $settings->password_min_length) }}" class="{{ $inp }}"></div>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_mixed" value="1" @checked(old('password_require_mixed', $settings->password_require_mixed)) class="rounded text-indigo-600"> Upper + lower</label>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_number" value="1" @checked(old('password_require_number', $settings->password_require_number)) class="rounded text-indigo-600"> Number</label>
                <label class="flex gap-2 items-center"><input type="checkbox" name="password_require_symbol" value="1" @checked(old('password_require_symbol', $settings->password_require_symbol)) class="rounded text-indigo-600"> Symbol</label>
            </div>
            <button class="btn btn-primary">Save policy</button>
        </form>
    </div>

    <div class="{{ $card }}">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="font-semibold text-slate-800 text-lg">Your active sessions</h2>
            <form action="{{ route('admin.security.sessions.others') }}" method="POST" data-confirm="Log out of all other devices?">@csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Log out other devices</button></form>
        </div>
        <div class="divide-y divide-slate-100 text-sm">
        @forelse($sessions as $s)
            <div class="py-3 flex flex-wrap justify-between gap-2">
                <span class="text-slate-700 break-all">{{ \Illuminate\Support\Str::limit($s->agent, 80) }}</span>
                <span class="text-slate-500">{{ $s->ip }} &middot; {{ $s->last->diffForHumans() }} @if($s->current)<x-ui.badge type="success">this device</x-ui.badge>@endif</span>
            </div>
        @empty
            <x-ui.empty-state title="No session records" message="Requires SESSION_DRIVER=database." />
        @endforelse
        </div>
    </div>

    <div class="card overflow-x-auto">
        <h2 class="font-semibold text-slate-800 text-lg px-6 py-4 border-b border-slate-100">Login attempts (latest 50, restaurant users)</h2>
        <table class="w-full text-left text-sm">
            <thead><tr><th class="px-6 py-3">When</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Result</th><th class="px-6 py-3">IP</th><th class="px-6 py-3">Device</th></tr></thead>
            <tbody>
            @forelse($attempts as $a)
                <tr><td class="px-6 py-3 whitespace-nowrap">{{ $a->created_at?->format('Y-m-d H:i') }}</td><td class="px-6 py-3">{{ $a->email }}</td>
                    <td class="px-6 py-3"><x-ui.badge :type="$a->result === 'success' ? 'success' : 'danger'">{{ $a->result }}</x-ui.badge></td>
                    <td class="px-6 py-3">{{ $a->ip_address }}</td><td class="px-6 py-3 text-slate-500">{{ \Illuminate\Support\Str::limit($a->user_agent, 50) }}</td></tr>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="No attempts recorded yet" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
