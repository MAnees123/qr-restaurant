@extends('staff.layout')
@section('title', 'My Attendance')
@section('header', 'My Attendance')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border p-6 text-center mb-6">
    <p class="text-slate-500 text-sm mb-1">{{ now()->format('l, d M Y') }}</p>
    <p class="text-sm font-bold text-slate-700 mb-4">
        In: {{ $today?->clock_in?->format('H:i') ?? '-' }} &middot; Out: {{ $today?->clock_out?->format('H:i') ?? '-' }}
    </p>
    <div class="flex gap-3 justify-center">
        <form action="{{ route('staff.attendance.clock-in') }}" method="POST">@csrf
            <button class="bg-emerald-600 text-white font-black px-8 py-3 rounded-2xl disabled:opacity-40" @disabled($today?->clock_in)>Clock in</button></form>
        <form action="{{ route('staff.attendance.clock-out') }}" method="POST">@csrf
            <button class="bg-slate-800 text-white font-black px-8 py-3 rounded-2xl disabled:opacity-40" @disabled(!$today?->clock_in || $today?->clock_out)>Clock out</button></form>
    </div>
</div>
<div class="bg-white rounded-2xl shadow-sm border overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead><tr class="bg-slate-50 text-xs font-black text-slate-400 uppercase"><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">In</th><th class="px-4 py-3">Out</th><th class="px-4 py-3">Hours</th></tr></thead>
        <tbody class="divide-y">@foreach($history as $r)
            <tr><td class="px-4 py-3">{{ $r->work_date->format('d M') }}</td><td class="px-4 py-3 capitalize">{{ str_replace('_',' ',$r->status) }}</td><td class="px-4 py-3">{{ $r->clock_in?->format('H:i') ?? '-' }}</td><td class="px-4 py-3">{{ $r->clock_out?->format('H:i') ?? '-' }}</td><td class="px-4 py-3">{{ $r->workedLabel() }}</td></tr>
        @endforeach</tbody>
    </table>
</div>
@endsection
