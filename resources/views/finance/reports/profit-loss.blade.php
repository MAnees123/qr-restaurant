@extends('layouts.admin')
@section('title', 'Profit and Loss')
@section('header', 'Profit & Loss')
@section('content')
@include('finance.partials.daterange')
<div class="card p-6 sm:p-8 max-w-2xl space-y-2">
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Income</p>
    <div class="flex justify-between"><span>Sales revenue (paid orders)</span><b class="font-semibold">{{ number_format($revenue, 2) }}</b></div>
    @foreach($manualIncome as $x)<div class="flex justify-between"><span>{{ $x['name'] }} (journal)</span><b class="font-semibold">{{ number_format($x['amount'], 2) }}</b></div>@endforeach
    <div class="flex justify-between border-t border-slate-200 pt-2 font-bold"><span>Total income</span><span>{{ number_format($totalIncome, 2) }}</span></div>
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 pt-4">Expenses</p>
    <div class="flex justify-between"><span>Operating expenses</span><b class="font-semibold">{{ number_format($expenses, 2) }}</b></div>
    <div class="flex justify-between"><span>Purchases (received)</span><b class="font-semibold">{{ number_format($purchases, 2) }}</b></div>
    @foreach($manualExpense as $x)<div class="flex justify-between"><span>{{ $x['name'] }} (journal)</span><b class="font-semibold">{{ number_format($x['amount'], 2) }}</b></div>@endforeach
    <div class="flex justify-between border-t border-slate-200 pt-2 font-bold"><span>Total expenses</span><span>{{ number_format($totalExpense, 2) }}</span></div>
    <div class="flex justify-between border-t-2 border-slate-300 pt-3 text-xl font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-red-600' }}"><span>Net {{ $net >= 0 ? 'profit' : 'loss' }}</span><span>Rs {{ number_format($net, 2) }}</span></div>
</div>
@endsection
