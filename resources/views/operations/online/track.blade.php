<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order {{ $order->order_number }} - {{ $restaurant->name }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased">
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="card p-6">
        <h1 class="text-xl font-bold text-slate-800">Thank you, {{ $order->customer_name }}!</h1>
        <p class="text-slate-500">Order <b>#{{ $order->order_number }}</b> at {{ $restaurant->name }} ({{ $order->order_type }})</p>
        <div class="my-4 flex flex-wrap gap-2">
            <x-ui.badge :type="$order->status === 'cancelled' ? 'danger' : ($order->status === 'pending' ? 'warning' : 'success')" class="uppercase !px-3 !py-1.5">
                {{ $order->status === 'pending' ? 'Waiting for confirmation' : $order->status }}</x-ui.badge>
            @if ($order->order_type === 'delivery' && $order->delivery_status)
                <x-ui.badge type="info" class="uppercase !px-3 !py-1.5">Delivery: {{ $order->delivery_status }}</x-ui.badge>
            @endif
        </div>
        @if ($order->status === 'cancelled' && $order->rejection_reason)<p class="text-sm text-red-600">Reason: {{ $order->rejection_reason }}</p>@endif
        <ul class="text-sm text-slate-600 space-y-1 mb-3">
            @foreach ($order->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach
        </ul>
        <div class="font-bold border-t border-slate-100 pt-3">Total: Rs {{ number_format($order->total_amount, 2) }}</div>
        <p class="text-xs text-slate-400 mt-3">Refresh this page to see updates.</p>
    </div>
</div>
</body></html>
