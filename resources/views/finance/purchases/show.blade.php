@extends('layouts.admin')
@section('title', $po->po_number)
@section('header', 'Purchase Order ' . $po->po_number)
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @include('finance.partials.errors')
    <div class="card p-6 sm:p-8">
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Supplier</p><p class="font-bold text-lg">{{ $po->supplier->name ?? '-' }}</p><p class="text-sm text-slate-500">{{ $po->order_date->format('d M Y') }}</p></div>
            <div class="text-right space-y-1"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</p><x-ui.badge :type="match($po->status) { 'received' => 'success', 'cancelled' => 'danger', default => 'info' }" class="capitalize">{{ $po->status }}</x-ui.badge><p><x-ui.badge :type="$po->payment_status === 'paid' ? 'success' : 'warning'" class="capitalize">{{ $po->payment_status }}</x-ui.badge></p></div>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-left mb-4">
            <thead><tr>@foreach(['Item','Qty','Unit cost','Total'] as $h)<th class="px-3 py-3 {{ $h !== 'Item' ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
            <tbody>@foreach($po->items as $i)<tr><td class="px-3 py-3 font-medium">{{ $i->description }}</td><td class="px-3 py-3 text-right">{{ rtrim(rtrim($i->quantity, '0'), '.') }}</td><td class="px-3 py-3 text-right">{{ number_format($i->unit_cost, 2) }}</td><td class="px-3 py-3 text-right font-semibold">{{ number_format($i->line_total, 2) }}</td></tr>@endforeach</tbody>
        </table>
        </div>
        <div class="text-right space-y-1 border-t border-slate-100 pt-4"><p>Total <b class="font-semibold">Rs {{ number_format($po->total_amount, 2) }}</b></p><p>Paid Rs {{ number_format($po->paid_amount, 2) }}</p><p class="text-lg font-bold text-red-600">Due Rs {{ number_format($po->due, 2) }}</p></div>
        @if($po->notes)<p class="mt-4 text-sm text-slate-500">{{ $po->notes }}</p>@endif
        @if($po->received_at)<p class="mt-2 text-xs text-slate-400">Received {{ $po->received_at->format('d M Y H:i') }}</p>@endif
    </div>
    <div class="flex flex-wrap gap-3 items-end">
        @if($po->status === 'ordered')
            <form method="POST" action="{{ route('admin.purchases.receive', $po) }}">@csrf<button class="btn btn-primary">Mark received</button></form>
        @endif
        @if($po->status !== 'cancelled' && $po->due > 0)
            <form method="POST" action="{{ route('admin.purchases.pay', $po) }}" class="flex flex-wrap gap-2 items-end">@csrf
                <input type="number" step="0.01" name="amount" placeholder="Amount (blank = full due)" class="input w-auto"><button class="btn btn-secondary">Record payment</button></form>
        @endif
        @if($po->status === 'ordered' && $po->paid_amount <= 0)
            <form method="POST" action="{{ route('admin.purchases.cancel', $po) }}" data-confirm="Cancel this purchase order?">@csrf<button class="btn btn-secondary">Cancel</button></form>
        @endif
        @if($po->status !== 'received')
            <form method="POST" action="{{ route('admin.purchases.destroy', $po) }}" data-confirm="Delete this purchase order?">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form>
        @endif
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">Back</a>
    </div>
    <p class="text-xs text-slate-400">Received status is tracked here only; it does not change menu/portion stock levels.</p>
</div>
@endsection
