@extends('layouts.admin')
@section('title', $expense->exists ? 'Edit Expense' : 'New Expense')
@section('header', $expense->exists ? 'Edit Expense' : 'New Expense')
@section('content')
@php $in = 'w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold'; $lb = 'block text-sm font-black text-slate-700 uppercase tracking-widest mb-2'; @endphp
<div class="max-w-2xl mx-auto">
    @include('finance.partials.errors')
    <div class="bg-white rounded-[2rem] shadow-sm border p-8">
        <form action="{{ $expense->exists ? route('admin.expenses.update', $expense) : route('admin.expenses.store') }}" method="POST" class="space-y-5">
            @csrf @if($expense->exists) @method('PUT') @endif
            <div class="grid sm:grid-cols-2 gap-5">
                <div><label class="{{ $lb }}">Date</label><input type="date" name="expense_date" required value="{{ old('expense_date', optional($expense->expense_date)->toDateString()) }}" class="{{ $in }}"></div>
                <div><label class="{{ $lb }}">Amount (Rs)</label><input type="number" step="0.01" min="0.01" name="amount" required value="{{ old('amount', $expense->amount) }}" class="{{ $in }}"></div>
                <div><label class="{{ $lb }}">Category</label><select name="expense_category_id" class="{{ $in }}"><option value="">Uncategorised</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('expense_category_id', $expense->expense_category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
                <div><label class="{{ $lb }}">Vendor / supplier</label><select name="supplier_id" class="{{ $in }}"><option value="">None</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $expense->supplier_id) == $s->id)>{{ $s->name }}</option>@endforeach</select></div>
            </div>
            <div><label class="{{ $lb }}">Reference</label><input name="reference" value="{{ old('reference', $expense->reference) }}" class="{{ $in }}"></div>
            <div><label class="{{ $lb }}">Notes</label><textarea name="notes" rows="2" class="{{ $in }}">{{ old('notes', $expense->notes) }}</textarea></div>
            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-[0.2em]">Save Expense</button>
        </form>
    </div>
</div>
@endsection
