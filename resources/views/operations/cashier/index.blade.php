@extends('operations.layout')
@section('title', 'Cashier')
@section('content')
<div class="grid sm:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Collected today</div><div class="text-2xl font-black text-emerald-600">Rs {{ number_format($summary['total'], 2) }}</div></div>
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Payments today</div><div class="text-2xl font-black text-slate-800">{{ $summary['count'] }}</div></div>
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Open bills</div><div class="text-2xl font-black text-amber-600">{{ $summary['unpaid_count'] }} (Rs {{ number_format($summary['unpaid_total'], 2) }})</div></div>
</div>
<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order number" class="flex-1 rounded-xl border-slate-200">
    <button class="px-5 rounded-xl bg-slate-800 text-white font-bold">Search</button>
</form>
<div class="bg-white rounded-2xl border overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b">
            <th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Order</th>
            <th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Type / Table</th>
            <th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Status</th>
            <th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Total</th>
            <th class="px-4 py-3"></th>
        </tr></thead>
        <tbody class="divide-y">
        @forelse ($orders as $o)
            <tr>
                <td class="px-4 py-3 font-black">#{{ $o->order_number }}<div class="text-xs text-slate-400 font-normal">{{ $o->created_at->format('H:i') }}</div></td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $o->order_type)) }}{{ $o->table ? ' / T' . $o->table->table_number : '' }}</td>
                <td class="px-4 py-3"><span class="text-[10px] font-black uppercase px-2 py-1 rounded-md bg-slate-100 text-slate-600">{{ $o->status }}</span></td>
                <td class="px-4 py-3 font-black">Rs {{ number_format($o->total_amount, 2) }}</td>
                <td class="px-4 py-3 text-right"><a href="{{ route('staff.cashier.orders.show', $o) }}" class="bg-emerald-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Take payment</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No unpaid orders.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $orders->links() }}</div>
</div>
@endsection
