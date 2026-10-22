@extends('layouts.admin')
@section('title', 'New Purchase Order')
@section('header', 'New Purchase Order')
@section('content')
@php $in = 'input'; $lb = 'block text-sm font-medium text-slate-700 mb-1.5';
$oldItems = old('items', [['description' => '', 'quantity' => 1, 'unit_cost' => 0]]); @endphp
<div class="max-w-3xl mx-auto" x-data="{ items: @js(array_values($oldItems)), get total(){ return this.items.reduce((a,i)=>a+(parseFloat(i.quantity)||0)*(parseFloat(i.unit_cost)||0),0) } }">
    @include('finance.partials.errors')
    @if($suppliers->isEmpty())<div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm">Add an active supplier first (Suppliers module).</div>@endif
    <div class="card p-6 sm:p-8">
        <form action="{{ route('admin.purchases.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid sm:grid-cols-2 gap-6">
                <div><label class="{{ $lb }}">Supplier</label>
                    <select name="supplier_id" required class="{{ $in }}"><option value="">Select...</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>@endforeach</select></div>
                <div><label class="{{ $lb }}">Order date</label><input type="date" name="order_date" required value="{{ old('order_date', now()->toDateString()) }}" class="{{ $in }}"></div>
            </div>
            <div>
                <label class="{{ $lb }}">Items</label>
                <template x-for="(item, i) in items" :key="i">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <input class="col-span-12 sm:col-span-6 {{ $in }}" :name="`items[${i}][description]`" x-model="item.description" placeholder="Description" required>
                        <input class="col-span-4 sm:col-span-2 {{ $in }}" type="number" step="0.001" min="0.001" :name="`items[${i}][quantity]`" x-model="item.quantity" required>
                        <input class="col-span-6 sm:col-span-3 {{ $in }}" type="number" step="0.01" min="0" :name="`items[${i}][unit_cost]`" x-model="item.unit_cost" required>
                        <button type="button" class="col-span-2 sm:col-span-1 btn btn-sm btn-ghost text-red-600" @click="items.length > 1 && items.splice(i,1)">&times;</button>
                    </div>
                </template>
                <button type="button" class="btn btn-sm btn-secondary mt-1" @click="items.push({description:'',quantity:1,unit_cost:0})">+ Add line</button>
                <p class="text-xs text-slate-400 mt-2">Columns: description, quantity, unit cost.</p>
            </div>
            <div class="text-right text-xl font-bold border-t border-slate-100 pt-4">Total: Rs <span x-text="total.toFixed(2)"></span></div>
            <div><label class="{{ $lb }}">Notes</label><textarea name="notes" rows="2" class="{{ $in }}">{{ old('notes') }}</textarea></div>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button class="btn btn-primary">Create Purchase Order</button>
                <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
