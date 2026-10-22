@extends('layouts.admin')
@section('title', 'Attendance')
@section('header', 'Attendance')
@section('content')
@include('staff._errors')
<div class="space-y-6">
<div class="flex flex-wrap gap-3 items-end justify-between">
<form method="GET" class="flex gap-2 items-end"><div><label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label><input type="date" name="date" value="{{ $date->toDateString() }}" class="input"></div><button class="btn btn-primary">Go</button></form>
<form action="{{ route('admin.attendance.mark-absent') }}" method="POST">@csrf<input type="hidden" name="date" value="{{ $date->toDateString() }}"><button class="btn btn-danger" data-confirm="Mark every staff member without a record as absent?">Mark unrecorded as absent</button></form>
</div>

<div class="card overflow-x-auto">
<h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Daily sheet - {{ $date->format('d M Y') }}</h3>
<table class="w-full text-left">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Staff</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">In</th><th class="px-6 py-4">Out</th><th class="px-6 py-4 text-right">Hours</th><th class="px-6 py-4">Correct / mark</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($staff as $u) @php $r = $daily->get($u->id); $st = $r->status ?? null; $stType = match($st) { 'present' => 'success', 'late' => 'warning', 'absent' => 'danger', 'leave', 'half_day' => 'info', default => 'neutral' }; @endphp
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold text-slate-800">{{ $u->name }}</td>
<td class="px-6 py-4 text-sm">@if($r)<x-ui.badge :type="$stType" class="capitalize">{{ str_replace('_',' ',$r->status) }}</x-ui.badge>@else<span class="text-slate-400">-</span>@endif</td>
<td class="px-6 py-4 text-sm">{{ $r?->clock_in?->format('H:i') ?? '-' }}</td>
<td class="px-6 py-4 text-sm">{{ $r?->clock_out?->format('H:i') ?? '-' }}</td>
<td class="px-6 py-4 text-sm text-right font-semibold">{{ $r?->workedLabel() ?? '-' }}</td>
<td class="px-6 py-4"><div class="flex flex-wrap gap-2 items-center"><form action="{{ route('admin.attendance.store') }}" method="POST" class="flex flex-wrap gap-1 items-center">@csrf
<input type="hidden" name="user_id" value="{{ $u->id }}"><input type="hidden" name="work_date" value="{{ $date->toDateString() }}">
<select name="status" class="input !w-auto text-xs">@foreach($statuses as $s)<option value="{{ $s }}" @selected(($r->status ?? 'present') === $s)>{{ str_replace('_',' ',$s) }}</option>@endforeach</select>
<input type="time" name="clock_in" value="{{ $r?->clock_in?->format('H:i') }}" class="input !w-auto text-xs">
<input type="time" name="clock_out" value="{{ $r?->clock_out?->format('H:i') }}" class="input !w-auto text-xs">
<button class="btn btn-sm btn-primary">Save</button></form>
@if($r)<form action="{{ route('admin.attendance.destroy', $r) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600" data-confirm="Remove this record?">Remove</button></form>@endif</div>
</td></tr>
@empty
<tr><td colspan="6"><x-ui.empty-state title="No staff found" message="Add staff accounts to start tracking attendance."/></td></tr>
@endforelse
</tbody></table></div>

<div class="card overflow-x-auto">
<h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Monthly summary - {{ $date->format('F Y') }}</h3>
<table class="w-full text-left">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Staff</th><th class="px-6 py-4 text-right">Days present</th><th class="px-6 py-4 text-right">Late</th><th class="px-6 py-4 text-right">Absent</th><th class="px-6 py-4 text-right">Leave</th><th class="px-6 py-4 text-right">Hours</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($staff as $u) @php $s = $summary[$u->id]; @endphp
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold text-slate-800">{{ $u->name }}</td><td class="px-6 py-4 text-right font-semibold">{{ $s['present'] }}</td><td class="px-6 py-4 text-right font-semibold">{{ $s['late'] }}</td><td class="px-6 py-4 text-right font-semibold">{{ $s['absent'] }}</td><td class="px-6 py-4 text-right font-semibold">{{ $s['leave'] }}</td><td class="px-6 py-4 text-right font-semibold">{{ round($s['minutes']/60, 1) }}</td></tr>
@empty
<tr><td colspan="6"><x-ui.empty-state title="No staff found"/></td></tr>
@endforelse</tbody></table></div>

<div class="card p-6 max-w-xl">
<h3 class="font-semibold text-slate-800 mb-1">Shift settings</h3>
<p class="text-xs text-slate-500 mb-4">Clock-ins after shift start plus grace are marked late; hours beyond standard hours count as overtime in payroll. Staff clock in at <span class="font-semibold">{{ url('/staff/attendance') }}</span>.</p>
<form action="{{ route('admin.attendance.settings') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">@csrf @method('PUT')
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Shift start</label><input type="time" name="shift_start" value="{{ $settings->shift_start }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Grace (min)</label><input type="number" name="late_grace_minutes" value="{{ $settings->late_grace_minutes }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Std hours</label><input type="number" step="0.5" name="standard_hours" value="{{ $settings->standard_hours }}" class="input"></div>
<button class="btn btn-primary sm:col-span-3">Save settings</button></form></div>
</div>
@endsection
