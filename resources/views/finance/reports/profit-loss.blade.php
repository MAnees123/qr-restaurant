@extends('layouts.admin')
@section('title', 'Profit and Loss')
@section('header', 'Profit & Loss')
@section('content')
@include('finance.partials.daterange')
<div class="bg-white rounded-[2rem] border shadow-sm p-8 max-w-2xl space-y-2">
    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Income</p>
    <div class="flex justify-between"><span>Sales revenue (paid orders)</span><b>{{ number_format($revenue, 2) }}</b></div>
    @foreach($manualIncome as $x)<div class="flex justify-between"><span>{{ $x['name'] }} (journal)</span><b>{{ number_format($x['amount'], 2) }}</b></div>@endforeach
    <div class="flex justify-between border-t pt-1 font-black"><span>Total income</span><span>{{ number_format($totalIncome, 2) }}</span></div>
    <p class="text-xs font-black text-slate-400 uppercase tracking-widest pt-4">Expenses</p>
    <div class="flex justify-between"><span>Operating expenses</span><b>{{ number_format($expenses, 2) }}</b></div>
    <div class="flex justify-between"><span>Purchases (received)</span><b>{{ number_format($purchases, 2) }}</b></div>
    @foreach($manualExpense as $x)<div class="flex justify-between"><span>{{ $x['name'] }} (journal)</span><b>{{ number_format($x['amount'], 2) }}</b></div>@endforeach
    <div class="flex justify-between border-t pt-1 font-black"><span>Total expenses</span><span>{{ number_format($totalExpense, 2) }}</span></div>
    <div class="flex justify-between border-t-2 pt-3 text-xl font-black {{ $net >= 0 ? 'text-emerald-600' : 'text-red-600' }}"><span>Net {{ $net >= 0 ? 'profit' : 'loss' }}</span><span>Rs {{ number_format($net, 2) }}</span></div>
</div>
@endsection
