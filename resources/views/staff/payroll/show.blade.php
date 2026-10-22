@extends('layouts.admin')
@section('title', 'Payroll ' . $run->month)
@section('header', 'Payroll ' . $run->month)
@section('content')
@include('staff._errors')
<a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-ghost">&larr; Payroll</a>
<div class="card overflow-x-auto mt-4"><table class="w-full text-left text-sm">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Staff</th><th class="px-6 py-4 text-right">Present</th><th class="px-6 py-4 text-right">Absent</th><th class="px-6 py-4 text-right">OT h</th><th class="px-6 py-4 text-right">Net pay</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($payslips as $s)
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold">{{ $s->user->name }}</td><td class="px-6 py-4 text-right">{{ $s->days_present + 0 }}</td><td class="px-6 py-4 text-right">{{ $s->days_absent + 0 }}</td><td class="px-6 py-4 text-right">{{ $s->overtime_hours + 0 }}</td>
<td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($s->net_pay, 2) }}</td>
<td class="px-6 py-4"><x-ui.badge :type="$s->status === 'paid' ? 'success' : 'warning'" class="uppercase">{{ $s->status }}</x-ui.badge></td>
<td class="px-6 py-4 text-right"><div class="flex justify-end items-center gap-2"><a href="{{ route('admin.payroll.payslip', $s) }}" class="btn btn-sm btn-secondary">Payslip</a>
@if($s->status !== 'paid')<form action="{{ route('admin.payroll.pay', $s) }}" method="POST" class="inline">@csrf<button class="btn btn-sm btn-primary" data-confirm="Mark this payslip as paid?">Mark paid</button></form>@endif</div></td></tr>
@empty
<tr><td colspan="7"><x-ui.empty-state title="No payslips in this run"/></td></tr>
@endforelse</tbody></table></div>
@endsection
