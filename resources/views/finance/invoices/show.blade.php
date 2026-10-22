@extends('layouts.admin')
@section('title', $invoice->invoice_number)
@section('header', 'Invoice ' . $invoice->invoice_number)
@section('content')
<style>@media print { body * { visibility: hidden; } #invoice, #invoice * { visibility: visible; } #invoice { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: 0; } }</style>
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex justify-between items-center print:hidden"><a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">&larr; Back</a>
        <button onclick="window.print()" class="btn btn-primary">Print</button></div>
    <div id="invoice" class="card p-6 sm:p-8">
        <div class="flex flex-wrap justify-between gap-4 border-b border-slate-100 pb-4 mb-4">
            <div><p class="text-2xl font-bold">{{ $restaurant->name }}</p><p class="text-sm text-slate-500">{{ $restaurant->address ?? '' }}</p></div>
            <div class="text-right"><p class="font-bold text-lg">{{ $invoice->invoice_number }}</p><p class="text-sm text-slate-500">{{ $invoice->invoice_date->format('d M Y') }}</p><p class="text-sm text-slate-500">Order {{ $invoice->order->order_number }}</p></div>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-left mb-4">
            <thead><tr>@foreach(['Item','Qty','Price','Amount'] as $h)<th class="px-3 py-3 {{ $h !== 'Item' ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
            <tbody>@foreach($invoice->order->orderItems as $it)<tr><td class="px-3 py-3 font-medium">{{ $it->menuItem->name ?? 'Item' }}</td><td class="px-3 py-3 text-right">{{ $it->quantity }}</td><td class="px-3 py-3 text-right">{{ number_format($it->unit_price, 2) }}</td><td class="px-3 py-3 text-right font-semibold">{{ number_format($it->subtotal, 2) }}</td></tr>@endforeach</tbody>
        </table>
        </div>
        <div class="ml-auto w-full sm:w-72 space-y-1 text-sm">
            @if($invoice->order->discount_amount > 0)<div class="flex justify-between"><span>Discount applied</span><span>- {{ number_format($invoice->order->discount_amount, 2) }}</span></div>@endif
            <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span>{{ $invoice->tax_name ?: 'Tax' }} @if($invoice->tax_name)({{ rtrim(rtrim($invoice->tax_rate, '0'), '.') }}%, {{ $invoice->tax_type }})@endif</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
            <div class="flex justify-between text-lg font-bold border-t border-slate-200 pt-2"><span>Total</span><span>Rs {{ number_format($invoice->total, 2) }}</span></div>
            <div class="text-right"><x-ui.badge type="success">Paid</x-ui.badge></div>
        </div>
    </div>
</div>
@endsection
