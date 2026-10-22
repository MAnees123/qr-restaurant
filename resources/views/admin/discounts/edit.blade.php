@extends('layouts.admin')

@section('header', 'Edit Coupon: ' . $discount->code)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-6 sm:p-8">
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Coupon Code</label>
                <input type="text" name="code" value="{{ old('code', $discount->code) }}" required class="input w-full font-semibold uppercase tracking-widest">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                    <select name="type" required class="input w-full">
                        <option value="percentage" {{ $discount->type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ $discount->type === 'fixed' ? 'selected' : '' }}>Fixed Amount (Rs)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Value</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value', $discount->value) }}" required class="input w-full">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Usage Limit</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" class="input w-full">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Starts At</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $discount->starts_at) }}" class="input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Expires At</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', $discount->expires_at) }}" class="input w-full">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection
