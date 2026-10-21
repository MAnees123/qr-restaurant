@extends('operations.layout')
@section('title', 'Take payment')
@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border p-6">
        <h1 class="text-xl font-black text-slate-800">Order #{{ $order->order_number }}</h1>
        <div class="text-sm text-slate-500 mb-4">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}{{ $order->table ? ' &middot; Table ' . $order->table->table_number : '' }} &middot; {{ $order->status }}</div>
        <table class="w-full text-sm">
            @foreach ($order->orderItems as $i)
                <tr><td class="py-1">{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</td><td class="py-1 text-right">Rs {{ number_format($i->subtotal, 2) }}</td></tr>
            @endforeach
            <tr class="border-t"><td class="pt-2 text-slate-500">Subtotal</td><td class="pt-2 text-right">Rs {{ number_format($order->subtotal, 2) }}</td></tr>
            @if ($order->discount_amount > 0)<tr><td class="text-slate-500">Discount</td><td class="text-right">- Rs {{ number_format($order->discount_amount, 2) }}</td></tr>@endif
            @if ($order->delivery_fee > 0)<tr><td class="text-slate-500">Delivery fee</td><td class="text-right">Rs {{ number_format($order->delivery_fee, 2) }}</td></tr>@endif
            <tr class="font-black text-lg"><td class="pt-2">Total due</td><td class="pt-2 text-right">Rs {{ number_format($order->total_amount, 2) }}</td></tr>
        </table>
    </div>
    <div class="bg-white rounded-2xl border p-6">
        @if ($order->payment_status === 'paid')
            <p class="font-black text-emerald-600 mb-4">This order is paid.</p>
            <a href="{{ route('staff.cashier.orders.receipt', $order) }}" class="inline-block bg-slate-800 text-white font-bold px-5 py-3 rounded-xl">View receipt</a>
        @else
        <form method="POST" action="{{ route('staff.cashier.orders.pay', $order) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Method</label>
                <select name="method" class="w-full rounded-xl border-slate-200 font-bold">
                    @foreach (['cash','card','jazzcash','easypaisa','safepay','bitcoin'] as $m)<option value="{{ $m }}" @selected(old('method') === $m)>{{ ucfirst($m) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Cash tendered (optional)</label>
                <input type="number" step="0.01" min="0" name="tendered" value="{{ old('tendered') }}" class="w-full rounded-xl border-slate-200"></div>
            <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Transaction ID (optional)</label>
                <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" class="w-full rounded-xl border-slate-200"></div>
            @if ($loyalty)
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 space-y-3">
                <div class="text-xs font-black uppercase tracking-widest text-amber-700">Loyalty (earn {{ rtrim(rtrim(number_format($loyalty->loyalty_points_per_currency, 2), '0'), '.') }} pt per Rs 1; 1 pt = Rs {{ rtrim(rtrim(number_format($loyalty->loyalty_point_value, 2), '0'), '.') }})</div>
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" placeholder="Customer phone" class="w-full rounded-xl border-slate-200">
                <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" placeholder="Customer name (optional)" class="w-full rounded-xl border-slate-200">
                <input type="number" min="0" name="redeem_points" value="{{ old('redeem_points') }}" placeholder="Points to redeem (min {{ $loyalty->loyalty_min_redeem }})" class="w-full rounded-xl border-slate-200">
            </div>
            @endif
            <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Notes</label>
                <input type="text" name="notes" value="{{ old('notes') }}" class="w-full rounded-xl border-slate-200"></div>
            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-[0.2em]">Record payment</button>
        </form>
        @endif
    </div>
</div>
@endsection
