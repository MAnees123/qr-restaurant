@extends('layouts.admin')

@section('header', 'Revenue Details')

@section('content')
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Order ID</th>
                <th class="px-6 py-3 font-medium text-right">Payment Amount</th>
                <th class="px-6 py-3 font-medium">Order Status</th>
                <th class="px-6 py-3 font-medium">Payment Status</th>
                <th class="px-6 py-3 font-medium">Time & Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($orders as $order)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-emerald-600">PKR {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <x-ui.badge type="info">{{ ucfirst($order->status) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4">
                        <x-ui.badge :type="$order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : ($order->payment_status === 'cancelled' ? 'danger' : 'neutral'))">{{ ucfirst($order->payment_status) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"><x-ui.empty-state title="No revenue records found" /></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
