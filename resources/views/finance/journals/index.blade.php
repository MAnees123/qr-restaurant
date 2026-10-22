@extends('layouts.admin')
@section('title', 'Journal')
@section('header', 'Journal Entries')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-center"><div class="flex flex-wrap gap-2"><a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-secondary">Accounts</a><a href="{{ route('admin.accounting.trial-balance') }}" class="btn btn-sm btn-secondary">Trial balance</a><a href="{{ route('admin.accounting.profit-loss') }}" class="btn btn-sm btn-secondary">Profit &amp; loss</a></div>
    <a href="{{ route('admin.journals.create') }}" class="btn btn-primary">New Entry</a></div>
<div class="space-y-4">
@forelse($entries as $e)
    <div class="card card-hover p-5">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2"><div><b class="font-semibold">{{ $e->memo }}</b> <span class="text-sm text-slate-400">{{ $e->entry_date->format('d M Y') }}</span></div>
            <form method="POST" action="{{ route('admin.journals.destroy', $e) }}" data-confirm="Delete this journal entry?">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">Delete</button></form></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><tbody>@foreach($e->lines as $l)<tr><td class="px-2 py-2">{{ $l->account->code ?? '' }} {{ $l->account->name ?? '' }}</td><td class="px-2 py-2 text-right font-semibold w-28">{{ $l->debit > 0 ? number_format($l->debit, 2) : '' }}</td><td class="px-2 py-2 text-right font-semibold w-28">{{ $l->credit > 0 ? number_format($l->credit, 2) : '' }}</td></tr>@endforeach</tbody></table></div>
    </div>
@empty<div class="card"><x-ui.empty-state title="No journal entries" /></div>@endforelse
</div>
<div class="mt-4">{{ $entries->links() }}</div>
@endsection
