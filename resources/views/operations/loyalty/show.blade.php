@extends('layouts.admin')
@section('title', 'Loyalty Ledger')
@section('header', 'Loyalty: ' . ($account->name ?: $account->phone))
@section('content')
@include('operations._alerts')
<div class="mb-4"><a href="{{ route('admin.loyalty.index') }}" class="btn btn-sm btn-ghost">&larr; All accounts</a></div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="card p-6 h-fit">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Balance</div>
        <div class="text-3xl font-bold text-emerald-600">{{ number_format($account->balance) }} pts</div>
        <div class="text-sm text-slate-500 mb-4">{{ $account->phone }}</div>
        <form method="POST" action="{{ route('admin.loyalty.adjust', $account) }}" class="space-y-3">
            @csrf
            <input type="number" name="points" value="{{ old('points') }}" placeholder="Points (+ add / - remove)" required class="input">
            <input type="text" name="note" value="{{ old('note') }}" placeholder="Reason" required maxlength="190" class="input">
            <button class="btn btn-primary w-full">Adjust balance</button>
        </form>
    </div>
    <div class="lg:col-span-2 card overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Date</th><th class="px-6 py-4">Type</th><th class="px-6 py-4 text-right">Points</th><th class="px-6 py-4">Note</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($transactions as $t)
                <tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 text-sm text-slate-500">{{ $t->created_at->format('Y-m-d H:i') }}</td><td class="px-6 py-4 capitalize">{{ $t->type }}</td>
                    <td class="px-6 py-4 text-right font-semibold {{ $t->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $t->points > 0 ? '+' : '' }}{{ $t->points }}</td><td class="px-6 py-4 text-sm text-slate-500">{{ $t->note }}</td></tr>
            @empty
                <tr><td colspan="4"><x-ui.empty-state title="No transactions"/></td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
