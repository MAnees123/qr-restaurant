@extends('layouts.admin')
@section('title', 'Journal')
@section('header', 'Journal Entries')
@section('content')
@include('finance.partials.errors')
<div class="mb-4 flex justify-between"><div class="flex gap-3 text-sm font-black items-center"><a href="{{ route('admin.accounts.index') }}" class="text-blue-500">Accounts</a><a href="{{ route('admin.accounting.trial-balance') }}" class="text-blue-500">Trial balance</a><a href="{{ route('admin.accounting.profit-loss') }}" class="text-blue-500">Profit &amp; loss</a></div>
    <a href="{{ route('admin.journals.create') }}" class="bg-emerald-600 text-white font-bold py-2 px-6 rounded-xl">New Entry</a></div>
<div class="space-y-4">
@forelse($entries as $e)
    <div class="bg-white rounded-2xl border shadow-sm p-5">
        <div class="flex justify-between mb-2"><div><b>{{ $e->memo }}</b> <span class="text-sm text-slate-400">{{ $e->entry_date->format('d M Y') }}</span></div>
            <form method="POST" action="{{ route('admin.journals.destroy', $e) }}" data-confirm="Delete this journal entry?">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm">Delete</button></form></div>
        <table class="w-full text-sm"><tbody>@foreach($e->lines as $l)<tr><td class="py-1">{{ $l->account->code ?? '' }} {{ $l->account->name ?? '' }}</td><td class="text-right w-28">{{ $l->debit > 0 ? number_format($l->debit, 2) : '' }}</td><td class="text-right w-28">{{ $l->credit > 0 ? number_format($l->credit, 2) : '' }}</td></tr>@endforeach</tbody></table>
    </div>
@empty<div class="bg-white rounded-2xl border p-10 text-center text-slate-400">No journal entries.</div>@endforelse
</div>
<div class="mt-4">{{ $entries->links() }}</div>
@endsection
