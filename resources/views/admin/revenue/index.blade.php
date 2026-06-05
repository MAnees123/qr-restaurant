@extends('layouts.admin')

@section('header', 'Revenue Details')

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Order ID</th>
                <th class="px-6 py-3 font-medium">Payment Amount</th>
                <th class="px-6 py-3 font-medium">Order Status</th>
                <th class="px-6 py-3 font-medium">Payment Status</th>
                <th class="px-6 py-3 font-medium">Time & Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 font-bold text-emerald-600">PKR {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $order->payment_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $order->payment_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No revenue records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
