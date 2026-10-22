@extends('layouts.admin')
@section('title', 'Tax Summary')
@section('header', 'Tax Summary')
@section('content')
@include('finance.partials.daterange', ['route' => 'admin.taxes.summary'])
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>@foreach(['Tax','Rate','Type','Invoices','Taxable amount','Tax collected','Total'] as $h)<th class="px-6 py-4 {{ in_array($h, ['Rate','Invoices','Taxable amount','Tax collected','Total']) ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($rows as $r)<tr><td class="px-6 py-4 font-medium">{{ $r->tax_name }}</td><td class="px-6 py-4 text-right">{{ rtrim(rtrim($r->tax_rate, '0'), '.') }}%</td><td class="px-6 py-4 capitalize">{{ $r->tax_type ?? '-' }}</td><td class="px-6 py-4 text-right">{{ $r->invoices }}</td><td class="px-6 py-4 text-right">{{ number_format($r->taxable, 2) }}</td><td class="px-6 py-4 text-right font-semibold">{{ number_format($r->tax, 2) }}</td><td class="px-6 py-4 text-right font-semibold">{{ number_format($r->total, 2) }}</td></tr>
        @empty<tr><td colspan="7"><x-ui.empty-state title="No invoices in this range" /></td></tr>@endforelse
        </tbody>
        @if($rows->isNotEmpty())<tfoot><tr class="border-t-2 border-slate-200 bg-slate-50 font-bold"><td class="px-6 py-4" colspan="5">Total tax collected</td><td class="px-6 py-4 text-right">{{ number_format($rows->sum('tax'), 2) }}</td><td></td></tr></tfoot>@endif
    </table>
</div>
@endsection
