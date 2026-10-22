@extends('layouts.admin')
@section('title', 'Reports')
@section('header', 'Reports')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([['Revenue', $kpi['revenue'], 'success'], ['Paid orders', $kpi['orders'], 'brand'], ['Expenses', $kpi['expenses'], 'danger'], ['Purchases', $kpi['purchases'], 'warning']] as [$l, $v, $tone])
    <x-ui.stat-card :label="$l" :value="$l === 'Paid orders' ? $v : 'Rs ' . number_format($v, 2)" :tone="$tone" />
    @endforeach
</div>
<div class="flex flex-wrap gap-2 mb-4">
    @foreach($reports as $key => $label)<a href="{{ route('admin.reports.index', ['report' => $key, 'from' => $from, 'to' => $to]) }}" class="btn btn-sm {{ $report === $key ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>@endforeach
</div>
@include('finance.partials.daterange', ['keep' => ['report' => $report]])
<div class="card overflow-x-auto">
    <div class="px-6 py-4 flex flex-wrap gap-2 justify-between items-center border-b border-slate-100"><p class="font-semibold text-slate-800">{{ $reports[$report] }}</p>
        <a href="{{ route('admin.reports.index', ['report' => $report, 'from' => $from, 'to' => $to, 'format' => 'csv']) }}" class="btn btn-sm btn-secondary">Download CSV</a></div>
    <table class="w-full text-left">
        <thead><tr>@foreach($headers as $h)<th class="px-6 py-3">{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($rows as $row)<tr>@foreach($row as $c)<td class="px-6 py-4 text-sm {{ is_numeric($c) ? 'font-semibold' : '' }}">{{ is_float($c) ? number_format($c, 2) : $c }}</td>@endforeach</tr>
        @empty<tr><td colspan="{{ count($headers) }}"><x-ui.empty-state title="No data for this range" /></td></tr>@endforelse
        </tbody>
    </table>
</div>
<p class="text-xs text-slate-400 mt-3">Revenue counts orders with payment status "paid" that are not cancelled.</p>
@endsection
