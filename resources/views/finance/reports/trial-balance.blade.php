@extends('layouts.admin')
@section('title', 'Trial Balance')
@section('header', 'Trial Balance')
@section('content')
@include('finance.partials.daterange')
<p class="text-xs text-slate-400 mb-3">Based on manual journal entries posted in the range. Orders, expenses and purchases are not auto-posted; see Profit &amp; Loss for the combined view.</p>
<div class="bg-white rounded-[2rem] border shadow-sm overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b">@foreach(['Code','Account','Type','Debit','Credit'] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y">@forelse($rows as $r)<tr><td class="px-6 py-3 font-black">{{ $r->code }}</td><td class="px-6 py-3">{{ $r->name }}</td><td class="px-6 py-3 capitalize text-sm">{{ $r->type }}</td><td class="px-6 py-3">{{ $r->dr ? number_format($r->dr, 2) : '' }}</td><td class="px-6 py-3">{{ $r->cr ? number_format($r->cr, 2) : '' }}</td></tr>
        @empty<tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No journal activity in this range.</td></tr>@endforelse</tbody>
        <tfoot><tr class="border-t-2 font-black"><td colspan="3" class="px-6 py-3">Totals</td><td class="px-6 py-3">{{ number_format($totalDr, 2) }}</td><td class="px-6 py-3">{{ number_format($totalCr, 2) }}</td></tr></tfoot>
    </table>
</div>
@endsection
