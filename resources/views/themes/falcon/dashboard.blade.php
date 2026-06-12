@extends('themes.falcon.layout')

@push('styles')
<style>
/* Cards */
.card { background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 1px 8px rgba(0,0,0,0.03); overflow:hidden; }
.row1 { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:20px; }
.stat-card { padding:20px 20px 16px; position:relative; display:flex; flex-direction:column; justify-content:space-between; min-height:130px; }
.stat-label { font-size:12px; font-weight:500; color:#748194; margin-bottom:4px; display:flex; align-items:center; gap:6px; }
.stat-value { font-size:26px; font-weight:700; color:#344050; letter-spacing:-0.5px; line-height:1.1; margin-bottom:4px; }
.stat-change { font-size:11.5px; font-weight:600; color:#00d27a; }
.stat-chart { width:100px; height:50px; flex-shrink:0; margin-top:-10px; }
.market-card { padding:18px 20px; }
.market-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.market-title { font-size:13px; font-weight:600; color:#344050; }
.market-body { display:flex; align-items:center; gap:20px; }
.market-legend { display:flex; flex-direction:column; gap:8px; flex:1; }
.legend-item { display:flex; align-items:center; gap:8px; font-size:12px; color:#5e6e82; }
.legend-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.market-donut-wrap { width:90px; height:90px; flex-shrink:0; position:relative; display:flex; align-items:center; justify-content:center; }
.market-donut-center { position:absolute; font-size:14px; font-weight:700; color:#344050; pointer-events:none; }
.weather-card { padding:18px 20px; }
.weather-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.weather-title { font-size:13px; font-weight:600; color:#344050; }
.weather-body { display:flex; align-items:center; gap:14px; }
.weather-icon { font-size:38px; line-height:1; }
.weather-city { font-size:13px; font-weight:600; color:#344050; }
.weather-desc { font-size:12px; color:#f5803e; font-weight:500; margin-bottom:4px; }
.row2 { display:grid; grid-template-columns:1fr 490px; gap:20px; margin-bottom:20px; }
.sales-card { padding:18px 20px 14px; }
.sales-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.sales-title { font-size:14px; font-weight:600; color:#344050; }
.proj-card { padding:0; }
.proj-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid #eef0f5; }
.proj-title { font-size:14px; font-weight:600; color:#344050; }
.proj-col-label { font-size:11.5px; font-weight:600; color:#748194; text-transform:uppercase; letter-spacing:0.05em; }
.proj-table { width:100%; }
.proj-row { display:flex; align-items:center; padding:13px 20px; border-bottom:1px solid #f5f7fa; gap:0; cursor:pointer; }
.proj-row:hover { background: #f9fbfd; }
.proj-avatar { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff; flex-shrink:0; margin-right:12px; }
.proj-name { font-size:13px; font-weight:600; color:#344050; margin-right:8px; }
.proj-pct-badge { font-size:10.5px; font-weight:600; padding:2px 8px; border-radius:10px; background:#e8f4ff; color:#2c7be5; flex-shrink:0; }
.proj-time { margin-left:auto; font-size:12.5px; color:#344050; font-weight:500; min-width:80px; text-align:left; }
</style>
@endpush

@section('content')
<div class="row1">
    <div class="card stat-card" x-data style="cursor:pointer;" @click="window.location.href='{{ route('admin.revenue') }}'">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; width:100%;">
            <div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">PKR {{ number_format($stats['total_revenue']) }}</div>
                <div class="stat-change">Cash: {{ number_format($stats['cash_payments']) }}</div>
            </div>
            <div class="stat-chart">
                <svg viewBox="0 0 100 50" preserveAspectRatio="none" style="width:100%;height:100%;"><path d="M0,50 L0,30 C20,20 30,40 50,25 C70,10 80,35 100,5 L100,50 Z" fill="rgba(0,210,122,0.1)"/><path d="M0,30 C20,20 30,40 50,25 C70,10 80,35 100,5" fill="none" stroke="#00d27a" stroke-width="2"/></svg>
            </div>
        </div>
    </div>
    <div class="card stat-card" x-data style="cursor:pointer;" @click="window.location.href='{{ route('admin.orders.index') }}'">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; width:100%;">
            <div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-change text-[#f5803e]">{{ $stats['pending_orders'] }} pending</div>
            </div>
            <div class="stat-chart">
                <svg viewBox="0 0 100 50" preserveAspectRatio="none" style="width:100%;height:100%;"><path d="M0,50 L0,40 C20,30 30,20 50,35 C70,50 80,10 100,20 L100,50 Z" fill="rgba(245,128,62,0.1)"/><path d="M0,40 C20,30 30,20 50,35 C70,50 80,10 100,20" fill="none" stroke="#f5803e" stroke-width="2"/></svg>
            </div>
        </div>
    </div>
    <div class="card market-card" x-data style="cursor:pointer;" @click="window.location.href='{{ route('admin.analytics.index') }}'">
        <div class="market-top"><div class="market-title">Top 10 Products Sold</div></div>
        <div class="market-body" style="justify-content:center;">
            <div class="market-donut-wrap">
                <canvas id="marketDonut" width="90" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="card weather-card">
        <div class="weather-header"><div class="weather-title">Restaurant Status</div></div>
        <div class="weather-body">
            <div class="weather-icon">🔥</div>
            <div class="weather-info">
                <div class="weather-city">Kitchen</div>
                <div class="weather-desc">Active</div>
                <div class="text-[11.5px] text-[#748194]"><b>{{ $stats['active_tables'] }}</b> tables occupied</div>
            </div>
        </div>
    </div>
</div>

<div class="row2">
    <div class="card proj-card">
        <div class="proj-header">
            <span class="proj-title">Recent Orders</span>
            <span class="proj-col-label">Status</span>
        </div>
        <div class="proj-table">
            @foreach($recentOrders as $order)
            <div class="proj-row" x-data @click="$dispatch('open-order-modal', {{ $order->id }})">
                <div class="proj-avatar" style="background:{{ $order->status === 'pending' ? '#f5803e' : '#00d27a' }}">{{ substr($order->order_number, -2) }}</div>
                <span class="proj-name">Table {{ $order->table->table_number ?? 'N/A' }}</span>
                <span class="proj-pct-badge uppercase">{{ $order->status }}</span>
                <span class="proj-time">{{ $order->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="card sales-card">
        <div class="sales-header">
            <span class="sales-title">Sales Overview</span>
        </div>
        <canvas id="totalSalesChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('marketDonut').getContext('2d'),{
        type:'doughnut',
        data:{ 
            labels: {!! json_encode($topProductNames) !!},
            datasets:[{ 
                data: {!! json_encode($topProductQtys) !!}, 
                backgroundColor: ['#2c7be5', '#e63757', '#00d97e', '#f5803e', '#6b5eae', '#0D49AB', '#3b82f6', '#06b6d4', '#10b981', '#14b8a6'], 
                borderWidth:0, hoverOffset:2 
            }] 
        },
        options:{ responsive:false, cutout:'72%', plugins:{ legend:{ display:false } }, animation:{ duration:800 } }
    });

    const tsCtx=document.getElementById('totalSalesChart').getContext('2d');
    const tsGrad=tsCtx.createLinearGradient(0,0,0,260);
    tsGrad.addColorStop(0,'rgba(44,123,229,0.18)');
    tsGrad.addColorStop(1,'rgba(44,123,229,0.02)');
    new Chart(tsCtx,{
        type:'line',
        data:{
            labels: {!! json_encode($chartData['labels']) !!},
            datasets:[{
                data: {!! json_encode($chartData['data']) !!},
                borderColor:'#2c7be5', borderWidth:2, backgroundColor:tsGrad, fill:true, tension:0.3,
                pointRadius:5, pointBackgroundColor:'#fff', pointBorderColor:'#2c7be5', pointBorderWidth:2, pointHoverRadius:6
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:true, aspectRatio:1.9,
            plugins:{ legend:{ display:false } },
            scales:{
                x:{ grid:{ color:'#eef0f5', drawBorder:false }, border:{ display:false }, ticks:{ color:'#9da9bb', font:{ family:"'Poppins',sans-serif", size:11 }, padding:4 } },
                y:{ grid:{ color:'#eef0f5', drawBorder:false }, border:{ display:false }, ticks:{ color:'#9da9bb', font:{ family:"'Poppins',sans-serif", size:11 }, padding:6 } }
            }
        }
    });
</script>
@endpush
