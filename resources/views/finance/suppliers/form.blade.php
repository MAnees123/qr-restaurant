@extends('layouts.admin')
@section('title', $supplier->exists ? 'Edit Supplier' : 'New Supplier')
@section('header', $supplier->exists ? 'Edit Supplier' : 'New Supplier')
@section('content')
@php $in = 'w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold'; $lb = 'block text-sm font-black text-slate-700 uppercase tracking-widest mb-2'; @endphp
<div class="max-w-2xl mx-auto">
    @include('finance.partials.errors')
    <div class="bg-white rounded-[2rem] shadow-sm border p-8">
        <form action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}" method="POST" class="space-y-5">
            @csrf @if($supplier->exists) @method('PUT') @endif
            <div><label class="{{ $lb }}">Name</label><input name="name" required value="{{ old('name', $supplier->name) }}" class="{{ $in }}"></div>
            <div class="grid sm:grid-cols-2 gap-5">
                <div><label class="{{ $lb }}">Contact person</label><input name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="{{ $in }}"></div>
                <div><label class="{{ $lb }}">Phone</label><input name="phone" value="{{ old('phone', $supplier->phone) }}" class="{{ $in }}"></div>
                <div><label class="{{ $lb }}">Email</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="{{ $in }}"></div>
                <div><label class="{{ $lb }}">Payment terms</label><input name="payment_terms" placeholder="e.g. Net 15" value="{{ old('payment_terms', $supplier->payment_terms) }}" class="{{ $in }}"></div>
            </div>
            <div><label class="{{ $lb }}">Address</label><textarea name="address" rows="2" class="{{ $in }}">{{ old('address', $supplier->address) }}</textarea></div>
            <div><label class="{{ $lb }}">Opening balance owed (Rs)</label><input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', $supplier->opening_balance ?? 0) }}" class="{{ $in }}"></div>
            <div><label class="{{ $lb }}">Notes</label><textarea name="notes" rows="2" class="{{ $in }}">{{ old('notes', $supplier->notes) }}</textarea></div>
            <label class="flex items-center gap-2 font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true)) class="rounded"> Active</label>
            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-[0.2em]">Save Supplier</button>
        </form>
    </div>
</div>
@endsection
