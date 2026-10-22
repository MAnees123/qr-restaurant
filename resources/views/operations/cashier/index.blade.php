@extends('operations.layout')
@section('title', 'Cashier')
@section('content')
<div class="space-y-6">
<div class="grid sm:grid-cols-3 gap-4">
    <x-ui.stat-card label="Collected today" :value="'Rs ' . number_format($summary['total'], 2)" tone="success"/>
    <x-ui.stat-card label="Payments today" :value="$summary['count']"/>
    <x-ui.stat-card label="Open bills" :value="$summary['unpaid_count'] . ' (Rs ' . number_format($summary['unpaid_total'], 2) . ')'" tone="warning"/>
</div>
<form method="GET" class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order number" class="input flex-1">
    <button class="btn btn-secondary">Search</button>
</form>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide">
            <th class="px-6 py-4">Order</th>
            <th class="px-6 py-4">Type / Table</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4 text-right">Total</th>
            <th class="px-6 py-4"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($orders as $o)
            @php $oType = match($o->status) { 'served', 'completed' => 'success', 'cancelled' => 'danger', 'pending' => 'warning', default => 'info' }; @endphp
            <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-6 py-4 font-semibold">#{{ $o->order_number }}<div class="text-xs text-slate-400 font-normal">{{ $o->created_at->format('H:i') }}</div></td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $o->order_type)) }}{{ $o->table ? ' / T' . $o->table->table_number : '' }}</td>
                <td class="px-6 py-4"><x-ui.badge :type="$oType" class="uppercase">{{ $o->status }}</x-ui.badge></td>
                <td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($o->total_amount, 2) }}</td>
                <td class="px-6 py-4 text-right"><a href="{{ route('staff.cashier.orders.show', $o) }}" class="btn btn-sm btn-primary">Take payment</a></td>
            </tr>
        @empty
            <tr><td colspan="5"><x-ui.empty-state title="No unpaid orders" message="All bills are settled."/></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $orders->links() }}</div>
</div>
</div>
@endsection
