@extends('operations.layout')
@section('title', $order ? 'Edit Order' : 'New Order')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-black text-slate-800">{{ $order ? 'Edit order #' . $order->order_number : 'New order' }} &middot; Table {{ $table->table_number ?? '-' }}</h1>
    <a href="{{ route('staff.waiter.index') }}" class="text-sm font-bold text-slate-500">Back</a>
</div>
<form method="POST" action="{{ $order ? route('staff.waiter.orders.update', $order) : route('staff.waiter.orders.store', $table) }}">
    @csrf
    @if ($order) @method('PUT') @endif
    @forelse ($categories as $cat)
        <h2 class="mt-6 mb-2 text-sm font-black uppercase tracking-widest text-slate-500">{{ $cat->name }}</h2>
        <div class="bg-white rounded-2xl border divide-y">
            @foreach ($cat->menuItems as $item)
                <div class="flex items-center justify-between px-4 py-3">
                    <div><div class="font-bold text-slate-800">{{ $item->name }}</div><div class="text-xs text-slate-400">Rs {{ number_format($item->price, 2) }}</div></div>
                    <input type="number" min="0" max="99" name="qty[{{ $item->id }}]" value="{{ old('qty.' . $item->id, $qty[$item->id] ?? 0) }}" class="w-20 rounded-xl border-slate-200 text-center font-bold">
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-slate-400">No available menu items.</p>
    @endforelse
    <div class="mt-6">
        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Notes</label>
        <textarea name="notes" rows="2" maxlength="500" class="w-full rounded-xl border-slate-200">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
    <button class="mt-6 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-[0.2em]">{{ $order ? 'Save changes' : 'Place order' }}</button>
</form>
@endsection
