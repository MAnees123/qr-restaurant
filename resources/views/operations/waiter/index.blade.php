@extends('operations.layout')
@section('title', 'Waiter Panel')
@section('content')
@if ($calls->isNotEmpty())
<section class="mb-8">
    <h2 class="text-sm font-black uppercase tracking-widest text-red-600 mb-3">Table calls</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($calls as $call)
            <div class="bg-white rounded-2xl border border-red-200 p-4 flex items-center justify-between">
                <div><div class="font-black text-slate-800">Table {{ $call->table->table_number ?? '?' }}</div>
                <div class="text-xs text-slate-400">{{ $call->created_at->diffForHumans() }}</div></div>
                <form method="POST" action="{{ route('staff.waiter.calls.answer', $call) }}">@csrf
                    <button class="bg-red-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Answered</button>
                </form>
            </div>
        @endforeach
    </div>
</section>
@endif

<section class="mb-8">
    <h2 class="text-sm font-black uppercase tracking-widest text-slate-500 mb-3">Tables</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @forelse ($tables as $t)
            <a href="{{ route('staff.waiter.orders.create', $t) }}" class="bg-white rounded-2xl border p-4 hover:border-emerald-500 transition block">
                <div class="font-black text-lg text-slate-800">{{ $t->table_number }}</div>
                <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md {{ $t->status === 'free' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $t->status }}</span>
                <div class="text-xs text-emerald-600 font-bold mt-2">+ New order</div>
            </a>
        @empty
            <p class="text-slate-400 col-span-full">No tables configured.</p>
        @endforelse
    </div>
</section>

<section>
    <h2 class="text-sm font-black uppercase tracking-widest text-slate-500 mb-3">Active orders</h2>
    <div class="grid md:grid-cols-2 gap-4">
        @forelse ($orders as $o)
            <div class="bg-white rounded-2xl border p-5">
                <div class="flex justify-between items-start">
                    <div><div class="font-black text-slate-800">#{{ $o->order_number }} &middot; Table {{ $o->table->table_number ?? '-' }}</div>
                    <div class="text-xs text-slate-400">{{ $o->created_at->diffForHumans() }}</div></div>
                    <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md bg-slate-100 text-slate-600">{{ $o->status }}</span>
                </div>
                <ul class="mt-3 text-sm text-slate-600 space-y-1">
                    @foreach ($o->orderItems as $i)<li>{{ $i->quantity }} &times; {{ $i->menuItem->name ?? 'Item' }}</li>@endforeach
                </ul>
                <div class="mt-3 font-black text-slate-800">Rs {{ number_format($o->total_amount, 2) }}
                    @if ($o->payment_status === 'paid')<span class="text-emerald-600 text-xs ml-2">PAID</span>@endif</div>
                <div class="mt-4 flex gap-2">
                    @if ($o->payment_status !== 'paid')
                        <a href="{{ route('staff.waiter.orders.edit', $o) }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold">Edit</a>
                    @endif
                    <form method="POST" action="{{ route('staff.waiter.orders.serve', $o) }}">@csrf
                        <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">Mark served</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-400">No active orders.</p>
        @endforelse
    </div>
</section>
@endsection
