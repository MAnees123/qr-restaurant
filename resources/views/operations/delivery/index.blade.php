@extends('layouts.admin')
@section('title', 'Delivery')
@section('header', 'Delivery Orders')
@section('content')
@include('operations._alerts')
<div class="space-y-6">
<form method="POST" action="{{ route('admin.delivery.settings') }}" class="card p-5 grid sm:grid-cols-3 gap-4 items-end">
    @csrf
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Delivery fee (Rs)</label>
        <input type="number" step="0.01" min="0" name="delivery_fee" value="{{ old('delivery_fee', $settings->delivery_fee) }}" class="input"></div>
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Minimum order (Rs)</label>
        <input type="number" step="0.01" min="0" name="delivery_min_order" value="{{ old('delivery_min_order', $settings->delivery_min_order) }}" class="input"></div>
    <button class="btn btn-primary">Save settings</button>
</form>
<p class="text-xs text-slate-500">Delivery orders are placed by customers on the public page: <a class="text-indigo-600 hover:underline" target="_blank" href="{{ route('operations.online.show', auth()->user()->restaurant_id) }}">{{ route('operations.online.show', auth()->user()->restaurant_id) }}</a>. Drivers are tracked by name only; there is no GPS tracking.</p>

<form method="GET">
    <select name="delivery_status" class="input !w-auto" onchange="this.form.submit()">
        <option value="">All deliveries</option>
        @foreach (['pending','assigned','out','delivered'] as $s)<option value="{{ $s }}" @selected(request('delivery_status') === $s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
</form>

<div class="space-y-4">
@forelse ($orders as $o)
    @php $dType = match($o->delivery_status ?? 'pending') { 'delivered' => 'success', 'out' => 'info', 'assigned' => 'info', default => 'warning' }; @endphp
    <div class="card card-hover p-5">
        <div class="flex flex-wrap justify-between gap-2">
            <div><div class="font-semibold text-slate-800">#{{ $o->order_number }}</div>
                <div class="text-sm text-slate-500">{{ $o->customer_name }} &middot; {{ $o->customer_phone }}</div>
                <div class="text-sm text-slate-600">{{ $o->delivery_address }}</div></div>
            <div class="text-right"><div class="font-semibold mb-1">Rs {{ number_format($o->total_amount, 2) }}</div>
                <x-ui.badge :type="$dType" class="uppercase">{{ $o->delivery_status ?? 'pending' }}</x-ui.badge>
                <x-ui.badge :type="$o->payment_status === 'paid' ? 'success' : 'neutral'" class="uppercase">{{ $o->payment_status }}</x-ui.badge></div>
        </div>
        @if ($o->driver_name)<p class="mt-2 text-sm text-slate-600">Driver: <b>{{ $o->driver_name }}</b> {{ $o->driver_phone }}</p>@endif
        @if ($o->delivery_status !== 'delivered')
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-3 items-center">
            <form method="POST" action="{{ route('admin.delivery.assign', $o) }}" class="flex flex-wrap gap-2">@csrf
                <input type="text" name="driver_name" value="{{ $o->driver_name }}" placeholder="Driver name" required class="input !w-auto text-sm">
                <input type="text" name="driver_phone" value="{{ $o->driver_phone }}" placeholder="Driver phone" class="input !w-auto text-sm">
                <button class="btn btn-sm btn-secondary">{{ $o->driver_name ? 'Reassign' : 'Assign' }}</button>
            </form>
            @if ($o->driver_name)
                @if ($o->delivery_status !== 'out')
                <form method="POST" action="{{ route('admin.delivery.status', $o) }}">@csrf<input type="hidden" name="delivery_status" value="out">
                    <button class="btn btn-sm btn-secondary">Out for delivery</button></form>
                @endif
                <form method="POST" action="{{ route('admin.delivery.status', $o) }}" class="flex items-center gap-2">@csrf<input type="hidden" name="delivery_status" value="delivered">
                    @if ($o->payment_status !== 'paid')<label class="text-xs"><input type="checkbox" name="collect_cash" value="1"> cash collected</label>@endif
                    <button class="btn btn-sm btn-primary">Delivered</button></form>
            @endif
        </div>
        @endif
    </div>
@empty
    <div class="card"><x-ui.empty-state title="No delivery orders"/></div>
@endforelse
</div>
<div>{{ $orders->links() }}</div>
</div>
@endsection
