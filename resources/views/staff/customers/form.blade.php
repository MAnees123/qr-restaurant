@extends('layouts.admin')
@section('title', 'Customer')
@section('header', $customer->exists ? 'Edit Customer' : 'New Customer')
@section('content')
<div class="max-w-2xl mx-auto">@include('staff._errors')
<div class="bg-white rounded-2xl shadow-sm border p-8">
<form action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}" method="POST" class="space-y-6">
@csrf @if($customer->exists) @method('PUT') @endif
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Name</label><input name="name" value="{{ old('name', $customer->name) }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Phone</label><input name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Email</label><input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tags (comma separated)</label><input name="tags" value="{{ old('tags', $customer->tags) }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold" placeholder="vip, regular"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Notes</label><textarea name="notes" rows="3" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">{{ old('notes', $customer->notes) }}</textarea></div>
<button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-widest">Save Customer</button>
</form></div></div>
@endsection
