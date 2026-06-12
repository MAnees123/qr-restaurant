@extends('themes.classic.layout')

@push('styles')
<style>
.row1 { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:20px; width:100%; }
.stat-card { background:#ffffff; border-radius:12px; padding:22px 22px 20px; display:flex; flex-direction:column; gap:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05); width:100%; cursor:pointer; }
.stat-label { font-size:13px; font-weight:500; color:#6b7280; }
.stat-value { font-size:27px; font-weight:800; color:#111827; letter-spacing:-0.5px; line-height:1.1; }
.stat-change { font-size:12.5px; font-weight:600; color:#10b981; }

.row2 { display:grid; grid-template-columns:1fr 380px; gap:18px; margin-bottom:20px; width:100%; }
.white-card { background:#ffffff; border-radius:12px; padding:22px 22px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); width:100%; }
.card-title { font-size:15px; font-weight:700; color:#111827; margin-bottom:16px; }

.sales-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.legend-row { display:flex; align-items:center; gap:16px; }
.leg-item { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#6b7280; font-weight:500; }
.leg-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

.products-body { display:flex; align-items:center; gap:20px; }
.pie-wrap { width:150px; height:150px; flex-shrink:0; }
.pie-wrap canvas { width:150px!important; height:150px!important; }
.prod-legend { flex:1; display:flex; flex-direction:column; gap:12px; }
.prod-leg-row { display:flex; align-items:center; justify-content:space-between; }
.prod-leg-left { display:flex; align-items:center; gap:9px; }
.prod-dot { width:11px; height:11px; border-radius:50%; flex-shrink:0; }
.prod-name { font-size:13px; color:#374151; font-weight:500; }
.prod-pct { font-size:13px; font-weight:700; color:#111827; }

.row3 { display:grid; grid-template-columns:1fr; gap:18px; width:100%; }
.orders-table { width:100%; border-collapse:collapse; margin-top:4px; }
.orders-table th { text-align:left; padding:10px 12px; font-size:12.5px; font-weight:700; color:#374151; border-bottom:2px solid #f3f4f6; background:#f9fafb; }
.orders-table td { padding:12px 12px; font-size:13px; color:#374151; border-bottom:1px solid #f3f4f6; font-weight:500; }
.orders-table tr { cursor: pointer; }
.orders-table tr:hover td { background: #f9fafb; }
.status-badge { display:inline-block; font-weight:600; font-size:12.5px; padding: 4px 8px; border-radius: 4px;}
.status-served { color:#3b5bdb; background: #ebf0ff; }
.status-pending { color:#f59e0b; background: #fff8eb; }
.status-ready { color:#10b981; background: #ebfff5; }
</style>
@endpush

@section('content')
<div class="row1">
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">PKR {{ number_format($stats['total_revenue']) }}</div>
        <div class="stat-change text-blue-600">Online: {{ number_format($stats['online_payments']) }}</div>
    </div>
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.orders.index') }}'">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $stats['total_orders'] }}</div>
        <div class="stat-change text-amber-500">{{ $stats['pending_orders'] }} pending</div>
    </div>
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.tables.index') }}'">
        <div class="stat-label">Active Tables</div>
        <div class="stat-value">{{ $stats['active_tables'] }}</div>
        <div class="stat-change text-emerald-500">Occupied</div>
    </div>
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="stat-label">Pending Payments</div>
        <div class="stat-value">PKR {{ number_format($stats['pending_payments']) }}</div>
        <div class="stat-change text-red-500">To be collected</div>
    </div>
</div>

<div class="row2">
    <div class="white-card">
        <div class="sales-header">
            <div class="card-title" style="margin:0">Sales Overview</div>
        </div>
        <canvas id="salesChart"></canvas>
    </div>
    <div class="white-card" x-data style="cursor:pointer;" @click="window.location.href='{{ route('admin.analytics.index') }}'">
        <div class="card-title">Top 10 Products Sold</div>
        <div class="products-body" style="justify-content:center;">
            <div class="pie-wrap"><canvas id="pieChart"></canvas></div>
        </div>
    </div>
</div>

<div class="row3">
    <div class="white-card">
        <div class="card-title">Recent Orders</div>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Table</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr x-data @click="$dispatch('open-order-modal', {{ $order->id }})">
                    <td class="font-bold">{{ $order->order_number }}</td>
                    <td>Table {{ $order->table->table_number ?? 'N/A' }}</td>
                    <td class="font-bold">PKR {{ number_format($order->total_amount) }}</td>
                    <td>
                        <span class="status-badge {{ $order->status === 'pending' ? 'status-pending' : ($order->status === 'served' ? 'status-served' : 'status-ready') }} uppercase">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const sCtx = document.getElementById('salesChart').getContext('2d');
const sg1 = sCtx.createLinearGradient(0,0,0,240);
sg1.addColorStop(0,'rgba(59,91,219,0.15)');
sg1.addColorStop(1,'rgba(59,91,219,0.01)');

new Chart(sCtx,{
  type:'line',
  data:{
    labels: {!! json_encode($chartData['labels']) !!},
    datasets:[{
      data: {!! json_encode($chartData['data']) !!},
      borderColor:'#3b5bdb',borderWidth:2.2,
      backgroundColor:sg1,fill:true,tension:0.45,
      pointRadius:4,pointBackgroundColor:'#fff',
      pointBorderColor:'#3b5bdb',pointBorderWidth:2,
      pointHoverRadius:6
    }]
  },
  options:{
    responsive:true,maintainAspectRatio:true,aspectRatio:2.6,
    plugins:{legend:{display:false}}
  }
});

new Chart(document.getElementById('pieChart').getContext('2d'),{
  type:'pie',
  data:{
    labels:{!! json_encode($topProductNames) !!},
    datasets:[{
      data:{!! json_encode($topProductQtys) !!},
      backgroundColor:['#1a2454', '#3b5bdb', '#e74c3c', '#27ae60', '#f39c12', '#0D49AB', '#3b82f6', '#06b6d4', '#10b981', '#14b8a6'],
      borderWidth:2,borderColor:'#fff',
      hoverOffset:6
    }]
  },
  options:{
    responsive:false,
    animation:{duration:800},
    plugins:{legend:{display:false}}
  }
});
</script>
@endpush
