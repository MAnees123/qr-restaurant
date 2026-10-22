@extends('layouts.admin')
@section('title', 'Customers')
@section('header', 'Customers')
@section('content')
<div class="space-y-6">
<div class="flex flex-wrap gap-3 justify-between">
<form method="GET" class="flex flex-wrap gap-2"><input name="q" value="{{ $q }}" placeholder="Search name, phone, email" class="input !w-auto"><input name="tag" value="{{ $tag }}" placeholder="Tag" class="input !w-28"><button class="btn btn-secondary">Filter</button></form>
<a href="{{ route('admin.customers.create') }}" class="btn btn-primary">New Customer</a></div>
<div class="card overflow-x-auto"><table class="w-full text-left text-sm">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Name</th><th class="px-6 py-4">Phone</th><th class="px-6 py-4">Tags</th><th class="px-6 py-4 text-right">Orders</th><th class="px-6 py-4 text-right">Spent</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($customers as $c)
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold text-slate-800">{{ $c->name }}<div class="text-xs font-normal text-slate-400">{{ $c->email }}</div></td><td class="px-6 py-4">{{ $c->phone ?? '-' }}</td>
<td class="px-6 py-4">@foreach($c->tagList() as $t)<x-ui.badge type="warning" class="mr-1">{{ $t }}</x-ui.badge>@endforeach</td>
<td class="px-6 py-4 text-right font-semibold">{{ $c->orders_count }}</td><td class="px-6 py-4 text-right font-semibold">Rs {{ number_format($c->spent ?? 0, 2) }}</td>
<td class="px-6 py-4 text-right"><div class="flex justify-end items-center gap-2"><a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-secondary">View</a><a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-ghost">Edit</a>
<form action="{{ route('admin.customers.destroy', $c) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600" data-confirm="Delete this customer? Their orders are kept.">Delete</button></form></div></td></tr>
@empty<tr><td colspan="6"><x-ui.empty-state title="No customers found"/></td></tr>@endforelse
</tbody></table><div class="px-6 py-4">{{ $customers->links() }}</div></div>
</div>
@endsection
