@extends('superadmin.layout')

@section('title', 'Plans & Pricing')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h1 style="font-size:22px; font-weight:800;">Plans & Pricing</h1>
        <p style="font-size:13px; color:var(--muted); margin-top:3px;">Manage subscription plans and feature sets</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Plan
    </a>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
    @forelse($plans as $plan)
    <div class="card" style="position:relative; overflow:hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,var(--accent),var(--accent2));"></div>
        <div style="margin-bottom:16px;">
            <div style="font-size:18px; font-weight:800;">{{ $plan->name }}</div>
            <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $plan->description ?? 'Subscription plan' }}</div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px;">
            <div style="background:rgba(255,255,255,.04); border-radius:8px; padding:10px; text-align:center;">
                <div style="font-size:18px; font-weight:800; color:var(--accent2);">${{ $plan->price_monthly ?? '0' }}</div>
                <div style="font-size:10px; color:var(--muted);">per month</div>
            </div>
            <div style="background:rgba(255,255,255,.04); border-radius:8px; padding:10px; text-align:center;">
                <div style="font-size:18px; font-weight:800; color:#4ade80;">${{ $plan->price_yearly ?? '0' }}</div>
                <div style="font-size:10px; color:var(--muted);">per year</div>
            </div>
        </div>

        <div style="font-size:12px; color:var(--muted); margin-bottom:10px;">
            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid var(--border);">
                <span>Max Branches</span><strong style="color:var(--text);">{{ $plan->max_branches ?? '∞' }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid var(--border);">
                <span>Max Staff</span><strong style="color:var(--text);">{{ $plan->max_users ?? '∞' }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid var(--border);">
                <span>Max Tables</span><strong style="color:var(--text);">{{ $plan->max_tables ?? '∞' }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0;">
                <span>Storage</span><strong style="color:var(--text);">{{ $plan->max_storage_mb ?? 1024 }} MB</strong>
            </div>
        </div>

        @if($plan->features)
        <div style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom:16px;">
            @foreach(is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? [] as $feat)
                <span class="badge badge-purple" style="font-size:10px;">{{ $feat }}</span>
            @endforeach
        </div>
        @endif

        <div style="display:flex; gap:8px;">
            <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-secondary btn-sm" style="flex:1; justify-content:center;">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST"
                  onsubmit="return confirm('Delete this plan?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1; text-align:center; padding:60px;">
        <i class="fa-solid fa-layer-group" style="font-size:40px; color:var(--muted); opacity:.3; display:block; margin-bottom:16px;"></i>
        <p style="color:var(--muted);">No plans yet. <a href="{{ route('superadmin.plans.create') }}" style="color:var(--accent2);">Create your first plan →</a></p>
    </div>
    @endforelse
</div>

@endsection
