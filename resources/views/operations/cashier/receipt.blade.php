<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Receipt #{{ $order->order_number }}</title>
<style>
 body{font-family:monospace;max-width:320px;margin:16px auto;color:#000;font-size:13px}
 h1,h2,p{margin:0;text-align:center} table{width:100%;border-collapse:collapse} td{padding:2px 0;vertical-align:top}
 .r{text-align:right} hr{border:0;border-top:1px dashed #000;margin:8px 0} .b{font-weight:bold}
 .actions{text-align:center;margin-top:16px} @media print{.actions{display:none}}
</style></head>
<body>
<h1>{{ $order->restaurant->name ?? 'Restaurant' }}</h1>
@if (!empty($order->restaurant->address))<p>{{ $order->restaurant->address }}</p>@endif
<hr>
<p>Order #{{ $order->order_number }}</p>
<p>{{ $order->created_at->format('Y-m-d H:i') }} &middot; {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}{{ $order->table ? ' &middot; Table ' . $order->table->table_number : '' }}</p>
<hr>
<table>
@foreach ($order->orderItems as $i)
 <tr><td>{{ $i->quantity }} x {{ $i->menuItem->name ?? 'Item' }}</td><td class="r">{{ number_format($i->subtotal, 2) }}</td></tr>
@endforeach
</table>
<hr>
<table>
 <tr><td>Subtotal</td><td class="r">{{ number_format($order->subtotal, 2) }}</td></tr>
 @if ($order->discount_amount > 0)<tr><td>Discount</td><td class="r">-{{ number_format($order->discount_amount, 2) }}</td></tr>@endif
 @if ($order->delivery_fee > 0)<tr><td>Delivery fee</td><td class="r">{{ number_format($order->delivery_fee, 2) }}</td></tr>@endif
 <tr class="b"><td>TOTAL</td><td class="r">{{ number_format($order->total_amount, 2) }}</td></tr>
</table>
<hr>
@foreach ($order->payments as $p)
 <p>Paid {{ number_format($p->amount, 2) }} by {{ $p->method }}</p>
@endforeach
@if ($order->payment_status !== 'paid')<p class="b">UNPAID</p>@endif
<p>Thank you!</p>
<div class="actions"><button onclick="window.print()">Print</button> <a href="{{ $back }}">Back</a></div>
</body></html>
