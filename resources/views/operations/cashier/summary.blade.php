@extends($layout)
@section('title', 'Daily Cash Summary')
@section('header', 'Daily Cash Summary')
@section('content')
<form method="GET" class="mb-6 flex gap-2 items-end">
    <div><label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Date</label>
    <input type="date" name="date" value="{{ $summary['date'] }}" class="rounded-xl border-slate-200"></div>
    <button class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold">Show</button>
</form>
<div class="grid sm:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Total collected</div><div class="text-2xl font-black text-emerald-600">Rs {{ number_format($summary['total'], 2) }}</div></div>
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Payments</div><div class="text-2xl font-black">{{ $summary['count'] }}</div></div>
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs font-black uppercase tracking-widest text-slate-400">Currently unpaid</div><div class="text-2xl font-black text-amber-600">{{ $summary['unpaid_count'] }} (Rs {{ number_format($summary['unpaid_total'], 2) }})</div></div>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b"><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Method</th><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Payments</th><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase text-right">Amount</th></tr></thead>
        <tbody class="divide-y">
        @forelse ($summary['by_method'] as $method => $row)
            <tr><td class="px-4 py-3 font-bold capitalize">{{ $method }}</td><td class="px-4 py-3">{{ $row['count'] }}</td><td class="px-4 py-3 text-right font-black">Rs {{ number_format($row['amount'], 2) }}</td></tr>
        @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No payments on this date.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
