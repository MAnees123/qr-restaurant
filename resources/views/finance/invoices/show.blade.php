@extends('layouts.admin')
@section('title', $invoice->invoice_number)
@section('header', 'Invoice ' . $invoice->invoice_number)
@section('content')
<style>@media print { body * { visibility: hidden; } #invoice, #invoice * { visibility: visible; } #invoice { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: 0; } }</style>
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex justify-between print:hidden"><a href="{{ route('admin.invoices.index') }}" class="text-slate-500 font-bold">&larr; Back</a>
        <button onclick="window.print()" class="bg-slate-800 text-white font-bold px-5 py-2 rounded-xl">Print</button></div>
    <div id="invoice" class="bg-white rounded-[2rem] shadow-sm border p-8">
        <div class="flex justify-between border-b pb-4 mb-4">
            <div><p class="text-2xl font-black">{{ $restaurant->name }}</p><p class="text-sm text-slate-500">{{ $restaurant->address ?? '' }}</p></div>
            <div class="text-right"><p class="font-black text-lg">{{ $invoice->invoice_number }}</p><p class="text-sm text-slate-500">{{ $invoice->invoice_date->format('d M Y') }}</p><p class="text-sm text-slate-500">Order {{ $invoice->order->order_number }}</p></div>
        </div>
        <table class="w-full text-left mb-4">
            <thead><tr class="border-b">@foreach(['Item','Qty','Price','Amount'] as $h)<th class="py-2 text-xs font-black text-slate-400 uppercase">{{ $h }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">@foreach($invoice->order->orderItems as $it)<tr><td class="py-2 font-bold">{{ $it->menuItem->name ?? 'Item' }}</td><td>{{ $it->quantity }}</td><td>{{ number_format($it->unit_price, 2) }}</td><td>{{ number_format($it->subtotal, 2) }}</td></tr>@endforeach</tbody>
        </table>
        <div class="ml-auto w-64 space-y-1 text-sm">
            @if($invoice->order->discount_amount > 0)<div class="flex justify-between"><span>Discount applied</span><span>- {{ number_format($invoice->order->discount_amount, 2) }}</span></div>@endif
            <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span>{{ $invoice->tax_name ?: 'Tax' }} @if($invoice->tax_name)({{ rtrim(rtrim($invoice->tax_rate, '0'), '.') }}%, {{ $invoice->tax_type }})@endif</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
            <div class="flex justify-between text-lg font-black border-t pt-2"><span>Total</span><span>Rs {{ number_format($invoice->total, 2) }}</span></div>
            <div class="text-right text-xs text-emerald-600 font-black uppercase">Paid</div>
        </div>
    </div>
</div>
@endsection
