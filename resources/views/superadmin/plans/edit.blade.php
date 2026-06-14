@extends('superadmin.layout')
@section('title', 'Edit Subscription Plan: ' . $plan->name)

@section('content')
<form method="POST" action="{{ route('superadmin.plans.update', $plan) }}" class="space-y-6 max-w-5xl">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Fields -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Plan Details -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Plan Basic Info</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Plan Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $plan->name) }}" placeholder="e.g. Premium Tier"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trial Days *</label>
                        <input type="number" name="trial_days" required min="0" value="{{ old('trial_days', $plan->trial_days) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Price Monthly (USD) *</label>
                        <input type="number" name="price_monthly" required min="0" step="0.01" value="{{ old('price_monthly', $plan->price_monthly) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Price Yearly (USD) *</label>
                        <input type="number" name="price_yearly" required min="0" step="0.01" value="{{ old('price_yearly', $plan->price_yearly) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Plan Limits -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Plan Limits (-1 for Unlimited)</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Branch Limit</label>
                        <input type="number" name="max_branches" required min="-1" value="{{ old('max_branches', $plan->max_branches) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Staff Limit</label>
                        <input type="number" name="max_users" required min="-1" value="{{ old('max_users', $plan->max_users) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Table Limit</label>
                        <input type="number" name="max_tables" required min="-1" value="{{ old('max_tables', $plan->max_tables) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Storage (MB)</label>
                        <input type="number" name="max_storage_mb" required min="10" value="{{ old('max_storage_mb', $plan->max_storage_mb ?? 100) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Features & Modules -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Granted Features & Modules</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    @foreach($features as $category => $items)
                        <div>
                            <h4 class="text-xs font-bold text-indigo-600 mb-2 uppercase tracking-wide">{{ $category }}</h4>
                            <div class="space-y-2">
                                @foreach($items as $f)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="features[]" value="{{ $f['code'] }}"
                                               {{ in_array($f['code'], $plan->features ?? []) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-slate-600">{{ $f['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Plan Status</h3>
                
                <label class="flex items-center gap-2 cursor-pointer pt-2">
                    <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700 font-medium">Publish Plan (Active)</span>
                </label>
                <p class="text-xs text-slate-400">Inactive plans cannot be selected by restaurants or super admins during creation.</p>
            </div>

            <div class="bg-slate-900 rounded-2xl p-6 text-white space-y-4">
                <h3 class="font-bold text-md">Update Subscription Plan</h3>
                <p class="text-xs text-slate-400">Updating this plan will affect all restaurants currently assigned to it.</p>
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold rounded-xl text-center transition-colors shadow-lg">
                        Update Plan
                    </button>
                </div>
                <a href="{{ route('superadmin.plans.index') }}" class="block text-center text-xs text-slate-400 hover:text-white underline">
                    Cancel & Go Back
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
