@extends('layouts.admin')

@section('title', ucfirst($channel) . ' Notifications')
@section('header', ($channel === 'sms' ? 'SMS' : ucfirst($channel)) . ' Notifications')

@section('content')
@include('admin.comms._flash')
@php $isEmail = $channel === 'email'; @endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 card p-6 md:p-8">
    <form action="{{ route("admin.comms.$channel.update") }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <label class="flex items-center gap-3">
            <input type="checkbox" name="enabled" value="1" class="rounded text-indigo-600" {{ old('enabled', $settings['enabled']) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-slate-700">Send {{ $channel }} notifications</span>
        </label>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Driver</label>
            <select name="driver" class="input">
                <option value="log" @selected(old('driver', $settings['driver']) === 'log')>Log only (nothing is delivered)</option>
                @if($isEmail)
                    <option value="mail" @selected(old('driver', $settings['driver']) === 'mail')>Email via Laravel Mail (.env mailer)</option>
                @else
                    <option value="http" @selected(old('driver', $settings['driver']) === 'http')>HTTP provider (your gateway URL + token)</option>
                @endif
            </select>
            <p class="text-xs text-slate-400 mt-2">
                @if($isEmail)
                    The "mail" driver uses the mailer configured in the server's <code>.env</code> (MAIL_MAILER, MAIL_HOST, ...). Without real SMTP credentials messages will fail and show as "failed" in the log.
                @else
                    The "http" driver POSTs JSON <code>{to, from, message, channel}</code> to your provider URL with an optional Bearer token. You need an account and credentials with an SMS/WhatsApp gateway; nothing is sent until you provide them.
                @endif
            </p>
        </div>

        @if($isEmail)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">From name</label>
                    <input type="text" name="from_name" value="{{ old('from_name', $settings['from_name']) }}" class="input" placeholder="Restaurant name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject', $settings['subject']) }}" class="input">
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Provider URL</label>
                    <input type="url" name="provider_url" value="{{ old('provider_url', $settings['provider_url']) }}" class="input" placeholder="https://gateway.example.com/send">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">API token</label>
                    <input type="password" name="token" autocomplete="new-password" class="input" placeholder="{{ $settings['has_token'] ? 'Saved (leave blank to keep)' : 'Bearer token' }}">
                    @if($settings['has_token'])
                        <label class="text-xs text-slate-500 mt-1 flex items-center gap-1"><input type="checkbox" name="clear_token" value="1" class="rounded"> Remove saved token</label>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sender / number</label>
                    <input type="text" name="sender" value="{{ old('sender', $settings['sender']) }}" class="input">
                </div>
            </div>
        @endif

        <div>
            <h3 class="text-base font-semibold text-slate-800 mb-3">Events and message templates</h3>
            <p class="text-xs text-slate-400 mb-3">Variables: {order_number} {status} {total} {restaurant} {table} {customer_name} {date} {time}</p>
            <div class="space-y-4">
            @foreach($events as $key => $label)
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50 transition hover:border-indigo-200">
                    <label class="flex items-center gap-2 mb-2">
                        <input type="checkbox" name="events[]" value="{{ $key }}" class="rounded text-indigo-600" {{ in_array($key, old('events', $settings['events'] ?? [])) ? 'checked' : '' }}>
                        <span class="font-semibold text-slate-700">{{ $label }}</span>
                    </label>
                    <textarea name="templates[{{ $key }}]" rows="2" class="input" placeholder="{{ $defaults[$key] }}">{{ old("templates.$key", $settings['templates'][$key] ?? '') }}</textarea>
                </div>
            @endforeach
            </div>
            <p class="text-xs text-slate-400 mt-3">Customer {{ $isEmail ? 'email' : 'phone' }} is read from the order / reservation (<code>customer_{{ $isEmail ? 'email' : 'phone' }}</code>). The guest checkout does not collect it yet, so orders without it are logged as "skipped".</p>
        </div>

        <button class="btn btn-primary">Save settings</button>
    </form>
</div>

<div class="space-y-6">
    <div class="card p-6 self-start">
        <h3 class="text-base font-semibold text-slate-800 mb-3">Send a test</h3>
        <form action="{{ route("admin.comms.$channel.test") }}" method="POST" class="space-y-3">
            @csrf
            <input type="{{ $isEmail ? 'email' : 'text' }}" name="recipient" required class="input" placeholder="{{ $isEmail ? 'name@example.com' : '+923001234567' }}">
            <button class="btn btn-secondary w-full">Send test</button>
        </form>
    </div>
</div>
</div>

<div class="card overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-slate-100 text-base font-semibold text-slate-800">Notification log</div>
    <div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>
            @foreach(['When','Event','Recipient','Driver','Status','Message / response'] as $h)
                <th class="px-6 py-4">{{ $h }}</th>
            @endforeach
        </tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">{{ $log->created_at }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-slate-800">{{ $log->event }}</td>
                <td class="px-6 py-4 text-sm">{{ $log->recipient ?? '-' }}</td>
                <td class="px-6 py-4 text-sm">{{ $log->driver }}</td>
                <td class="px-6 py-4"><x-ui.badge :type="['sent'=>'success','failed'=>'danger','logged'=>'neutral','skipped'=>'warning'][$log->status] ?? 'neutral'">{{ $log->status }}</x-ui.badge></td>
                <td class="px-6 py-4 text-xs text-slate-500 max-w-md">{{ $log->message }}<br><span class="text-slate-400">{{ $log->response }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6"><x-ui.empty-state title="No notifications yet" /></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection
