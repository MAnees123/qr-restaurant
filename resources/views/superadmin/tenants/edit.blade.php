@extends('superadmin.layout')
@section('title', 'Edit Tenant: ' . $restaurant->name)

@section('content')
<form method="POST" action="{{ route('superadmin.tenants.update', $restaurant) }}" enctype="multipart/form-data" class="space-y-6 max-w-5xl">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Fields -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Basic Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Restaurant Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $restaurant->name) }}" placeholder="e.g. Gourmet Palace"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cuisine Type</label>
                        <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" placeholder="e.g. Italian, Fine Dining"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Owner Name</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $restaurant->owner_name) }}" placeholder="e.g. John Doe"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" placeholder="e.g. +1 (555) 000-0000"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Country</label>
                        <input type="text" name="country" value="{{ old('country', $restaurant->country) }}" placeholder="e.g. United States"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $restaurant->city) }}" placeholder="e.g. New York"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $restaurant->currency) }}" placeholder="e.g. USD, EUR, PKR"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Timezone</label>
                        <select name="timezone" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ $restaurant->timezone === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Business Settings & Limits -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Business Limits & Customization</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Restaurant Logo</label>
                        @if($restaurant->logo)
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-12 h-12 rounded-xl object-cover">
                                <span class="text-xs text-slate-400">Current Logo</span>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Theme Customization</label>
                        <select name="theme" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500">
                            <option value="default" {{ $restaurant->theme === 'default' ? 'selected' : '' }}>Default Dark-Orange Theme</option>
                            <option value="modern" {{ $restaurant->theme === 'modern' ? 'selected' : '' }}>Modern Glassmorphic Theme</option>
                            <option value="classic" {{ $restaurant->theme === 'classic' ? 'selected' : '' }}>Classic Minimalist Theme</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Branch Limit</label>
                        <input type="number" name="max_branches" min="1" value="{{ old('max_branches', $restaurant->max_branches) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Staff Limit</label>
                        <input type="number" name="max_users" min="1" value="{{ old('max_users', $restaurant->max_users) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Table Limit</label>
                        <input type="number" name="max_tables" min="1" value="{{ old('max_tables', $restaurant->max_tables) }}"
                               class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Storage (MB)</label>
                        <input type="number" name="max_storage_mb" min="10" value="{{ old('max_storage_mb', $restaurant->max_storage_mb ?? 100) }}"
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
                                        <input type="checkbox" name="granted_features[]" value="{{ $f['code'] }}"
                                               {{ in_array($f['code'], $restaurant->granted_features ?? []) ? 'checked' : '' }}
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

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <!-- Subscription & Plan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3">Subscription Details</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Subscription Plan</label>
                    <select name="plan_id" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500">
                        <option value="">Custom Plan (No Package)</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $restaurant->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Billing Cycle</label>
                    <select name="billing_cycle" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500">
                        <option value="monthly" {{ $restaurant->billing_cycle === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $restaurant->billing_cycle === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="lifetime" {{ $restaurant->billing_cycle === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500">
                        <option value="trial" {{ $restaurant->payment_status === 'trial' ? 'selected' : '' }}>Trial Account</option>
                        <option value="paid" {{ $restaurant->payment_status === 'paid' ? 'selected' : '' }}>Paid & Active</option>
                        <option value="unpaid" {{ $restaurant->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid / Expired</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Subscription Expiry Date</label>
                    <input type="date" name="subscription_ends_at" 
                           value="{{ $restaurant->subscription_ends_at ? $restaurant->subscription_ends_at->format('Y-m-d') : '' }}"
                           class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Submit Panel -->
            <div class="bg-slate-900 rounded-2xl p-6 text-white space-y-4">
                <h3 class="font-bold text-md">Save Changes?</h3>
                <p class="text-xs text-slate-400">Updating this account will take effect immediately for the restaurant dashboard.</p>
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold rounded-xl text-center transition-colors shadow-lg">
                        Update Account
                    </button>
                </div>
                <a href="{{ route('superadmin.tenants.show', $restaurant) }}" class="block text-center text-xs text-slate-400 hover:text-white underline">
                    Cancel & Go Back
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
