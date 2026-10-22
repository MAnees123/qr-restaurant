@extends('operations.layout')
@section('title', $order ? 'Edit Order' : 'New Order')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">{{ $order ? 'Edit order #' . $order->order_number : 'New order' }} &middot; Table {{ $table->table_number ?? '-' }}</h1></div>
    <a href="{{ route('staff.waiter.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ $order ? route('staff.waiter.orders.update', $order) : route('staff.waiter.orders.store', $table) }}" class="space-y-6">
    @csrf
    @if ($order) @method('PUT') @endif
    @forelse ($categories as $cat)
        <div>
        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ $cat->name }}</h2>
        <div class="card divide-y divide-slate-100">
            @foreach ($cat->menuItems as $item)
                <div class="flex items-center justify-between gap-3 px-6 py-4">
                    <div><div class="font-semibold text-slate-800">{{ $item->name }}</div><div class="text-xs text-slate-400">Rs {{ number_format($item->price, 2) }}</div></div>
                    <input type="number" min="0" max="99" name="qty[{{ $item->id }}]" value="{{ old('qty.' . $item->id, $qty[$item->id] ?? 0) }}" class="input !w-20 text-center font-semibold">
                </div>
            @endforeach
        </div>
        </div>
    @empty
        <div class="card"><x-ui.empty-state title="No available menu items"/></div>
    @endforelse
    <div class="card p-5">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="2" maxlength="500" class="input">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
    <button class="btn btn-primary w-full py-3">{{ $order ? 'Save changes' : 'Place order' }}</button>
</form>
@endsection
