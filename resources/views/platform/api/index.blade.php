@extends('layouts.admin')
@section('title', 'API Access')
@section('header', 'API Access')

@section('content')
@include('platform._errors')
<div class="max-w-5xl space-y-6">
    @if(session('new_token'))
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-5">
        <p class="font-semibold text-amber-800 mb-2">Your new API token (shown only once):</p>
        <code class="block break-all bg-white rounded-xl p-3 text-sm font-mono select-all" id="new-token">{{ session('new_token') }}</code>
    </div>
    @endif

    <div class="card p-6 sm:p-8">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="font-semibold text-slate-800 text-lg">Create token</h2>
            <a href="{{ route('admin.api-tokens.docs') }}" class="btn btn-sm btn-secondary">API documentation &rarr;</a>
        </div>
        <form action="{{ route('admin.api-tokens.store') }}" method="POST" class="grid sm:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="sm:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" class="input" placeholder="e.g. POS integration">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Scope</label>
                <select name="scope" class="input">
                    <option value="read">Read only</option>
                    <option value="write">Read &amp; write (update order status)</option>
                </select>
            </div>
            <button class="btn btn-primary">Create token</button>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr>
                @foreach(['Name','Token','Scope','Last used','Status',''] as $h)
                <th class="px-6 py-4">{{ $h }}</th>
                @endforeach
            </tr></thead>
            <tbody>
            @forelse($tokens as $t)
                <tr>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $t->name }}</td>
                    <td class="px-6 py-4 font-mono text-sm text-slate-500">{{ $t->token_prefix }}&hellip;</td>
                    <td class="px-6 py-4 text-sm">{{ $t->abilities }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $t->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-6 py-4">
                        @if($t->revoked_at)<x-ui.badge>Revoked</x-ui.badge>
                        @else<x-ui.badge type="success">Active</x-ui.badge>@endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @unless($t->revoked_at)
                        <form action="{{ route('admin.api-tokens.destroy', $t) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" data-confirm="Revoke this token? Applications using it will stop working.">Revoke</button>
                        </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-ui.empty-state title="No tokens yet" message="Create a token above to connect an external app." /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
