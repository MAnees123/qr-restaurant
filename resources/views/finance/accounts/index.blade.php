@extends('layouts.admin')
@section('title', 'Chart of Accounts')
@section('header', 'Chart of Accounts')
@section('content')
@include('finance.partials.errors')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2"><a href="{{ route('admin.journals.index') }}" class="btn btn-sm btn-secondary">Journal</a><a href="{{ route('admin.accounting.trial-balance') }}" class="btn btn-sm btn-secondary">Trial balance</a><a href="{{ route('admin.accounting.profit-loss') }}" class="btn btn-sm btn-secondary">Profit &amp; loss</a></div>
        @if($accounts->isEmpty())<form method="POST" action="{{ route('admin.accounts.defaults') }}">@csrf<button class="btn btn-primary">Create starter accounts</button></form>@endif
    </div>
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.accounts.store') }}" class="flex flex-wrap gap-3 items-end">@csrf
            <input name="code" required placeholder="Code" maxlength="20" class="input w-28"><input name="name" required placeholder="Account name" class="input flex-1 min-w-[10rem]">
            <select name="type" class="input w-auto">@foreach($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach</select>
            <button class="btn btn-primary">Add account</button>
        </form>
    </div>
    <div class="card overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr>@foreach(['Code','Name','Type',''] as $h)<th class="px-6 py-4">{{ $h }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse($accounts as $a)
                <tr><td class="px-6 py-4 font-semibold">{{ $a->code }}</td><td class="px-6 py-4 font-medium">{{ $a->name }}</td><td class="px-6 py-4 text-sm"><x-ui.badge type="info" class="capitalize">{{ $a->type }}</x-ui.badge></td>
                    <td class="px-6 py-4 text-right"><form action="{{ route('admin.accounts.destroy', $a) }}" method="POST" data-confirm="Delete this account?">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">Delete</button></form></td></tr>
            @empty<tr><td colspan="4"><x-ui.empty-state title="No accounts yet" /></td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
