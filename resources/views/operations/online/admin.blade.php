@extends('layouts.admin')
@section('title', 'Online Orders')
@section('header', 'Online Orders')
@section('content')
@include('operations._alerts')
<div class="space-y-6">
<div class="card p-5 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="font-semibold text-slate-800">Public ordering page</div>
        <a class="text-indigo-600 hover:underline text-sm break-all" href="{{ route('operations.online.show', auth()->user()->restaurant_id) }}" target="_blank">{{ route('operations.online.show', auth()->user()->restaurant_id) }}</a>
        <p class="text-xs text-slate-500 mt-1">Customers pay cash at pickup / delivery; no online payment gateway is connected.</p>
    </div>
    <form method="POST" action="{{ route('admin.online-orders.settings') }}" class="flex items-center gap-3">
        @csrf
        <label class="flex items-center gap-2 font-medium text-sm"><input type="checkbox" name="online_ordering_open" value="1" @checked($settings->online_ordering_open) class="rounded border-slate-300 text-indigo-600"> Accepting orders</label>
        <button class="btn btn-primary">Save</button>
    </form>
</div>

<form method="GET">
    <select name="status" class="input !w-auto" onchange="this.form.submit()">
        <option value="">All statuses</option>
        @foreach (['pending','confirmed','preparing','ready','served','cancelled'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
</form>

<div class="space-y-4">
@forelse ($orders as $o)
    @php $oType = match($o->status) { 'cancelled' => 'danger', 'pending' => 'warning', 'served' => 'success', default => 'info' }; @endphp
    <div class="card card-hover p-5">
        <div class="flex flex-wrap justify-between gap-2">
            <div><div class="font-semibold text-slate-800">#{{ $o->order_number }} <x-ui.badge class="uppercase ml-1">{{ $o->order_type }}</x-ui.badge></div>
            <div class="text-sm text-slate-500">{{ $o->customer_name }} &middot; {{ $o->customer_phone }} &middot; {{ $o->created_at->diffForHumans() }}</div>
            @if ($o->order_type === 'delivery')<div class="text-sm text-slate-500">{{ $o->delivery_address }}</div>@endif</div>
            <div class="text-right"><div class="font-semibold mb-1">Rs {{ number_format($o->total_amount, 2) }}</div>
            <x-ui.badge :type="$oType" class="uppercase">{{ $o->status }}</x-ui.badge>
            <x-ui.badge :type="$o->payment_status === 'paid' ? 'success' : 'neutral'" class="uppercase">{{ $o->payment_status }}</x-ui.badge></div>
        </div>
        <ul class="mt-3 text-sm text-slate-600">@foreach ($o->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach</ul>
        @if ($o->notes)<p class="text-sm text-slate-500 mt-2">Note: {{ $o->notes }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-2 items-center">
            @if ($o->status === 'pending')
                <form method="POST" action="{{ route('admin.online-orders.accept', $o) }}">@csrf<button class="btn btn-sm btn-primary">Accept</button></form>
                <form method="POST" action="{{ route('admin.online-orders.reject', $o) }}" class="flex gap-2" data-confirm="Reject this order?">@csrf
                    <input type="text" name="reason" placeholder="Reason (optional)" class="input !w-auto text-sm">
                    <button class="btn btn-sm btn-danger">Reject</button></form>
            @elseif (in_array($o->status, ['confirmed', 'preparing', 'ready']))
                @foreach (['preparing', 'ready'] + ($o->order_type === 'pickup' ? [2 => 'served'] : []) as $next)
                    <form method="POST" action="{{ route('admin.online-orders.status', $o) }}" class="flex items-center">@csrf
                        <input type="hidden" name="status" value="{{ $next }}">
                        @if ($next === 'served' && $o->payment_status !== 'paid')<label class="text-xs mr-2"><input type="checkbox" name="collect_cash" value="1"> cash collected</label>@endif
                        <button class="btn btn-sm btn-secondary capitalize">{{ $next === 'served' ? 'Picked up' : $next }}</button></form>
                @endforeach
            @endif
        </div>
    </div>
@empty
    <div class="card"><x-ui.empty-state title="No online orders yet"/></div>
@endforelse
</div>
<div>{{ $orders->links() }}</div>
</div>
@endsection
