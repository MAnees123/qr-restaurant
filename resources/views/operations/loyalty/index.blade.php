@extends('layouts.admin')
@section('title', 'Loyalty Points')
@section('header', 'Loyalty Points')
@section('content')
@include('operations._alerts')
<form method="POST" action="{{ route('admin.loyalty.settings') }}" class="mb-6 bg-white rounded-2xl border p-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
    @csrf
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Points per Rs 1 paid</label>
        <input type="number" step="0.01" min="0" name="loyalty_points_per_currency" value="{{ old('loyalty_points_per_currency', $settings->loyalty_points_per_currency) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Value of 1 point (Rs)</label>
        <input type="number" step="0.01" min="0" name="loyalty_point_value" value="{{ old('loyalty_point_value', $settings->loyalty_point_value) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Minimum to redeem</label>
        <input type="number" min="1" name="loyalty_min_redeem" value="{{ old('loyalty_min_redeem', $settings->loyalty_min_redeem) }}" class="w-full rounded-xl border-slate-200 font-bold"></div>
    <div class="flex items-center gap-3">
        <label class="flex items-center gap-2 font-bold text-sm"><input type="checkbox" name="loyalty_enabled" value="1" @checked($settings->loyalty_enabled) class="rounded"> Enabled</label>
        <button class="bg-slate-800 text-white font-bold px-4 py-2.5 rounded-xl">Save</button>
    </div>
</form>
<p class="text-xs text-slate-400 mb-4">Customers are identified by phone number. Points are earned when an order that has a customer phone is marked paid, and can be redeemed on the online order page or at the cashier. Outstanding points: <b>{{ number_format($outstanding) }}</b>.</p>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search phone or name" class="flex-1 rounded-xl border-slate-200">
    <button class="px-5 rounded-xl bg-slate-800 text-white font-bold">Search</button>
</form>
<div class="bg-white rounded-2xl border overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b">
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Customer</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Phone</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Balance</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Lifetime</th>
            <th class="px-6 py-4"></th></tr></thead>
        <tbody class="divide-y">
        @forelse ($accounts as $a)
            <tr><td class="px-6 py-4 font-bold">{{ $a->name ?: '-' }}</td><td class="px-6 py-4 text-slate-500">{{ $a->phone }}</td>
                <td class="px-6 py-4 font-black text-emerald-600">{{ number_format($a->balance) }}</td><td class="px-6 py-4 text-slate-500">{{ number_format($a->lifetime_earned) }}</td>
                <td class="px-6 py-4 text-right"><a href="{{ route('admin.loyalty.show', $a) }}" class="text-blue-500 font-black text-sm">Ledger</a></td></tr>
        @empty
            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No loyalty accounts yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $accounts->links() }}</div>
</div>
@endsection
