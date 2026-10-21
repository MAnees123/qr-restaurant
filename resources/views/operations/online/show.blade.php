<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Order online - {{ $restaurant->name }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased">
<div class="max-w-3xl mx-auto px-4 py-6" x-data="{ type: '{{ old('order_type', $types[0]) }}', fee: {{ (float) $settings->delivery_fee }},
    prices: {{ Js::from($categories->flatMap->menuItems->pluck('price', 'id')) }}, qty: {{ Js::from((object) old('qty', [])) }},
    get subtotal() { let t = 0; for (const id in this.prices) { t += (parseInt(this.qty[id]) || 0) * parseFloat(this.prices[id]); } return t; },
    get total() { return this.subtotal + (this.type === 'delivery' ? this.fee : 0); } }">
    <h1 class="text-2xl font-black text-slate-800">{{ $restaurant->name }}</h1>
    <p class="text-slate-500 mb-4">Order online for {{ implode(' or ', $types) }}.</p>

    @if (!$settings->online_ordering_open)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 font-bold text-amber-700">Online ordering is currently closed. Please check back later.</div>
    @else
    @include('operations._alerts')
    <form method="POST" action="{{ route('operations.online.place', $restaurant) }}" class="space-y-6">
        @csrf
        @foreach ($categories as $cat)
            <div>
                <h2 class="mb-2 text-sm font-black uppercase tracking-widest text-slate-500">{{ $cat->name }}</h2>
                <div class="bg-white rounded-2xl border divide-y">
                    @foreach ($cat->menuItems as $item)
                        <div class="flex items-center justify-between px-4 py-3 gap-3">
                            <div><div class="font-bold text-slate-800">{{ $item->name }}</div><div class="text-xs text-slate-400">Rs {{ number_format($item->price, 2) }}</div></div>
                            <input type="number" min="0" max="99" name="qty[{{ $item->id }}]" x-model="qty[{{ $item->id }}]" class="w-20 rounded-xl border-slate-200 text-center font-bold" placeholder="0">
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="bg-white rounded-2xl border p-5 space-y-4">
            <div class="flex gap-2">
                @foreach ($types as $t)
                    <label class="flex-1 cursor-pointer"><input type="radio" name="order_type" value="{{ $t }}" x-model="type" class="peer sr-only">
                        <span class="block text-center rounded-xl border-2 border-slate-200 py-3 font-black capitalize peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">{{ $t }}</span></label>
                @endforeach
            </div>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Your name" required class="w-full rounded-xl border-slate-200">
            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Phone number" required class="w-full rounded-xl border-slate-200">
            <div x-show="type === 'delivery'" x-cloak>
                <textarea name="delivery_address" rows="2" placeholder="Delivery address" class="w-full rounded-xl border-slate-200">{{ old('delivery_address') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Delivery fee Rs {{ number_format($settings->delivery_fee, 2) }}@if ($settings->delivery_min_order > 0), minimum order Rs {{ number_format($settings->delivery_min_order, 2) }}@endif</p>
            </div>
            @if ($loyaltyOn)
                <input type="number" min="0" name="redeem_points" value="{{ old('redeem_points') }}" placeholder="Loyalty points to redeem (optional, min {{ $settings->loyalty_min_redeem }}; 1 pt = Rs {{ rtrim(rtrim(number_format($settings->loyalty_point_value, 2), '0'), '.') }})" class="w-full rounded-xl border-slate-200">
            @endif
            <textarea name="notes" rows="2" maxlength="500" placeholder="Notes (optional)" class="w-full rounded-xl border-slate-200">{{ old('notes') }}</textarea>
            <p class="text-xs text-slate-400">Payment is made in cash at pickup / on delivery. Online card payment is not available.</p>
        </div>
        <div class="flex items-center justify-between">
            <div class="font-black text-xl">Total: Rs <span x-text="total.toFixed(2)"></span></div>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-8 py-3 rounded-2xl">Place order</button>
        </div>
    </form>
    @endif
</div>
</body></html>
