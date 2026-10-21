@extends('layouts.admin')
@section('title', 'Attendance')
@section('header', 'Attendance')
@section('content')
@include('staff._errors')
<div class="flex flex-wrap gap-3 items-end justify-between mb-6">
<form method="GET" class="flex gap-2 items-end"><div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Date</label><input type="date" name="date" value="{{ $date->toDateString() }}" class="rounded-xl border-slate-200 font-bold"></div><button class="bg-slate-800 text-white font-bold px-4 py-2 rounded-xl">Go</button></form>
<form action="{{ route('admin.attendance.mark-absent') }}" method="POST">@csrf<input type="hidden" name="date" value="{{ $date->toDateString() }}"><button class="bg-red-500 text-white font-bold px-4 py-2 rounded-xl" data-confirm="Mark every staff member without a record as absent?">Mark unrecorded as absent</button></form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto mb-8">
<h3 class="px-6 pt-5 font-black text-slate-800">Daily sheet - {{ $date->format('d M Y') }}</h3>
<table class="w-full text-left mt-3">
<thead><tr class="bg-slate-50/50 border-b text-xs font-black text-slate-400 uppercase"><th class="px-6 py-3">Staff</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">In</th><th class="px-6 py-3">Out</th><th class="px-6 py-3">Hours</th><th class="px-6 py-3">Correct / mark</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($staff as $u) @php $r = $daily->get($u->id); @endphp
<tr><td class="px-6 py-3 font-bold text-slate-800">{{ $u->name }}</td>
<td class="px-6 py-3 text-sm capitalize">{{ $r ? str_replace('_',' ',$r->status) : '-' }}</td>
<td class="px-6 py-3 text-sm">{{ $r?->clock_in?->format('H:i') ?? '-' }}</td>
<td class="px-6 py-3 text-sm">{{ $r?->clock_out?->format('H:i') ?? '-' }}</td>
<td class="px-6 py-3 text-sm">{{ $r?->workedLabel() ?? '-' }}</td>
<td class="px-6 py-3"><form action="{{ route('admin.attendance.store') }}" method="POST" class="flex flex-wrap gap-1 items-center">@csrf
<input type="hidden" name="user_id" value="{{ $u->id }}"><input type="hidden" name="work_date" value="{{ $date->toDateString() }}">
<select name="status" class="rounded-lg border-slate-200 text-xs">@foreach($statuses as $s)<option value="{{ $s }}" @selected(($r->status ?? 'present') === $s)>{{ str_replace('_',' ',$s) }}</option>@endforeach</select>
<input type="time" name="clock_in" value="{{ $r?->clock_in?->format('H:i') }}" class="rounded-lg border-slate-200 text-xs">
<input type="time" name="clock_out" value="{{ $r?->clock_out?->format('H:i') }}" class="rounded-lg border-slate-200 text-xs">
<button class="bg-emerald-600 text-white text-xs font-bold px-3 py-2 rounded-lg">Save</button></form>
@if($r)<form action="{{ route('admin.attendance.destroy', $r) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-500 text-xs font-black" data-confirm="Remove this record?">Remove</button></form>@endif
</td></tr>
@endforeach
</tbody></table></div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto mb-8">
<h3 class="px-6 pt-5 font-black text-slate-800">Monthly summary - {{ $date->format('F Y') }}</h3>
<table class="w-full text-left mt-3">
<thead><tr class="bg-slate-50/50 border-b text-xs font-black text-slate-400 uppercase"><th class="px-6 py-3">Staff</th><th class="px-6 py-3">Days present</th><th class="px-6 py-3">Late</th><th class="px-6 py-3">Absent</th><th class="px-6 py-3">Leave</th><th class="px-6 py-3">Hours</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($staff as $u) @php $s = $summary[$u->id]; @endphp
<tr><td class="px-6 py-3 font-bold">{{ $u->name }}</td><td class="px-6 py-3">{{ $s['present'] }}</td><td class="px-6 py-3">{{ $s['late'] }}</td><td class="px-6 py-3">{{ $s['absent'] }}</td><td class="px-6 py-3">{{ $s['leave'] }}</td><td class="px-6 py-3">{{ round($s['minutes']/60, 1) }}</td></tr>
@endforeach</tbody></table></div>

<div class="bg-white rounded-2xl shadow-sm border p-6 max-w-xl">
<h3 class="font-black text-slate-800 mb-1">Shift settings</h3>
<p class="text-xs text-slate-400 mb-4">Clock-ins after shift start plus grace are marked late; hours beyond standard hours count as overtime in payroll. Staff clock in at <span class="font-bold">{{ url('/staff/attendance') }}</span>.</p>
<form action="{{ route('admin.attendance.settings') }}" method="POST" class="grid grid-cols-3 gap-3 items-end">@csrf @method('PUT')
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Shift start</label><input type="time" name="shift_start" value="{{ $settings->shift_start }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Grace (min)</label><input type="number" name="late_grace_minutes" value="{{ $settings->late_grace_minutes }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Std hours</label><input type="number" step="0.5" name="standard_hours" value="{{ $settings->standard_hours }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<button class="col-span-3 bg-slate-800 text-white font-bold py-3 rounded-xl">Save settings</button></form></div>
@endsection
