@extends('layouts.admin')
@section('title', 'Purchase Orders')
@section('header', 'Purchase Orders')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-center">
    <div class="bg-white rounded-2xl border px-5 py-3"><span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total payable</span> <span class="ml-2 text-xl font-black text-red-600">Rs {{ number_format($payable, 2) }}</span></div>
    <div class="flex gap-2">
        <a href="{{ route('admin.purchases.index', ['payment' => 'unpaid']) }}" class="px-4 py-2 rounded-xl border font-bold text-sm">Unpaid only</a>
        <a href="{{ route('admin.purchases.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg shadow-emerald-100">New Purchase Order</a>
    </div>
</div>
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b">@foreach(['PO #','Supplier','Date','Total','Paid','Status','Payment',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($orders as $o)
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-4 font-black text-slate-800">{{ $o->po_number }}</td>
                <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $o->supplier->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $o->order_date->format('d M Y') }}</td>
                <td class="px-6 py-4 font-bold">Rs {{ number_format($o->total_amount, 2) }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">Rs {{ number_format($o->paid_amount, 2) }}</td>
                <td class="px-6 py-4 text-xs font-black uppercase">{{ $o->status }}</td>
                <td class="px-6 py-4 text-xs font-black uppercase {{ $o->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $o->payment_status }}</td>
                <td class="px-6 py-4"><a href="{{ route('admin.purchases.show', $o) }}" class="text-blue-500 font-black text-sm">View</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-6 py-10 text-center text-slate-400">No purchase orders.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $orders->links() }}</div>
</div>
@endsection
