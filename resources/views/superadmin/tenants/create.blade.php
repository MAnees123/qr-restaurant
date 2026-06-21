@extends('superadmin.layout')

@section('title', 'Add Restaurant')

@section('content')

<div style="margin-bottom:24px;">
    <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:12px;">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <h1 style="font-size:22px; font-weight:800;">Add New Restaurant</h1>
    <p style="font-size:13px; color:var(--muted); margin-top:3px;">Create a new tenant account with subscription and features</p>
</div>

<form action="{{ route('superadmin.tenants.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">

        {{-- ── Left Column ──────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Basic Info --}}
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-info-circle" style="color:var(--accent2);margin-right:8px;"></i>Basic Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Restaurant Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Owner Name *</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="form-control {{ $errors->has('owner_name') ? 'is-invalid' : '' }}" required>
                        @error('owner_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-control">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ old('timezone') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="PKR" {{ old('currency') == 'PKR' ? 'selected' : '' }}>PKR - Pakistani Rupee</option>
                            <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                            <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Business Settings --}}
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-building" style="color:var(--accent2);margin-right:8px;"></i>Business Settings</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Restaurant Logo</label>
                        <input type="file" name="logo" accept="image/*" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Theme</label>
                        <select name="theme" class="form-control">
                            <option value="default">Default</option>
                            <option value="dark">Dark</option>
                            <option value="modern">Modern</option>
                            <option value="classic">Classic</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Branch Limit</label>
                        <input type="number" name="max_branches" value="{{ old('max_branches', 1) }}" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Staff Limit</label>
                        <input type="number" name="max_users" value="{{ old('max_users', 10) }}" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Table Limit</label>
                        <input type="number" name="max_tables" value="{{ old('max_tables', 20) }}" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Storage Limit (MB)</label>
                        <input type="number" name="max_storage_mb" value="{{ old('max_storage_mb', 1024) }}" class="form-control" min="100">
                    </div>
                </div>
            </div>

            {{-- Subscription --}}
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-credit-card" style="color:var(--accent2);margin-right:8px;"></i>Subscription</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Plan *</label>
                        <select name="plan_id" class="form-control" required>
                            <option value="">— Select Plan —</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} ({{ $plan->billing_cycle ?? 'Custom' }})
                                </option>
                            @endforeach
                        </select>
                        @error('plan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Billing Cycle</label>
                        <select name="billing_cycle" class="form-control">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trial Days</label>
                        <input type="number" name="trial_days" value="{{ old('trial_days', 14) }}" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="trial">Trial</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right Column – Feature Toggles ──────── --}}
        <div class="card" style="position:sticky; top:80px;">
            <div class="card-header"><i class="fa-solid fa-toggle-on" style="color:var(--accent2);margin-right:8px;"></i>Feature Access</div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:16px;">Choose which modules this restaurant can access.</p>

            @foreach(\App\Services\FeatureRegistry::all() as $category => $features)
                <div style="margin-bottom:14px;">
                    <div style="font-size:10px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:8px;">{{ $category }}</div>
                    @foreach($features as $feature)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:6px 0; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;">{{ $feature['label'] }}</span>
                        <label class="toggle">
                            <input type="checkbox" name="granted_features[]" value="{{ $feature['code'] }}"
                                {{ in_array($feature['code'], old('granted_features', \App\Services\FeatureRegistry::freeFeatures())) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>

    {{-- Submit --}}
    <div style="margin-top:24px; display:flex; gap:10px;">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-check"></i> Create Restaurant
        </button>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>

@endsection
