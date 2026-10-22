@extends('layouts.admin')
@section('title', 'Purchase Orders')
@section('header', 'Purchase Orders')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-center">
    <div class="card px-5 py-3"><span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total payable</span> <span class="ml-2 text-xl font-bold text-red-600">Rs {{ number_format($payable, 2) }}</span></div>
    <div class="flex gap-2">
        <a href="{{ route('admin.purchases.index', ['payment' => 'unpaid']) }}" class="btn btn-secondary">Unpaid only</a>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">New Purchase Order</a>
    </div>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>@foreach(['PO #','Supplier','Date','Total','Paid','Status','Payment',''] as $h)<th class="px-6 py-4 {{ in_array($h, ['Total','Paid']) ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($orders as $o)
            <tr>
                <td class="px-6 py-4 font-semibold text-slate-800">{{ $o->po_number }}</td>
                <td class="px-6 py-4 text-sm font-medium text-slate-600">{{ $o->supplier->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $o->order_date->format('d M Y') }}</td>
                <td class="px-6 py-4 text-right font-semibold whitespace-nowrap">Rs {{ number_format($o->total_amount, 2) }}</td>
                <td class="px-6 py-4 text-right text-sm text-slate-500 whitespace-nowrap">Rs {{ number_format($o->paid_amount, 2) }}</td>
                <td class="px-6 py-4"><x-ui.badge :type="match($o->status) { 'received' => 'success', 'cancelled' => 'danger', default => 'info' }" class="capitalize">{{ $o->status }}</x-ui.badge></td>
                <td class="px-6 py-4"><x-ui.badge :type="$o->payment_status === 'paid' ? 'success' : 'warning'" class="capitalize">{{ $o->payment_status }}</x-ui.badge></td>
                <td class="px-6 py-4 text-right"><a href="{{ route('admin.purchases.show', $o) }}" class="btn btn-sm btn-secondary">View</a></td>
            </tr>
        @empty
            <tr><td colspan="8"><x-ui.empty-state title="No purchase orders" /></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $orders->links() }}</div>
</div>
@endsection
