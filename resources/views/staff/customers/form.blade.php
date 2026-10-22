@extends('layouts.admin')
@section('title', 'Customer')
@section('header', $customer->exists ? 'Edit Customer' : 'New Customer')
@section('content')
<div class="max-w-2xl mx-auto">@include('staff._errors')
<div class="card p-6 sm:p-8">
<form action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}" method="POST" class="space-y-6">
@csrf @if($customer->exists) @method('PUT') @endif
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label><input name="name" value="{{ old('name', $customer->name) }}" required class="input"></div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label><input name="phone" value="{{ old('phone', $customer->phone) }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label><input type="email" name="email" value="{{ old('email', $customer->email) }}" class="input"></div></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Tags (comma separated)</label><input name="tags" value="{{ old('tags', $customer->tags) }}" class="input" placeholder="vip, regular"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label><textarea name="notes" rows="3" class="input">{{ old('notes', $customer->notes) }}</textarea></div>
<div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
<a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
<button class="btn btn-primary">Save Customer</button></div>
</form></div></div>
@endsection
