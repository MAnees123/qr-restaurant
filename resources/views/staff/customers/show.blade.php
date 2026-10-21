@extends('layouts.admin')
@section('title', 'Customer')
@section('header', $customer->name)
@section('content')
@include('staff._errors')
<a href="{{ route('admin.customers.index') }}" class="text-sm font-bold text-blue-500">&larr; Customers</a>
<div class="grid md:grid-cols-3 gap-6 mt-4">
<div class="bg-white rounded-2xl border shadow-sm p-6 text-sm space-y-2">
<p><span class="text-slate-400">Phone:</span> {{ $customer->phone ?? '-' }}</p><p><span class="text-slate-400">Email:</span> {{ $customer->email ?? '-' }}</p>
<p><span class="text-slate-400">Total spent:</span> <span class="font-black">Rs {{ number_format($spent, 2) }}</span></p>
<p>@foreach($customer->tagList() as $t)<span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-1 rounded-md mr-1">{{ $t }}</span>@endforeach</p>
<p class="whitespace-pre-line text-slate-600">{{ $customer->notes }}</p>
<form action="{{ route('admin.customers.link-order', $customer) }}" method="POST" class="pt-4 border-t flex gap-2">@csrf
<input name="order_number" placeholder="Order number" class="rounded-xl border-slate-200 text-sm w-full"><button class="bg-slate-800 text-white font-bold px-3 rounded-xl">Link</button></form>
</div>
<div class="md:col-span-2 bg-white rounded-2xl border shadow-sm overflow-x-auto"><h3 class="px-6 pt-5 font-black">Order history</h3>
<table class="w-full text-left text-sm mt-2"><thead><tr class="text-xs font-black text-slate-400 uppercase"><th class="px-6 py-2">Order</th><th class="px-6 py-2">Date</th><th class="px-6 py-2">Status</th><th class="px-6 py-2">Total</th></tr></thead><tbody class="divide-y">
@forelse($orders as $o)<tr><td class="px-6 py-2 font-bold">{{ $o->order_number }}</td><td class="px-6 py-2">{{ $o->created_at->format('d M Y H:i') }}</td><td class="px-6 py-2 capitalize">{{ $o->status }}</td><td class="px-6 py-2">Rs {{ number_format($o->total_amount, 2) }}</td></tr>
@empty<tr><td colspan="4" class="px-6 py-6 text-center text-slate-400">No orders linked yet.</td></tr>@endforelse
</tbody></table><div class="px-6 py-3">{{ $orders->links() }}</div></div>
</div>
@endsection
