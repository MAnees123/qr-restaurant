@extends('layouts.admin')
@section('title', 'Reports')
@section('header', 'Reports')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([['Revenue', $kpi['revenue']], ['Paid orders', $kpi['orders']], ['Expenses', $kpi['expenses']], ['Purchases', $kpi['purchases']]] as [$l, $v])
    <div class="bg-white rounded-2xl border p-4"><p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $l }}</p><p class="text-2xl font-black">{{ $l === 'Paid orders' ? $v : 'Rs ' . number_format($v, 2) }}</p></div>
    @endforeach
</div>
<div class="flex flex-wrap gap-2 mb-4">
    @foreach($reports as $key => $label)<a href="{{ route('admin.reports.index', ['report' => $key, 'from' => $from, 'to' => $to]) }}" class="px-4 py-2 rounded-xl text-sm font-bold border {{ $report === $key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600' }}">{{ $label }}</a>@endforeach
</div>
@include('finance.partials.daterange', ['keep' => ['report' => $report]])
<div class="bg-white rounded-[2rem] border shadow-sm overflow-x-auto">
    <div class="px-6 pt-4 flex justify-between items-center"><p class="font-black">{{ $reports[$report] }}</p>
        <a href="{{ route('admin.reports.index', ['report' => $report, 'from' => $from, 'to' => $to, 'format' => 'csv']) }}" class="text-blue-500 font-black text-sm">Download CSV</a></div>
    <table class="w-full text-left">
        <thead><tr class="border-b">@foreach($headers as $h)<th class="px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y">
        @forelse($rows as $row)<tr>@foreach($row as $c)<td class="px-6 py-3 text-sm {{ is_numeric($c) ? 'font-bold' : '' }}">{{ is_float($c) ? number_format($c, 2) : $c }}</td>@endforeach</tr>
        @empty<tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-slate-400">No data for this range.</td></tr>@endforelse
        </tbody>
    </table>
</div>
<p class="text-xs text-slate-400 mt-3">Revenue counts orders with payment status "paid" that are not cancelled.</p>
@endsection
