@extends('layouts.admin')
@section('title', 'Delivery')
@section('header', 'Delivery Orders')
@section('content')
@include('operations._alerts')
<form method="POST" action="{{ route('admin.delivery.settings') }}" class="mb-6 bg-white rounded-2xl border p-5 grid sm:grid-cols-3 gap-4 items-end">
    @csrf
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Delivery fee (Rs)</label>
        <input type="number" step="0.01" min="0" name="delivery_fee" value="{{ old('delivery_fee', $settings->delivery_fee) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Minimum order (Rs)</label>
        <input type="number" step="0.01" min="0" name="delivery_min_order" value="{{ old('delivery_min_order', $settings->delivery_min_order) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
    <button class="bg-slate-800 text-white font-bold px-4 py-2.5 rounded-xl">Save settings</button>
</form>
<p class="text-xs text-slate-400 mb-4">Delivery orders are placed by customers on the public page: <a class="text-emerald-600" target="_blank" href="{{ route('operations.online.show', auth()->user()->restaurant_id) }}">{{ route('operations.online.show', auth()->user()->restaurant_id) }}</a>. Drivers are tracked by name only; there is no GPS tracking.</p>

<form method="GET" class="mb-4">
    <select name="delivery_status" class="rounded-xl border-slate-200" onchange="this.form.submit()">
        <option value="">All deliveries</option>
        @foreach (['pending','assigned','out','delivered'] as $s)<option value="{{ $s }}" @selected(request('delivery_status') === $s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
</form>

<div class="space-y-4">
@forelse ($orders as $o)
    <div class="bg-white rounded-2xl border p-5">
        <div class="flex flex-wrap justify-between gap-2">
            <div><div class="font-black text-slate-800">#{{ $o->order_number }}</div>
                <div class="text-sm text-slate-500">{{ $o->customer_name }} &middot; {{ $o->customer_phone }}</div>
                <div class="text-sm text-slate-600">{{ $o->delivery_address }}</div></div>
            <div class="text-right"><div class="font-black">Rs {{ number_format($o->total_amount, 2) }}</div>
                <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md bg-blue-100 text-blue-700">{{ $o->delivery_status ?? 'pending' }}</span>
                <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md bg-slate-100 text-slate-600">{{ $o->payment_status }}</span></div>
        </div>
        @if ($o->driver_name)<p class="mt-2 text-sm text-slate-600">Driver: <b>{{ $o->driver_name }}</b> {{ $o->driver_phone }}</p>@endif
        @if ($o->delivery_status !== 'delivered')
        <div class="mt-4 flex flex-wrap gap-3 items-center">
            <form method="POST" action="{{ route('admin.delivery.assign', $o) }}" class="flex flex-wrap gap-2">@csrf
                <input type="text" name="driver_name" value="{{ $o->driver_name }}" placeholder="Driver name" required class="rounded-xl border-slate-200 text-sm">
                <input type="text" name="driver_phone" value="{{ $o->driver_phone }}" placeholder="Driver phone" class="rounded-xl border-slate-200 text-sm">
                <button class="bg-slate-800 text-white text-sm font-bold px-4 py-2 rounded-xl">{{ $o->driver_name ? 'Reassign' : 'Assign' }}</button>
            </form>
            @if ($o->driver_name)
                @if ($o->delivery_status !== 'out')
                <form method="POST" action="{{ route('admin.delivery.status', $o) }}">@csrf<input type="hidden" name="delivery_status" value="out">
                    <button class="bg-blue-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Out for delivery</button></form>
                @endif
                <form method="POST" action="{{ route('admin.delivery.status', $o) }}" class="flex items-center gap-2">@csrf<input type="hidden" name="delivery_status" value="delivered">
                    @if ($o->payment_status !== 'paid')<label class="text-xs"><input type="checkbox" name="collect_cash" value="1"> cash collected</label>@endif
                    <button class="bg-emerald-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Delivered</button></form>
            @endif
        </div>
        @endif
    </div>
@empty
    <div class="bg-white rounded-2xl border p-10 text-center text-slate-400">No delivery orders.</div>
@endforelse
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
