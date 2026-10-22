@extends('layouts.admin')
@section('title', 'Customer')
@section('header', $customer->name)
@section('content')
@include('staff._errors')
<a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-ghost">&larr; Customers</a>
<div class="grid md:grid-cols-3 gap-6 mt-4">
<div class="card p-6 text-sm space-y-2">
<p><span class="text-slate-400">Phone:</span> {{ $customer->phone ?? '-' }}</p><p><span class="text-slate-400">Email:</span> {{ $customer->email ?? '-' }}</p>
<p><span class="text-slate-400">Total spent:</span> <span class="font-semibold">Rs {{ number_format($spent, 2) }}</span></p>
<p>@foreach($customer->tagList() as $t)<x-ui.badge type="warning" class="mr-1">{{ $t }}</x-ui.badge>@endforeach</p>
<p class="whitespace-pre-line text-slate-600">{{ $customer->notes }}</p>
<form action="{{ route('admin.customers.link-order', $customer) }}" method="POST" class="pt-4 border-t border-slate-100 flex gap-2">@csrf
<input name="order_number" placeholder="Order number" class="input"><button class="btn btn-secondary">Link</button></form>
</div>
<div class="md:col-span-2 card overflow-x-auto"><h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Order history</h3>
<table class="w-full text-left text-sm"><thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Order</th><th class="px-6 py-4">Date</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Total</th></tr></thead><tbody class="divide-y divide-slate-100">
@forelse($orders as $o)@php $oType = match($o->status) { 'served', 'completed', 'paid' => 'success', 'cancelled' => 'danger', 'pending' => 'warning', default => 'info' }; @endphp
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold">{{ $o->order_number }}</td><td class="px-6 py-4">{{ $o->created_at->format('d M Y H:i') }}</td><td class="px-6 py-4"><x-ui.badge :type="$oType" class="capitalize">{{ $o->status }}</x-ui.badge></td><td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($o->total_amount, 2) }}</td></tr>
@empty<tr><td colspan="4"><x-ui.empty-state title="No orders linked yet"/></td></tr>@endforelse
</tbody></table><div class="px-6 py-3">{{ $orders->links() }}</div></div>
</div>
@endsection
