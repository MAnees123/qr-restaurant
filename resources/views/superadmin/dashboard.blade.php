@extends('superadmin.layout')

@section('title', 'Dashboard')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
    <div>
        <h1 style="font-size:22px; font-weight:800; color:var(--text);">Global Overview</h1>
        <p style="font-size:13px; color:var(--muted); margin-top:3px;">Real-time statistics across all restaurant tenants</p>
    </div>
    <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Restaurant
    </a>
</div>

{{-- ── Stat Cards ───────────────────────────────────────────── --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(108,99,255,.15);">
            <i class="fa-solid fa-store" style="color:var(--accent2);"></i>
        </div>
        <div class="stat-value">{{ $stats['total_restaurants'] }}</div>
        <div class="stat-label">Total Restaurants</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);">
            <i class="fa-solid fa-circle-check" style="color:#4ade80;"></i>
        </div>
        <div class="stat-value">{{ $stats['active_restaurants'] }}</div>
        <div class="stat-label">Active Restaurants</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,.15);">
            <i class="fa-solid fa-ban" style="color:#f87171;"></i>
        </div>
        <div class="stat-value">{{ $stats['suspended_restaurants'] }}</div>
        <div class="stat-label">Suspended</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);">
            <i class="fa-solid fa-triangle-exclamation" style="color:#fbbf24;"></i>
        </div>
        <div class="stat-value">{{ $stats['expired_subscriptions'] }}</div>
        <div class="stat-label">Expired Subscriptions</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);">
            <i class="fa-solid fa-dollar-sign" style="color:#4ade80;"></i>
        </div>
        <div class="stat-value">${{ number_format($stats['monthly_revenue'], 2) }}</div>
        <div class="stat-label">Monthly Revenue</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);">
            <i class="fa-solid fa-bag-shopping" style="color:#60a5fa;"></i>
        </div>
        <div class="stat-value">{{ $stats['active_orders'] }}</div>
        <div class="stat-label">Active Orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,.15);">
            <i class="fa-solid fa-qrcode" style="color:#c084fc;"></i>
        </div>
        <div class="stat-value">{{ $stats['qr_orders'] }}</div>
        <div class="stat-label">QR Orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(20,184,166,.15);">
            <i class="fa-solid fa-boxes-stacked" style="color:#2dd4bf;"></i>
        </div>
        <div class="stat-value">{{ $stats['inventory_items'] }}</div>
        <div class="stat-label">Inventory Items</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);">
            <i class="fa-solid fa-users" style="color:#fbbf24;"></i>
        </div>
        <div class="stat-value">{{ $stats['active_staff'] }}</div>
        <div class="stat-label">Active Staff</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,.15);">
            <i class="fa-solid fa-code-branch" style="color:#f87171;"></i>
        </div>
        <div class="stat-value">{{ $stats['total_branches'] }}</div>
        <div class="stat-label">Total Branches</div>
    </div>

</div>

{{-- ── Recent Restaurants ───────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <span><i class="fa-solid fa-store" style="color:var(--accent2); margin-right:8px;"></i>Recent Restaurants</span>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>

    @php
        $recent = \App\Models\Restaurant::latest()->limit(8)->get();
    @endphp

    @if($recent->isEmpty())
        <div style="text-align:center; padding:40px; color:var(--muted);">
            <i class="fa-solid fa-store" style="font-size:32px; margin-bottom:12px; opacity:.3;"></i>
            <p>No restaurants yet. <a href="{{ route('superadmin.tenants.create') }}" style="color:var(--accent2);">Add your first tenant →</a></p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Restaurant</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent as $r)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $r->name }}</div>
                            <div style="font-size:11px; color:var(--muted);">{{ $r->city ?? '' }}{{ $r->country ? ', '.$r->country : '' }}</div>
                        </td>
                        <td style="color:var(--muted);">{{ $r->email ?? '—' }}</td>
                        <td>
                            @if($r->subscription_plan)
                                <span class="badge badge-purple">{{ $r->subscription_plan }}</span>
                            @else
                                <span class="badge badge-muted">Free</span>
                            @endif
                        </td>
                        <td>
                            @if($r->is_suspended)
                                <span class="badge badge-danger">Suspended</span>
                            @elseif($r->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </td>
                        <td style="color:var(--muted); font-size:12px;">{{ $r->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('superadmin.tenants.show', $r) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.tenants.edit', $r) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── Quick Actions ────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-top:20px;">
    <div class="card" style="padding:20px;">
        <div class="card-header" style="margin-bottom:12px;">
            <span><i class="fa-solid fa-bolt" style="color:var(--warning); margin-right:8px;"></i>Quick Actions</span>
        </div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add New Restaurant
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-layer-group"></i> Manage Plans
            </a>
            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-list"></i> View All Tenants
            </a>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <div class="card-header" style="margin-bottom:12px;">
            <span><i class="fa-solid fa-circle-info" style="color:var(--accent2); margin-right:8px;"></i>System Info</span>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; justify-content:space-between; font-size:13px;">
                <span style="color:var(--muted);">Laravel Version</span>
                <span>{{ app()->version() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13px;">
                <span style="color:var(--muted);">PHP Version</span>
                <span>{{ phpversion() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13px;">
                <span style="color:var(--muted);">Environment</span>
                <span class="badge {{ app()->environment('production') ? 'badge-success' : 'badge-warning' }}">
                    {{ app()->environment() }}
                </span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13px;">
                <span style="color:var(--muted);">Server Time</span>
                <span>{{ now()->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
