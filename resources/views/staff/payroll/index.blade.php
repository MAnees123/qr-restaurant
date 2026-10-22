@extends('layouts.admin')
@section('title', 'Payroll')
@section('header', 'Payroll')
@section('content')
@include('staff._errors')
<div class="space-y-6">
<div class="flex flex-wrap gap-3 items-end justify-between">
<form action="{{ route('admin.payroll.generate') }}" method="POST" class="flex gap-2 items-end">@csrf
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Generate payroll for month</label><input type="month" name="month" value="{{ old('month', now()->format('Y-m')) }}" required class="input"></div>
<button class="btn btn-primary">Generate</button></form>
<a href="{{ route('admin.payroll.profiles') }}" class="btn btn-secondary">Salary profiles</a></div>
<p class="text-xs text-slate-500">Payroll is calculated from attendance records. Absent days are working days minus paid days (present, late, leave, half days = 0.5). No money is transferred by this system; "paid" is a record you set manually.</p>
<div class="card overflow-x-auto"><table class="w-full text-left">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Month</th><th class="px-6 py-4 text-right">Payslips</th><th class="px-6 py-4 text-right">Total net</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($runs as $run)
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold">{{ $run->month }}</td><td class="px-6 py-4 text-right">{{ $run->payslips_count }}</td><td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($run->payslips_sum_net_pay ?? 0, 2) }}</td>
<td class="px-6 py-4 text-right"><div class="flex justify-end items-center gap-2"><a href="{{ route('admin.payroll.show', $run) }}" class="btn btn-sm btn-secondary">Open</a>
<form action="{{ route('admin.payroll.destroy', $run) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600" data-confirm="Delete this payroll run?">Delete</button></form></div></td></tr>
@empty<tr><td colspan="4"><x-ui.empty-state title="No payroll runs yet" message="Generate payroll for a month to get started."/></td></tr>@endforelse
</tbody></table></div>
</div>
@endsection
