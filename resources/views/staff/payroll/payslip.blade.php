@extends('layouts.admin')
@section('title', 'Payslip')
@section('header', 'Payslip')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-2xl border shadow-sm p-8" id="payslip">
    <div class="flex justify-between mb-6">
        <div><h2 class="text-xl font-black">{{ $restaurant->name }}</h2><p class="text-sm text-slate-500">Payslip for {{ $payslip->run->month }}</p></div>
        <span class="text-xs font-black uppercase self-start px-2 py-1 rounded-md {{ $payslip->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $payslip->status }}</span>
    </div>
    <p class="font-bold mb-4">{{ $payslip->user->name }} <span class="text-slate-400 font-medium">({{ $payslip->user->email }})</span></p>
    <table class="w-full text-sm">
        @foreach([['Days paid', $payslip->days_present + 0], ['Days absent', $payslip->days_absent + 0], ['Overtime hours', $payslip->overtime_hours + 0]] as [$l, $v])
        <tr class="border-b"><td class="py-2 text-slate-500">{{ $l }}</td><td class="py-2 text-right">{{ $v }}</td></tr>@endforeach
        @foreach([['Base salary', $payslip->base_salary, 1], ['Allowances', $payslip->allowances, 1], ['Overtime pay', $payslip->overtime_pay, 1], ['Deductions', $payslip->deductions, -1], ['Absence deduction', $payslip->absence_deduction, -1]] as [$l, $v, $sign])
        <tr class="border-b"><td class="py-2 text-slate-500">{{ $l }}</td><td class="py-2 text-right {{ $sign < 0 ? 'text-red-600' : '' }}">{{ $sign < 0 ? '-' : '' }}Rs {{ number_format($v, 2) }}</td></tr>@endforeach
        <tr><td class="py-3 font-black">Net pay</td><td class="py-3 text-right font-black text-lg">Rs {{ number_format($payslip->net_pay, 2) }}</td></tr>
    </table>
    @if($payslip->paid_at)<p class="text-xs text-slate-400 mt-2">Paid on {{ $payslip->paid_at->format('d M Y H:i') }}</p>@endif
    <div class="mt-6 flex gap-2 print:hidden">
        <button onclick="window.print()" class="bg-slate-800 text-white font-bold px-5 py-2 rounded-xl">Print</button>
        <a href="{{ route('admin.payroll.show', $payslip->run) }}" class="px-5 py-2 font-bold text-slate-500">Back</a>
    </div>
</div>
@endsection
