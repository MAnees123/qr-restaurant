@extends('superadmin.layout')

@section('title', 'Analytics')

@section('content')

<div style="margin-bottom:24px;">
    <h1 style="font-size:22px; font-weight:800;">Analytics</h1>
    <p style="font-size:13px; color:var(--muted); margin-top:3px;">System-wide performance metrics and trends</p>
</div>

{{-- ── Revenue Chart ────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px;">

    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-chart-area" style="color:var(--accent2); margin-right:8px;"></i>Monthly Revenue (Last 6 Months)</span>
        </div>
        <div style="display:flex; align-items:flex-end; gap:12px; height:200px; padding-top:10px;">
            @php $maxRevenue = max(array_column($revenueChart, 'value')) ?: 1; @endphp
            @foreach($revenueChart as $item)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;">
                <span style="font-size:11px; font-weight:700; color:var(--accent2);">${{ number_format($item['value']) }}</span>
                <div style="width:100%; background:linear-gradient(180deg, var(--accent), var(--accent2)); border-radius:6px 6px 0 0; min-height:4px; transition:height .5s;"
                     class="bar-animate"
                     data-height="{{ ($item['value'] / $maxRevenue) * 160 }}">
                </div>
                <span style="font-size:10px; color:var(--muted);">{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Orders by Status --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-pie-chart" style="color:var(--accent2); margin-right:8px;"></i>Orders by Status</span>
        </div>
        @if(count($ordersByStatus) > 0)
        <div style="display:flex; flex-direction:column; gap:10px;">
            @php
                $statusColors = [
                    'pending' => ['#f59e0b', 'rgba(245,158,11,.15)'],
                    'confirmed' => ['#6c63ff', 'rgba(108,99,255,.15)'],
                    'preparing' => ['#3b82f6', 'rgba(59,130,246,.15)'],
                    'ready' => ['#22c55e', 'rgba(34,197,94,.15)'],
                    'completed' => ['#10b981', 'rgba(16,185,129,.15)'],
                    'cancelled' => ['#ef4444', 'rgba(239,68,68,.15)'],
                ];
                $totalOrders = array_sum($ordersByStatus) ?: 1;
            @endphp
            @foreach($ordersByStatus as $status => $count)
            @php $color = $statusColors[$status] ?? ['var(--muted)', 'rgba(148,163,184,.15)']; @endphp
            <div>
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                    <span style="text-transform:capitalize; font-weight:600;">{{ $status }}</span>
                    <span style="color:var(--muted);">{{ $count }}</span>
                </div>
                <div style="height:8px; background:{{ $color[1] }}; border-radius:10px; overflow:hidden;">
                    <div style="height:100%; width:{{ ($count / $totalOrders) * 100 }}%; background:{{ $color[0] }}; border-radius:10px; transition:width .8s;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state" style="padding:30px;">
            <i class="fa-solid fa-chart-pie"></i>
            <p>No order data available.</p>
        </div>
        @endif
    </div>

</div>

{{-- ── Restaurant Growth + Top Restaurants ──────────────── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Growth --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-arrow-trend-up" style="color:#22c55e; margin-right:8px;"></i>Restaurant Growth</span>
        </div>
        <div style="display:flex; align-items:flex-end; gap:12px; height:180px; padding-top:10px;">
            @php $maxGrowth = max(array_column($growthChart, 'value')) ?: 1; @endphp
            @foreach($growthChart as $item)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;">
                <span style="font-size:12px; font-weight:700; color:#22c55e;">{{ $item['value'] }}</span>
                <div style="width:100%; background:linear-gradient(180deg, #22c55e, #16a34a); border-radius:6px 6px 0 0; min-height:4px; transition:height .5s;"
                     class="bar-animate"
                     data-height="{{ ($item['value'] / $maxGrowth) * 140 }}">
                </div>
                <span style="font-size:10px; color:var(--muted);">{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Restaurants --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-trophy" style="color:#f59e0b; margin-right:8px;"></i>Top Restaurants by Orders</span>
        </div>
        @if(count($topRestaurants) > 0)
        <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($topRestaurants as $index => $restaurant)
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:8px; background:{{ $index === 0 ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'rgba(108,99,255,.12)' }}; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:{{ $index === 0 ? '#fff' : 'var(--accent2)' }};">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:13px;">{{ $restaurant->name }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $restaurant->orders_count }} orders</div>
                </div>
                <span class="badge badge-purple">{{ $restaurant->orders_count }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state" style="padding:30px;">
            <i class="fa-solid fa-trophy"></i>
            <p>No restaurant data yet.</p>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Animate chart bars on page load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.bar-animate').forEach(bar => {
            const h = bar.getAttribute('data-height');
            setTimeout(() => {
                bar.style.height = h + 'px';
            }, 100);
        });
    });
</script>
@endpush
