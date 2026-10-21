@extends('layouts.admin')
@section('title', 'Payroll ' . $run->month)
@section('header', 'Payroll ' . $run->month)
@section('content')
@include('staff._errors')
<a href="{{ route('admin.payroll.index') }}" class="text-sm font-bold text-blue-500">&larr; Payroll</a>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto mt-4"><table class="w-full text-left text-sm">
<thead><tr class="bg-slate-50/50 border-b text-xs font-black text-slate-400 uppercase"><th class="px-4 py-3">Staff</th><th class="px-4 py-3">Present</th><th class="px-4 py-3">Absent</th><th class="px-4 py-3">OT h</th><th class="px-4 py-3">Net pay</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($payslips as $s)
<tr><td class="px-4 py-3 font-bold">{{ $s->user->name }}</td><td class="px-4 py-3">{{ $s->days_present + 0 }}</td><td class="px-4 py-3">{{ $s->days_absent + 0 }}</td><td class="px-4 py-3">{{ $s->overtime_hours + 0 }}</td>
<td class="px-4 py-3 font-black">Rs {{ number_format($s->net_pay, 2) }}</td>
<td class="px-4 py-3"><span class="text-[10px] font-black uppercase px-2 py-1 rounded-md {{ $s->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $s->status }}</span></td>
<td class="px-4 py-3 text-right space-x-2"><a href="{{ route('admin.payroll.payslip', $s) }}" class="text-blue-500 font-black">Payslip</a>
@if($s->status !== 'paid')<form action="{{ route('admin.payroll.pay', $s) }}" method="POST" class="inline">@csrf<button class="text-emerald-600 font-black" data-confirm="Mark this payslip as paid?">Mark paid</button></form>@endif</td></tr>
@endforeach</tbody></table></div>
@endsection
