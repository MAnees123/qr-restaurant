@extends('layouts.admin')
@section('title', 'Online Orders')
@section('header', 'Online Orders')
@section('content')
@include('operations._alerts')
<div class="mb-6 bg-white rounded-2xl border p-5 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="font-black text-slate-800">Public ordering page</div>
        <a class="text-emerald-600 text-sm break-all" href="{{ route('operations.online.show', auth()->user()->restaurant_id) }}" target="_blank">{{ route('operations.online.show', auth()->user()->restaurant_id) }}</a>
        <p class="text-xs text-slate-400 mt-1">Customers pay cash at pickup / delivery; no online payment gateway is connected.</p>
    </div>
    <form method="POST" action="{{ route('admin.online-orders.settings') }}" class="flex items-center gap-3">
        @csrf
        <label class="flex items-center gap-2 font-bold text-sm"><input type="checkbox" name="online_ordering_open" value="1" @checked($settings->online_ordering_open) class="rounded"> Accepting orders</label>
        <button class="bg-slate-800 text-white font-bold px-4 py-2 rounded-xl">Save</button>
    </form>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <select name="status" class="rounded-xl border-slate-200" onchange="this.form.submit()">
        <option value="">All statuses</option>
        @foreach (['pending','confirmed','preparing','ready','served','cancelled'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
</form>

<div class="space-y-4">
@forelse ($orders as $o)
    <div class="bg-white rounded-2xl border p-5">
        <div class="flex flex-wrap justify-between gap-2">
            <div><div class="font-black text-slate-800">#{{ $o->order_number }} <span class="text-xs uppercase bg-slate-100 text-slate-600 px-2 py-1 rounded-md">{{ $o->order_type }}</span></div>
            <div class="text-sm text-slate-500">{{ $o->customer_name }} &middot; {{ $o->customer_phone }} &middot; {{ $o->created_at->diffForHumans() }}</div>
            @if ($o->order_type === 'delivery')<div class="text-sm text-slate-500">{{ $o->delivery_address }}</div>@endif</div>
            <div class="text-right"><div class="font-black">Rs {{ number_format($o->total_amount, 2) }}</div>
            <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md {{ $o->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $o->status }}</span>
            <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md bg-slate-100 text-slate-600">{{ $o->payment_status }}</span></div>
        </div>
        <ul class="mt-3 text-sm text-slate-600">@foreach ($o->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach</ul>
        @if ($o->notes)<p class="text-sm text-slate-500 mt-2">Note: {{ $o->notes }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-2 items-center">
            @if ($o->status === 'pending')
                <form method="POST" action="{{ route('admin.online-orders.accept', $o) }}">@csrf<button class="bg-emerald-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Accept</button></form>
                <form method="POST" action="{{ route('admin.online-orders.reject', $o) }}" class="flex gap-2" data-confirm="Reject this order?">@csrf
                    <input type="text" name="reason" placeholder="Reason (optional)" class="rounded-xl border-slate-200 text-sm">
                    <button class="bg-red-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Reject</button></form>
            @elseif (in_array($o->status, ['confirmed', 'preparing', 'ready']))
                @foreach (['preparing', 'ready'] + ($o->order_type === 'pickup' ? [2 => 'served'] : []) as $next)
                    <form method="POST" action="{{ route('admin.online-orders.status', $o) }}">@csrf
                        <input type="hidden" name="status" value="{{ $next }}">
                        @if ($next === 'served' && $o->payment_status !== 'paid')<label class="text-xs mr-1"><input type="checkbox" name="collect_cash" value="1"> cash collected</label>@endif
                        <button class="bg-slate-100 text-slate-700 text-sm font-bold px-4 py-2 rounded-xl capitalize">{{ $next === 'served' ? 'Picked up' : $next }}</button></form>
                @endforeach
            @endif
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl border p-10 text-center text-slate-400">No online orders yet.</div>
@endforelse
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
