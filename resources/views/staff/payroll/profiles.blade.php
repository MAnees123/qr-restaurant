@extends('layouts.admin')
@section('title', 'Salary Profiles')
@section('header', 'Salary Profiles')
@section('content')
@include('staff._errors')
<a href="{{ route('admin.payroll.index') }}" class="text-sm font-bold text-blue-500">&larr; Payroll</a>
<div class="space-y-3 mt-4">
@foreach($staff as $u) @php $p = $profiles->get($u->id); @endphp
<form action="{{ route('admin.payroll.profiles.save') }}" method="POST" class="bg-white rounded-2xl border shadow-sm p-4 grid grid-cols-2 md:grid-cols-7 gap-3 items-end">@csrf
<input type="hidden" name="user_id" value="{{ $u->id }}">
<div class="col-span-2 md:col-span-1 font-black text-slate-800">{{ $u->name }}<div class="text-xs font-medium text-slate-400">{{ $u->role }}</div></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Base</label><input type="number" step="0.01" min="0" name="base_salary" value="{{ $p->base_salary ?? 0 }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Allowances</label><input type="number" step="0.01" min="0" name="allowances" value="{{ $p->allowances ?? 0 }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Deductions</label><input type="number" step="0.01" min="0" name="deductions" value="{{ $p->deductions ?? 0 }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">OT / hour</label><input type="number" step="0.01" min="0" name="overtime_rate" value="{{ $p->overtime_rate ?? 0 }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Work days</label><input type="number" min="1" max="31" name="working_days" value="{{ $p->working_days ?? 26 }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<button class="bg-emerald-600 text-white font-bold py-3 rounded-xl">{{ $p ? 'Update' : 'Create' }}</button>
</form>
@endforeach</div>
@endsection
