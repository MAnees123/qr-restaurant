@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Order #</th>
                <th class="px-6 py-3 font-medium">Table</th>
                <th class="px-6 py-3 font-medium">Time</th>
                <th class="px-6 py-3 font-medium text-right">Total</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Payment</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($orders as $order)
                <tr x-data="{ localStatus: '{{ $order->status }}' }"
                    @order-updated.window="if ($event.detail.id == {{ $order->id }}) localStatus = $event.detail.status"
                    class="hover:bg-indigo-50/40 cursor-pointer transition"
                    @click="$dispatch('open-order-modal', {{ $order->id }})">
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 font-medium">{{ $order->table ? $order->table->table_number : 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $order->created_at->format('M d, h:i A') }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-800">PKR {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="badge capitalize"
                            :class="{
                                'badge-warning': localStatus === 'pending',
                                'badge-info': localStatus === 'preparing' || localStatus === 'ready',
                                'badge-success': localStatus === 'served',
                                'badge-danger': localStatus === 'cancelled'
                            }"
                            x-text="localStatus">
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <x-ui.badge :type="$order->payment_status === 'paid' ? 'success' : 'danger'" class="capitalize">{{ $order->payment_status }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-300">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </td>
                </tr>
            @endforeach
            @if($orders->count() === 0)
                <tr><td colspan="7"><x-ui.empty-state title="No orders yet" message="New orders will appear here as customers place them." /></td></tr>
            @endif
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
