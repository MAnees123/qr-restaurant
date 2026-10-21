@extends('layouts.admin')
@section('title', 'Invoices')
@section('header', 'Invoices')
@section('content')
@include('finance.partials.errors')
@if($pending->isNotEmpty())
<div class="mb-6 bg-white rounded-[2rem] border p-6">
    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Paid orders without an invoice</p>
    <div class="flex flex-wrap gap-2">
        @foreach($pending as $o)
        <form method="POST" action="{{ route('admin.invoices.generate') }}">@csrf<input type="hidden" name="order_id" value="{{ $o->id }}">
            <button class="border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-4 py-2 rounded-xl text-sm">{{ $o->order_number }} - Rs {{ number_format($o->total_amount, 2) }} &rarr; Invoice</button></form>
        @endforeach
    </div>
</div>
@endif
<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <input name="q" value="{{ request('q') }}" placeholder="Invoice or order number" class="rounded-xl border-slate-200 text-sm">
    <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border-slate-200 text-sm"><input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border-slate-200 text-sm">
    <button class="bg-slate-800 text-white font-bold px-4 rounded-xl text-sm">Search</button>
</form>
<div class="bg-white rounded-[2rem] shadow-sm border overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b">@foreach(['Invoice','Order','Date','Subtotal','Tax','Total',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y">
        @forelse($invoices as $i)
            <tr><td class="px-6 py-3 font-black">{{ $i->invoice_number }}</td><td class="px-6 py-3 text-sm">{{ $i->order->order_number ?? '-' }}</td>
                <td class="px-6 py-3 text-sm text-slate-500">{{ $i->invoice_date->format('d M Y') }}</td><td class="px-6 py-3">{{ number_format($i->subtotal, 2) }}</td>
                <td class="px-6 py-3 text-sm">{{ number_format($i->tax_amount, 2) }} @if($i->tax_name)<span class="text-slate-400">({{ $i->tax_name }})</span>@endif</td>
                <td class="px-6 py-3 font-black">Rs {{ number_format($i->total, 2) }}</td>
                <td class="px-6 py-3"><a href="{{ route('admin.invoices.show', $i) }}" class="text-blue-500 font-black text-sm">View / Print</a></td></tr>
        @empty<tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">No invoices found.</td></tr>@endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $invoices->links() }}</div>
</div>
@endsection
