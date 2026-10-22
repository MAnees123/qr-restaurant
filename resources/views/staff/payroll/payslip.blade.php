@extends('layouts.admin')
@section('title', 'Payslip')
@section('header', 'Payslip')
@section('content')
<div class="max-w-xl mx-auto card p-6 sm:p-8" id="payslip">
    <div class="flex justify-between gap-3 mb-6">
        <div><h2 class="text-xl font-bold text-slate-800">{{ $restaurant->name }}</h2><p class="text-sm text-slate-500">Payslip for {{ $payslip->run->month }}</p></div>
        <x-ui.badge :type="$payslip->status === 'paid' ? 'success' : 'warning'" class="self-start uppercase">{{ $payslip->status }}</x-ui.badge>
    </div>
    <p class="font-semibold mb-4">{{ $payslip->user->name }} <span class="text-slate-400 font-normal">({{ $payslip->user->email }})</span></p>
    <table class="w-full text-sm">
        @foreach([['Days paid', $payslip->days_present + 0], ['Days absent', $payslip->days_absent + 0], ['Overtime hours', $payslip->overtime_hours + 0]] as [$l, $v])
        <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500">{{ $l }}</td><td class="py-2.5 text-right font-semibold">{{ $v }}</td></tr>@endforeach
        @foreach([['Base salary', $payslip->base_salary, 1], ['Allowances', $payslip->allowances, 1], ['Overtime pay', $payslip->overtime_pay, 1], ['Deductions', $payslip->deductions, -1], ['Absence deduction', $payslip->absence_deduction, -1]] as [$l, $v, $sign])
        <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500">{{ $l }}</td><td class="py-2.5 text-right font-semibold {{ $sign < 0 ? 'text-red-600' : '' }}">{{ $sign < 0 ? '-' : '' }}Rs {{ number_format($v, 2) }}</td></tr>@endforeach
        <tr><td class="py-3 font-bold">Net pay</td><td class="py-3 text-right font-bold text-lg text-indigo-600">Rs {{ number_format($payslip->net_pay, 2) }}</td></tr>
    </table>
    @if($payslip->paid_at)<p class="text-xs text-slate-400 mt-2">Paid on {{ $payslip->paid_at->format('d M Y H:i') }}</p>@endif
    <div class="mt-6 flex gap-2 print:hidden">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('admin.payroll.show', $payslip->run) }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
