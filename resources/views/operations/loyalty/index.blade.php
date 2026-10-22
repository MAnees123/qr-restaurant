@extends('layouts.admin')
@section('title', 'Loyalty Points')
@section('header', 'Loyalty Points')
@section('content')
@include('operations._alerts')
<div class="space-y-6">
<form method="POST" action="{{ route('admin.loyalty.settings') }}" class="card p-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
    @csrf
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Points per Rs 1 paid</label>
        <input type="number" step="0.01" min="0" name="loyalty_points_per_currency" value="{{ old('loyalty_points_per_currency', $settings->loyalty_points_per_currency) }}" class="input"></div>
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Value of 1 point (Rs)</label>
        <input type="number" step="0.01" min="0" name="loyalty_point_value" value="{{ old('loyalty_point_value', $settings->loyalty_point_value) }}" class="input"></div>
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Minimum to redeem</label>
        <input type="number" min="1" name="loyalty_min_redeem" value="{{ old('loyalty_min_redeem', $settings->loyalty_min_redeem) }}" class="input"></div>
    <div class="flex items-center gap-3">
        <label class="flex items-center gap-2 font-medium text-sm"><input type="checkbox" name="loyalty_enabled" value="1" @checked($settings->loyalty_enabled) class="rounded border-slate-300 text-indigo-600"> Enabled</label>
        <button class="btn btn-primary">Save</button>
    </div>
</form>
<p class="text-xs text-slate-500">Customers are identified by phone number. Points are earned when an order that has a customer phone is marked paid, and can be redeemed on the online order page or at the cashier. Outstanding points: <b>{{ number_format($outstanding) }}</b>.</p>

<form method="GET" class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search phone or name" class="input flex-1">
    <button class="btn btn-secondary">Search</button>
</form>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide">
            <th class="px-6 py-4">Customer</th>
            <th class="px-6 py-4">Phone</th>
            <th class="px-6 py-4 text-right">Balance</th>
            <th class="px-6 py-4 text-right">Lifetime</th>
            <th class="px-6 py-4"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($accounts as $a)
            <tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold">{{ $a->name ?: '-' }}</td><td class="px-6 py-4 text-slate-500">{{ $a->phone }}</td>
                <td class="px-6 py-4 text-right font-semibold text-emerald-600">{{ number_format($a->balance) }}</td><td class="px-6 py-4 text-right text-slate-500">{{ number_format($a->lifetime_earned) }}</td>
                <td class="px-6 py-4 text-right"><a href="{{ route('admin.loyalty.show', $a) }}" class="btn btn-sm btn-secondary">Ledger</a></td></tr>
        @empty
            <tr><td colspan="5"><x-ui.empty-state title="No loyalty accounts yet"/></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $accounts->links() }}</div>
</div>
</div>
@endsection
