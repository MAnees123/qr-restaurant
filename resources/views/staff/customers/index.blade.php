@extends('layouts.admin')
@section('title', 'Customers')
@section('header', 'Customers')
@section('content')
<div class="flex flex-wrap gap-3 justify-between mb-6">
<form method="GET" class="flex gap-2"><input name="q" value="{{ $q }}" placeholder="Search name, phone, email" class="rounded-xl border-slate-200 font-bold"><input name="tag" value="{{ $tag }}" placeholder="Tag" class="rounded-xl border-slate-200 font-bold w-28"><button class="bg-slate-800 text-white font-bold px-4 rounded-xl">Filter</button></form>
<a href="{{ route('admin.customers.create') }}" class="bg-emerald-600 text-white font-bold py-2 px-6 rounded-xl">New Customer</a></div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto"><table class="w-full text-left text-sm">
<thead><tr class="bg-slate-50/50 border-b text-xs font-black text-slate-400 uppercase"><th class="px-6 py-3">Name</th><th class="px-6 py-3">Phone</th><th class="px-6 py-3">Tags</th><th class="px-6 py-3">Orders</th><th class="px-6 py-3">Spent</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($customers as $c)
<tr><td class="px-6 py-3 font-black text-slate-800">{{ $c->name }}<div class="text-xs font-medium text-slate-400">{{ $c->email }}</div></td><td class="px-6 py-3">{{ $c->phone ?? '-' }}</td>
<td class="px-6 py-3">@foreach($c->tagList() as $t)<span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-1 rounded-md mr-1">{{ $t }}</span>@endforeach</td>
<td class="px-6 py-3">{{ $c->orders_count }}</td><td class="px-6 py-3 font-bold">Rs {{ number_format($c->spent ?? 0, 2) }}</td>
<td class="px-6 py-3 text-right space-x-2"><a href="{{ route('admin.customers.show', $c) }}" class="text-blue-500 font-black">View</a><a href="{{ route('admin.customers.edit', $c) }}" class="text-slate-500 font-black">Edit</a>
<form action="{{ route('admin.customers.destroy', $c) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-500 font-black" data-confirm="Delete this customer? Their orders are kept.">Delete</button></form></td></tr>
@empty<tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No customers found.</td></tr>@endforelse
</tbody></table><div class="px-6 py-4">{{ $customers->links() }}</div></div>
@endsection
