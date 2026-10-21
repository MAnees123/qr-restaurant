@extends('layouts.admin')
@section('title', 'Payroll')
@section('header', 'Payroll')
@section('content')
@include('staff._errors')
<div class="flex flex-wrap gap-3 items-end justify-between mb-6">
<form action="{{ route('admin.payroll.generate') }}" method="POST" class="flex gap-2 items-end">@csrf
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Generate payroll for month</label><input type="month" name="month" value="{{ old('month', now()->format('Y-m')) }}" required class="rounded-xl border-slate-200 font-bold"></div>
<button class="bg-emerald-600 text-white font-bold px-5 py-2 rounded-xl">Generate</button></form>
<a href="{{ route('admin.payroll.profiles') }}" class="bg-slate-800 text-white font-bold px-5 py-2 rounded-xl">Salary profiles</a></div>
<p class="text-xs text-slate-400 mb-4">Payroll is calculated from attendance records. Absent days are working days minus paid days (present, late, leave, half days = 0.5). No money is transferred by this system; "paid" is a record you set manually.</p>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto"><table class="w-full text-left">
<thead><tr class="bg-slate-50/50 border-b text-xs font-black text-slate-400 uppercase"><th class="px-6 py-3">Month</th><th class="px-6 py-3">Payslips</th><th class="px-6 py-3">Total net</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($runs as $run)
<tr><td class="px-6 py-3 font-black">{{ $run->month }}</td><td class="px-6 py-3">{{ $run->payslips_count }}</td><td class="px-6 py-3 font-bold">Rs {{ number_format($run->payslips_sum_net_pay ?? 0, 2) }}</td>
<td class="px-6 py-3 text-right space-x-2"><a href="{{ route('admin.payroll.show', $run) }}" class="text-blue-500 font-black text-sm">Open</a>
<form action="{{ route('admin.payroll.destroy', $run) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm" data-confirm="Delete this payroll run?">Delete</button></form></td></tr>
@empty<tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No payroll runs yet.</td></tr>@endforelse
</tbody></table></div>
@endsection
