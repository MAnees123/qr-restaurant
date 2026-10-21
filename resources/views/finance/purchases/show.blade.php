@extends('layouts.admin')
@section('title', $po->po_number)
@section('header', 'Purchase Order ' . $po->po_number)
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @include('finance.partials.errors')
    <div class="bg-white rounded-[2rem] shadow-sm border p-8">
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <div><p class="text-xs font-black text-slate-400 uppercase tracking-widest">Supplier</p><p class="font-black text-lg">{{ $po->supplier->name ?? '-' }}</p><p class="text-sm text-slate-500">{{ $po->order_date->format('d M Y') }}</p></div>
            <div class="text-right"><p class="text-xs font-black uppercase text-slate-400">Status</p><p class="font-black uppercase">{{ $po->status }}</p><p class="text-xs font-black uppercase {{ $po->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $po->payment_status }}</p></div>
        </div>
        <table class="w-full text-left mb-4">
            <thead><tr class="border-b">@foreach(['Item','Qty','Unit cost','Total'] as $h)<th class="py-2 text-xs font-black text-slate-400 uppercase">{{ $h }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">@foreach($po->items as $i)<tr><td class="py-2 font-bold">{{ $i->description }}</td><td>{{ rtrim(rtrim($i->quantity, '0'), '.') }}</td><td>{{ number_format($i->unit_cost, 2) }}</td><td class="font-bold">{{ number_format($i->line_total, 2) }}</td></tr>@endforeach</tbody>
        </table>
        <div class="text-right space-y-1"><p>Total <b>Rs {{ number_format($po->total_amount, 2) }}</b></p><p>Paid Rs {{ number_format($po->paid_amount, 2) }}</p><p class="text-lg font-black text-red-600">Due Rs {{ number_format($po->due, 2) }}</p></div>
        @if($po->notes)<p class="mt-4 text-sm text-slate-500">{{ $po->notes }}</p>@endif
        @if($po->received_at)<p class="mt-2 text-xs text-slate-400">Received {{ $po->received_at->format('d M Y H:i') }}</p>@endif
    </div>
    <div class="flex flex-wrap gap-3 items-end">
        @if($po->status === 'ordered')
            <form method="POST" action="{{ route('admin.purchases.receive', $po) }}">@csrf<button class="bg-emerald-600 text-white font-bold py-2 px-5 rounded-xl">Mark received</button></form>
        @endif
        @if($po->status !== 'cancelled' && $po->due > 0)
            <form method="POST" action="{{ route('admin.purchases.pay', $po) }}" class="flex gap-2 items-end">@csrf
                <input type="number" step="0.01" name="amount" placeholder="Amount (blank = full due)" class="rounded-xl border-slate-200 text-sm"><button class="bg-slate-800 text-white font-bold py-2 px-5 rounded-xl">Record payment</button></form>
        @endif
        @if($po->status === 'ordered' && $po->paid_amount <= 0)
            <form method="POST" action="{{ route('admin.purchases.cancel', $po) }}" data-confirm="Cancel this purchase order?">@csrf<button class="border border-amber-300 text-amber-700 font-bold py-2 px-5 rounded-xl">Cancel</button></form>
        @endif
        @if($po->status !== 'received')
            <form method="POST" action="{{ route('admin.purchases.destroy', $po) }}" data-confirm="Delete this purchase order?">@csrf @method('DELETE')<button class="text-red-500 font-bold py-2 px-5">Delete</button></form>
        @endif
        <a href="{{ route('admin.purchases.index') }}" class="text-slate-500 font-bold py-2">Back</a>
    </div>
    <p class="text-xs text-slate-400">Received status is tracked here only; it does not change menu/portion stock levels.</p>
</div>
@endsection
