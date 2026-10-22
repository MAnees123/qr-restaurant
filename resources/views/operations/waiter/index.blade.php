@extends('operations.layout')
@section('title', 'Waiter Panel')
@section('content')
@if ($calls->isNotEmpty())
<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-red-600 mb-3">Table calls</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($calls as $call)
            <div class="card card-hover !border-red-200 p-4 flex items-center justify-between gap-3">
                <div><div class="font-semibold text-slate-800">Table {{ $call->table->table_number ?? '?' }}</div>
                <div class="text-xs text-slate-400">{{ $call->created_at->diffForHumans() }}</div></div>
                <form method="POST" action="{{ route('staff.waiter.calls.answer', $call) }}">@csrf
                    <button class="btn btn-sm btn-danger">Answered</button>
                </form>
            </div>
        @endforeach
    </div>
</section>
@endif

<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-3">Tables</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @forelse ($tables as $t)
            <a href="{{ route('staff.waiter.orders.create', $t) }}" class="card card-hover p-4 block">
                <div class="font-bold text-lg text-slate-800">{{ $t->table_number }}</div>
                <x-ui.badge :type="$t->status === 'free' ? 'success' : 'warning'" class="uppercase">{{ $t->status }}</x-ui.badge>
                <div class="text-xs text-indigo-600 font-semibold mt-2">+ New order</div>
            </a>
        @empty
            <div class="card col-span-full"><x-ui.empty-state title="No tables configured"/></div>
        @endforelse
    </div>
</section>

<section>
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-3">Active orders</h2>
    <div class="grid md:grid-cols-2 gap-4">
        @forelse ($orders as $o)
            <div class="card card-hover p-5">
                <div class="flex justify-between items-start gap-2">
                    <div><div class="font-semibold text-slate-800">#{{ $o->order_number }} &middot; Table {{ $o->table->table_number ?? '-' }}</div>
                    <div class="text-xs text-slate-400">{{ $o->created_at->diffForHumans() }}</div></div>
                    <x-ui.badge type="info" class="uppercase">{{ $o->status }}</x-ui.badge>
                </div>
                <ul class="mt-3 text-sm text-slate-600 space-y-1">
                    @foreach ($o->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach
                </ul>
                <div class="mt-3 font-semibold text-slate-800">Rs {{ number_format($o->total_amount, 2) }}
                    @if ($o->payment_status === 'paid')<x-ui.badge type="success" class="ml-2">PAID</x-ui.badge>@endif</div>
                <div class="mt-4 flex gap-2">
                    @if ($o->payment_status !== 'paid')
                        <a href="{{ route('staff.waiter.orders.edit', $o) }}" class="btn btn-sm btn-secondary">Edit</a>
                    @endif
                    <form method="POST" action="{{ route('staff.waiter.orders.serve', $o) }}">@csrf
                        <button class="btn btn-sm btn-primary">Mark served</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card md:col-span-2"><x-ui.empty-state title="No active orders"/></div>
        @endforelse
    </div>
</section>
@endsection
