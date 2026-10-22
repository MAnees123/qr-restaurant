@extends('layouts.admin')
@section('title', 'Trial Balance')
@section('header', 'Trial Balance')
@section('content')
@include('finance.partials.daterange')
<p class="text-xs text-slate-400 mb-3">Based on manual journal entries posted in the range. Orders, expenses and purchases are not auto-posted; see Profit &amp; Loss for the combined view.</p>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>@foreach(['Code','Account','Type','Debit','Credit'] as $h)<th class="px-6 py-4 {{ in_array($h, ['Debit','Credit']) ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
        <tbody>@forelse($rows as $r)<tr><td class="px-6 py-4 font-semibold">{{ $r->code }}</td><td class="px-6 py-4">{{ $r->name }}</td><td class="px-6 py-4 text-sm"><x-ui.badge type="info" class="capitalize">{{ $r->type }}</x-ui.badge></td><td class="px-6 py-4 text-right font-semibold">{{ $r->dr ? number_format($r->dr, 2) : '' }}</td><td class="px-6 py-4 text-right font-semibold">{{ $r->cr ? number_format($r->cr, 2) : '' }}</td></tr>
        @empty<tr><td colspan="5"><x-ui.empty-state title="No journal activity in this range" /></td></tr>@endforelse</tbody>
        <tfoot><tr class="border-t-2 border-slate-200 bg-slate-50 font-bold"><td colspan="3" class="px-6 py-4">Totals</td><td class="px-6 py-4 text-right">{{ number_format($totalDr, 2) }}</td><td class="px-6 py-4 text-right">{{ number_format($totalCr, 2) }}</td></tr></tfoot>
    </table>
</div>
@endsection
