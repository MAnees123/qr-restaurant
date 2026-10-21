@extends('layouts.admin')
@section('title', 'New Journal Entry')
@section('header', 'New Journal Entry')
@section('content')
@php $in = 'w-full rounded-xl border-slate-200 font-bold text-sm'; $old = old('lines', [['account_id'=>'','debit'=>'','credit'=>''],['account_id'=>'','debit'=>'','credit'=>'']]); @endphp
<div class="max-w-3xl mx-auto" x-data="{ lines: @js(array_values($old)), get dr(){return this.lines.reduce((a,l)=>a+(parseFloat(l.debit)||0),0)}, get cr(){return this.lines.reduce((a,l)=>a+(parseFloat(l.credit)||0),0)} }">
    @include('finance.partials.errors')
    @if($accounts->isEmpty())<div class="mb-4 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm">Create accounts first in the Chart of Accounts.</div>@endif
    <div class="bg-white rounded-[2rem] border shadow-sm p-8">
        <form method="POST" action="{{ route('admin.journals.store') }}" class="space-y-4">@csrf
            <div class="grid sm:grid-cols-3 gap-4"><input type="date" name="entry_date" required value="{{ old('entry_date', now()->toDateString()) }}" class="{{ $in }}"><input name="memo" required placeholder="Memo" value="{{ old('memo') }}" class="{{ $in }} sm:col-span-2"></div>
            <template x-for="(l, i) in lines" :key="i">
                <div class="grid grid-cols-12 gap-2">
                    <select class="col-span-6 {{ $in }}" :name="`lines[${i}][account_id]`" x-model="l.account_id" required><option value="">Account...</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select>
                    <input class="col-span-3 {{ $in }}" type="number" step="0.01" min="0" placeholder="Debit" :name="`lines[${i}][debit]`" x-model="l.debit">
                    <input class="col-span-3 {{ $in }}" type="number" step="0.01" min="0" placeholder="Credit" :name="`lines[${i}][credit]`" x-model="l.credit">
                </div>
            </template>
            <button type="button" class="text-sm font-black text-emerald-600" @click="lines.push({account_id:'',debit:'',credit:''})">+ Add line</button>
            <div class="flex justify-end gap-6 font-black text-sm"><span>Debit <span x-text="dr.toFixed(2)"></span></span><span>Credit <span x-text="cr.toFixed(2)"></span></span><span :class="Math.abs(dr-cr)<0.005 ? 'text-emerald-600':'text-red-600'" x-text="Math.abs(dr-cr)<0.005 ? 'Balanced':'Not balanced'"></span></div>
            <button class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl uppercase tracking-[0.2em]">Post Entry</button>
        </form>
    </div>
</div>
@endsection
