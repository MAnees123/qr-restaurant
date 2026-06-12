@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Order #</th>
                <th class="px-6 py-3 font-medium">Table</th>
                <th class="px-6 py-3 font-medium">Time</th>
                <th class="px-6 py-3 font-medium">Total</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Payment</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($orders as $order)
                <tr x-data="{ localStatus: '{{ $order->status }}' }"
                    @order-updated.window="if ($event.detail.id == {{ $order->id }}) localStatus = $event.detail.status"
                    class="hover:bg-slate-50 cursor-pointer transition"
                    @click="$dispatch('open-order-modal', {{ $order->id }})">
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 font-medium">{{ $order->table ? $order->table->table_number : 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $order->created_at->format('M d, h:i A') }}</td>
                    <td class="px-6 py-4 font-bold text-amber-600">PKR {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest inline-block shadow-sm" 
                            :class="{
                                'bg-amber-100 text-amber-700': localStatus === 'pending',
                                'bg-blue-100 text-blue-700': localStatus === 'preparing',
                                'bg-indigo-100 text-indigo-700': localStatus === 'ready',
                                'bg-emerald-100 text-emerald-700': localStatus === 'served',
                                'bg-red-100 text-red-700': localStatus === 'cancelled'
                            }"
                            x-text="localStatus">
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest inline-block shadow-sm 
                            {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $order->payment_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-300">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
