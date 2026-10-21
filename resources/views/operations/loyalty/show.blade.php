@extends('layouts.admin')
@section('title', 'Loyalty Ledger')
@section('header', 'Loyalty: ' . ($account->name ?: $account->phone))
@section('content')
@include('operations._alerts')
<div class="mb-4"><a href="{{ route('admin.loyalty.index') }}" class="text-sm font-bold text-slate-500">&larr; All accounts</a></div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl border p-6 h-fit">
        <div class="text-xs font-black uppercase tracking-widest text-slate-400">Balance</div>
        <div class="text-3xl font-black text-emerald-600">{{ number_format($account->balance) }} pts</div>
        <div class="text-sm text-slate-500 mb-4">{{ $account->phone }}</div>
        <form method="POST" action="{{ route('admin.loyalty.adjust', $account) }}" class="space-y-3">
            @csrf
            <input type="number" name="points" value="{{ old('points') }}" placeholder="Points (+ add / - remove)" required class="w-full rounded-xl border-slate-200">
            <input type="text" name="note" value="{{ old('note') }}" placeholder="Reason" required maxlength="190" class="w-full rounded-xl border-slate-200">
            <button class="w-full bg-slate-800 text-white font-bold py-2.5 rounded-xl">Adjust balance</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50 border-b"><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Date</th><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Type</th><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Points</th><th class="px-4 py-3 text-xs font-black text-slate-400 uppercase">Note</th></tr></thead>
            <tbody class="divide-y">
            @forelse ($transactions as $t)
                <tr><td class="px-4 py-3 text-sm text-slate-500">{{ $t->created_at->format('Y-m-d H:i') }}</td><td class="px-4 py-3 capitalize">{{ $t->type }}</td>
                    <td class="px-4 py-3 font-black {{ $t->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $t->points > 0 ? '+' : '' }}{{ $t->points }}</td><td class="px-4 py-3 text-sm text-slate-500">{{ $t->note }}</td></tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No transactions.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
