<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $type === 'kitchen' ? 'Kitchen ticket' : 'Bill' }} {{ $order->order_number }}</title>
@php $w = (int) ($s['paper_width'] ?? 80); @endphp
<style>
    @page { size: {{ $w }}mm auto; margin: 0; }
    body { margin: 0; background: #eee; font-family: 'Courier New', monospace; font-size: {{ $w === 58 ? 11 : 13 }}px; color: #000; }
    .ticket { width: {{ $w }}mm; background: #fff; margin: 12px auto; padding: 3mm; box-sizing: border-box; }
    .c { text-align: center; } .b { font-weight: bold; }
    hr { border: 0; border-top: 1px dashed #000; margin: 6px 0; }
    .row { display: flex; justify-content: space-between; gap: 6px; }
    .big { font-size: 1.4em; }
    .noprint { text-align: center; margin: 10px; font-family: sans-serif; }
    @media print { body { background: #fff; } .ticket { margin: 0; } .noprint { display: none; } }
</style>
</head>
<body>
<div class="noprint"><button onclick="window.print()">Print</button> <span>{{ $w }} mm paper</span></div>
<div class="ticket">
    @if($type === 'bill' && $branding && !empty($s['show_logo']) && !empty($branding['logo']))
        <div class="c"><img src="{{ asset('storage/' . $branding['logo']) }}" alt="" style="max-width:60%;max-height:20mm"></div>
    @endif
    <div class="c b big">{{ $type === 'kitchen' ? 'KITCHEN TICKET' : $restaurant->name }}</div>
    @if($type === 'bill' && $branding && !empty($branding['tagline']))<div class="c">{{ $branding['tagline'] }}</div>@endif
    @if(!empty($s['header']))<div class="c">{{ $s['header'] }}</div>@endif
    @if($test)<div class="c b">*** TEST PRINT ***</div>@endif
    <hr>
    <div class="row"><span>Order</span><span class="b">{{ $order->order_number }}</span></div>
    <div class="row"><span>Table</span><span class="b">{{ $order->table->table_number ?? '-' }}</span></div>
    <div class="row"><span>Time</span><span>{{ optional($order->created_at)->format('d M Y H:i') }}</span></div>
    <hr>
    @foreach($order->orderItems as $it)
        <div class="row">
            <span>{{ $it->quantity }} x {{ $it->menuItem->name ?? 'Item' }}</span>
            @if($type === 'bill')<span>{{ number_format((float) $it->subtotal, 2) }}</span>@endif
        </div>
        @if(!empty($it->special_instructions))<div>&nbsp;&nbsp;* {{ $it->special_instructions }}</div>@endif
    @endforeach
    <hr>
    @if($type === 'bill')
        <div class="row"><span>Subtotal</span><span>{{ number_format((float) $order->subtotal, 2) }}</span></div>
        @if((float) $order->discount_amount > 0)<div class="row"><span>Discount</span><span>-{{ number_format((float) $order->discount_amount, 2) }}</span></div>@endif
        <div class="row b big"><span>TOTAL</span><span>{{ number_format((float) $order->total_amount, 2) }}</span></div>
        <hr>
    @endif
    @if($order->notes)<div><b>Notes:</b> {{ $order->notes }}</div><hr>@endif
    @if(!empty($s['footer']))<div class="c">{{ $s['footer'] }}</div>@endif
</div>
@if(request()->boolean('autoprint'))<script>window.onload = function () { window.print(); };</script>@endif
</body>
</html>
