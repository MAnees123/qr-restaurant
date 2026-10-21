@extends('layouts.admin')
@section('title', 'Tax Summary')
@section('header', 'Tax Summary')
@section('content')
@include('finance.partials.daterange', ['route' => 'admin.taxes.summary'])
<div class="bg-white rounded-[2rem] border shadow-sm overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b">@foreach(['Tax','Rate','Type','Invoices','Taxable amount','Tax collected','Total'] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y">
        @forelse($rows as $r)<tr><td class="px-6 py-3 font-bold">{{ $r->tax_name }}</td><td class="px-6 py-3">{{ rtrim(rtrim($r->tax_rate, '0'), '.') }}%</td><td class="px-6 py-3 capitalize">{{ $r->tax_type ?? '-' }}</td><td class="px-6 py-3">{{ $r->invoices }}</td><td class="px-6 py-3">{{ number_format($r->taxable, 2) }}</td><td class="px-6 py-3 font-black">{{ number_format($r->tax, 2) }}</td><td class="px-6 py-3">{{ number_format($r->total, 2) }}</td></tr>
        @empty<tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">No invoices in this range.</td></tr>@endforelse
        </tbody>
        @if($rows->isNotEmpty())<tfoot><tr class="border-t-2 font-black"><td class="px-6 py-3" colspan="5">Total tax collected</td><td class="px-6 py-3">{{ number_format($rows->sum('tax'), 2) }}</td><td></td></tr></tfoot>@endif
    </table>
</div>
@endsection
