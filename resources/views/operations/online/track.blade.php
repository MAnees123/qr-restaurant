<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order {{ $order->order_number }} - {{ $restaurant->name }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased">
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl border p-6">
        <h1 class="text-xl font-black text-slate-800">Thank you, {{ $order->customer_name }}!</h1>
        <p class="text-slate-500">Order <b>#{{ $order->order_number }}</b> at {{ $restaurant->name }} ({{ $order->order_type }})</p>
        <div class="my-4">
            <span class="text-xs font-black uppercase px-3 py-1.5 rounded-md {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $order->status === 'pending' ? 'Waiting for confirmation' : $order->status }}</span>
            @if ($order->order_type === 'delivery' && $order->delivery_status)
                <span class="text-xs font-black uppercase px-3 py-1.5 rounded-md bg-blue-100 text-blue-700 ml-2">Delivery: {{ $order->delivery_status }}</span>
            @endif
        </div>
        @if ($order->status === 'cancelled' && $order->rejection_reason)<p class="text-sm text-red-600">Reason: {{ $order->rejection_reason }}</p>@endif
        <ul class="text-sm text-slate-600 space-y-1 mb-3">
            @foreach ($order->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach
        </ul>
        <div class="font-black">Total: Rs {{ number_format($order->total_amount, 2) }}</div>
        <p class="text-xs text-slate-400 mt-3">Refresh this page to see updates.</p>
    </div>
</div>
</body></html>
