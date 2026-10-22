@extends('layouts.admin')
@section('title', 'Salary Profiles')
@section('header', 'Salary Profiles')
@section('content')
@include('staff._errors')
<a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-ghost">&larr; Payroll</a>
<div class="space-y-4 mt-4">
@forelse($staff as $u) @php $p = $profiles->get($u->id); @endphp
<form action="{{ route('admin.payroll.profiles.save') }}" method="POST" class="card card-hover p-5 grid grid-cols-2 md:grid-cols-7 gap-4 items-end">@csrf
<input type="hidden" name="user_id" value="{{ $u->id }}">
<div class="col-span-2 md:col-span-1 font-semibold text-slate-800">{{ $u->name }}<div class="text-xs font-normal text-slate-400">{{ $u->role }}</div></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Base</label><input type="number" step="0.01" min="0" name="base_salary" value="{{ $p->base_salary ?? 0 }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Allowances</label><input type="number" step="0.01" min="0" name="allowances" value="{{ $p->allowances ?? 0 }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Deductions</label><input type="number" step="0.01" min="0" name="deductions" value="{{ $p->deductions ?? 0 }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">OT / hour</label><input type="number" step="0.01" min="0" name="overtime_rate" value="{{ $p->overtime_rate ?? 0 }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Work days</label><input type="number" min="1" max="31" name="working_days" value="{{ $p->working_days ?? 26 }}" class="input"></div>
<button class="btn btn-primary">{{ $p ? 'Update' : 'Create' }}</button>
</form>
@empty
<div class="card"><x-ui.empty-state title="No staff found"/></div>
@endforelse</div>
@endsection
