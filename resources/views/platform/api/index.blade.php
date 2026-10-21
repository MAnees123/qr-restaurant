@extends('layouts.admin')
@section('title', 'API Access')
@section('header', 'API Access')

@section('content')
@include('platform._errors')
<div class="max-w-5xl space-y-6">
    @if(session('new_token'))
    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5">
        <p class="font-black text-amber-800 mb-2">Your new API token (shown only once):</p>
        <code class="block break-all bg-white rounded-xl p-3 text-sm font-mono select-all" id="new-token">{{ session('new_token') }}</code>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="font-black text-slate-800 text-lg">Create token</h2>
            <a href="{{ route('admin.api-tokens.docs') }}" class="text-emerald-600 font-bold text-sm">API documentation &rarr;</a>
        </div>
        <form action="{{ route('admin.api-tokens.store') }}" method="POST" class="grid sm:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="sm:col-span-1">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold" placeholder="e.g. POS integration">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Scope</label>
                <select name="scope" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                    <option value="read">Read only</option>
                    <option value="write">Read &amp; write (update order status)</option>
                </select>
            </div>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl transition">Create token</button>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                @foreach(['Name','Token','Scope','Last used','Status',''] as $h)
                <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>
                @endforeach
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($tokens as $t)
                <tr>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $t->name }}</td>
                    <td class="px-6 py-4 font-mono text-sm text-slate-500">{{ $t->token_prefix }}&hellip;</td>
                    <td class="px-6 py-4 text-sm">{{ $t->abilities }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $t->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-6 py-4">
                        @if($t->revoked_at)<span class="bg-slate-100 text-slate-500 text-[10px] font-black uppercase px-2 py-1 rounded-md">Revoked</span>
                        @else<span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase px-2 py-1 rounded-md">Active</span>@endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @unless($t->revoked_at)
                        <form action="{{ route('admin.api-tokens.destroy', $t) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-black text-sm" data-confirm="Revoke this token? Applications using it will stop working.">Revoke</button>
                        </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">No tokens yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
