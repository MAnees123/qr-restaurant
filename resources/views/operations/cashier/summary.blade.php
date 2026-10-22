@extends($layout)
@section('title', 'Daily Cash Summary')
@section('header', 'Daily Cash Summary')
@section('content')
<div class="space-y-6">
<form method="GET" class="flex gap-2 items-end">
    <div><label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
    <input type="date" name="date" value="{{ $summary['date'] }}" class="input"></div>
    <button class="btn btn-primary">Show</button>
</form>
<div class="grid sm:grid-cols-3 gap-4">
    <x-ui.stat-card label="Total collected" :value="'Rs ' . number_format($summary['total'], 2)" tone="success"/>
    <x-ui.stat-card label="Payments" :value="$summary['count']"/>
    <x-ui.stat-card label="Currently unpaid" :value="$summary['unpaid_count'] . ' (Rs ' . number_format($summary['unpaid_total'], 2) . ')'" tone="warning"/>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Method</th><th class="px-6 py-4 text-right">Payments</th><th class="px-6 py-4 text-right">Amount</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($summary['by_method'] as $method => $row)
            <tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold capitalize">{{ $method }}</td><td class="px-6 py-4 text-right">{{ $row['count'] }}</td><td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($row['amount'], 2) }}</td></tr>
        @empty
            <tr><td colspan="3"><x-ui.empty-state title="No payments on this date"/></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
