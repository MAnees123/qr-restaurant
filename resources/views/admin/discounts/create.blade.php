@extends('layouts.admin')

@section('header', 'Create Coupon')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-[2rem] shadow-sm border p-8">
        <form action="{{ route('admin.discounts.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Coupon Code</label>
                <input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold uppercase tracking-widest" placeholder="E.G. SAVE20">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Type</label>
                    <select name="type" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (Rs)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Value</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value') }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                </div>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Usage Limit</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold" placeholder="Unlimited if empty">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Starts At</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Expires At</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-emerald-200 uppercase tracking-[0.2em]">Create Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection
