@extends('staff.layout')
@section('title', 'My Attendance')
@section('header', 'My Attendance')
@section('content')
<div class="space-y-6">
<div class="card p-6 text-center">
    <p class="text-slate-500 text-sm mb-1">{{ now()->format('l, d M Y') }}</p>
    <p class="text-sm font-semibold text-slate-700 mb-4">
        In: {{ $today?->clock_in?->format('H:i') ?? '-' }} &middot; Out: {{ $today?->clock_out?->format('H:i') ?? '-' }}
    </p>
    <div class="flex gap-3 justify-center">
        <form action="{{ route('staff.attendance.clock-in') }}" method="POST">@csrf
            <button class="btn btn-primary px-8 disabled:opacity-40" @disabled($today?->clock_in)>Clock in</button></form>
        <form action="{{ route('staff.attendance.clock-out') }}" method="POST">@csrf
            <button class="btn btn-secondary px-8 disabled:opacity-40" @disabled(!$today?->clock_in || $today?->clock_out)>Clock out</button></form>
    </div>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Date</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">In</th><th class="px-6 py-4">Out</th><th class="px-6 py-4 text-right">Hours</th></tr></thead>
        <tbody class="divide-y divide-slate-100">@forelse($history as $r)
            @php $stType = match($r->status) { 'present' => 'success', 'late' => 'warning', 'absent' => 'danger', 'leave', 'half_day' => 'info', default => 'neutral' }; @endphp
            <tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4">{{ $r->work_date->format('d M') }}</td><td class="px-6 py-4"><x-ui.badge :type="$stType" class="capitalize">{{ str_replace('_',' ',$r->status) }}</x-ui.badge></td><td class="px-6 py-4">{{ $r->clock_in?->format('H:i') ?? '-' }}</td><td class="px-6 py-4">{{ $r->clock_out?->format('H:i') ?? '-' }}</td><td class="px-6 py-4 text-right font-semibold">{{ $r->workedLabel() }}</td></tr>
        @empty
            <tr><td colspan="5"><x-ui.empty-state title="No attendance history yet"/></td></tr>
        @endforelse</tbody>
    </table>
</div>
</div>
@endsection
