@extends('layouts.admin')
@section('title', 'Chart of Accounts')
@section('header', 'Chart of Accounts')
@section('content')
@include('finance.partials.errors')
@php $in = 'rounded-xl border-slate-200 text-sm font-bold'; @endphp
<div class="mb-4 flex flex-wrap gap-3 justify-between">
    <div class="flex gap-3 text-sm font-black"><a href="{{ route('admin.journals.index') }}" class="text-blue-500">Journal</a><a href="{{ route('admin.accounting.trial-balance') }}" class="text-blue-500">Trial balance</a><a href="{{ route('admin.accounting.profit-loss') }}" class="text-blue-500">Profit &amp; loss</a></div>
    @if($accounts->isEmpty())<form method="POST" action="{{ route('admin.accounts.defaults') }}">@csrf<button class="bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-sm">Create starter accounts</button></form>@endif
</div>
<div class="bg-white rounded-[2rem] border shadow-sm p-6 mb-6">
    <form method="POST" action="{{ route('admin.accounts.store') }}" class="flex flex-wrap gap-2 items-end">@csrf
        <input name="code" required placeholder="Code" maxlength="20" class="{{ $in }} w-28"><input name="name" required placeholder="Account name" class="{{ $in }} flex-1 min-w-[10rem]">
        <select name="type" class="{{ $in }}">@foreach($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach</select>
        <button class="bg-emerald-600 text-white font-bold px-5 py-2 rounded-xl text-sm">Add account</button>
    </form>
</div>
<div class="bg-white rounded-[2rem] border shadow-sm overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b">@foreach(['Code','Name','Type',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y">
        @forelse($accounts as $a)
            <tr><td class="px-6 py-3 font-black">{{ $a->code }}</td><td class="px-6 py-3 font-bold">{{ $a->name }}</td><td class="px-6 py-3 capitalize text-sm">{{ $a->type }}</td>
                <td class="px-6 py-3"><form action="{{ route('admin.accounts.destroy', $a) }}" method="POST" data-confirm="Delete this account?">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm">Delete</button></form></td></tr>
        @empty<tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">No accounts yet.</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endsection
