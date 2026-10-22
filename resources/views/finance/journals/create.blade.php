@extends('layouts.admin')
@section('title', 'New Journal Entry')
@section('header', 'New Journal Entry')
@section('content')
@php $in = 'input'; $old = old('lines', [['account_id'=>'','debit'=>'','credit'=>''],['account_id'=>'','debit'=>'','credit'=>'']]); @endphp
<div class="max-w-3xl mx-auto" x-data="{ lines: @js(array_values($old)), get dr(){return this.lines.reduce((a,l)=>a+(parseFloat(l.debit)||0),0)}, get cr(){return this.lines.reduce((a,l)=>a+(parseFloat(l.credit)||0),0)} }">
    @include('finance.partials.errors')
    @if($accounts->isEmpty())<div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm">Create accounts first in the Chart of Accounts.</div>@endif
    <div class="card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.journals.store') }}" class="space-y-4">@csrf
            <div class="grid sm:grid-cols-3 gap-4"><input type="date" name="entry_date" required value="{{ old('entry_date', now()->toDateString()) }}" class="{{ $in }}"><input name="memo" required placeholder="Memo" value="{{ old('memo') }}" class="{{ $in }} sm:col-span-2"></div>
            <template x-for="(l, i) in lines" :key="i">
                <div class="grid grid-cols-12 gap-2">
                    <select class="col-span-12 sm:col-span-6 {{ $in }}" :name="`lines[${i}][account_id]`" x-model="l.account_id" required><option value="">Account...</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select>
                    <input class="col-span-6 sm:col-span-3 {{ $in }}" type="number" step="0.01" min="0" placeholder="Debit" :name="`lines[${i}][debit]`" x-model="l.debit">
                    <input class="col-span-6 sm:col-span-3 {{ $in }}" type="number" step="0.01" min="0" placeholder="Credit" :name="`lines[${i}][credit]`" x-model="l.credit">
                </div>
            </template>
            <button type="button" class="btn btn-sm btn-secondary" @click="lines.push({account_id:'',debit:'',credit:''})">+ Add line</button>
            <div class="flex flex-wrap justify-end gap-6 font-semibold text-sm border-t border-slate-100 pt-4"><span>Debit <span x-text="dr.toFixed(2)"></span></span><span>Credit <span x-text="cr.toFixed(2)"></span></span><span :class="Math.abs(dr-cr)<0.005 ? 'text-emerald-600':'text-red-600'" x-text="Math.abs(dr-cr)<0.005 ? 'Balanced':'Not balanced'"></span></div>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button class="btn btn-primary">Post Entry</button>
                <a href="{{ route('admin.journals.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
