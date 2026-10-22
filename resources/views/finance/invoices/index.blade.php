@extends('layouts.admin')
@section('title', 'Invoices')
@section('header', 'Invoices')
@section('content')
@include('finance.partials.errors')
<div class="space-y-6">
@if($pending->isNotEmpty())
<div class="card p-6">
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Paid orders without an invoice</p>
    <div class="flex flex-wrap gap-2">
        @foreach($pending as $o)
        <form method="POST" action="{{ route('admin.invoices.generate') }}">@csrf<input type="hidden" name="order_id" value="{{ $o->id }}">
            <button class="btn btn-sm btn-secondary">{{ $o->order_number }} - Rs {{ number_format($o->total_amount, 2) }} &rarr; Invoice</button></form>
        @endforeach
    </div>
</div>
@endif
<form method="GET" class="flex flex-wrap gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Invoice or order number" class="input w-auto">
    <input type="date" name="from" value="{{ request('from') }}" class="input w-auto"><input type="date" name="to" value="{{ request('to') }}" class="input w-auto">
    <button class="btn btn-primary">Search</button>
</form>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>@foreach(['Invoice','Order','Date','Subtotal','Tax','Total',''] as $h)<th class="px-6 py-4 {{ in_array($h, ['Subtotal','Tax','Total']) ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($invoices as $i)
            <tr><td class="px-6 py-4 font-semibold">{{ $i->invoice_number }}</td><td class="px-6 py-4 text-sm">{{ $i->order->order_number ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $i->invoice_date->format('d M Y') }}</td><td class="px-6 py-4 text-right">{{ number_format($i->subtotal, 2) }}</td>
                <td class="px-6 py-4 text-sm text-right">{{ number_format($i->tax_amount, 2) }} @if($i->tax_name)<span class="text-slate-400">({{ $i->tax_name }})</span>@endif</td>
                <td class="px-6 py-4 text-right font-semibold whitespace-nowrap">Rs {{ number_format($i->total, 2) }}</td>
                <td class="px-6 py-4 text-right"><a href="{{ route('admin.invoices.show', $i) }}" class="btn btn-sm btn-secondary whitespace-nowrap">View / Print</a></td></tr>
        @empty<tr><td colspan="7"><x-ui.empty-state title="No invoices found" /></td></tr>@endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $invoices->links() }}</div>
</div>
</div>
@endsection
