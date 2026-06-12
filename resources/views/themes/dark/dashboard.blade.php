@extends('themes.dark.layout')

@push('styles')
<style>
.row1 { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:18px; width:100%; }
.stat-card { background:#1a1a1a; border-radius:16px; padding:24px 24px 22px; display:flex; flex-direction:column; gap:10px; min-height:130px; cursor:pointer;}
.stat-label { font-size:13px; color:#888; font-weight:500; }
.stat-value { font-size:30px; font-weight:800; color:#fff; letter-spacing:-0.8px; line-height:1.1; }
.stat-value.green { color:#c8f135; }
.stat-note { font-size:12.5px; color:#555; font-weight:400; }
.stat-note.green { color:#c8f135; }

.row2 { display:grid; grid-template-columns:1fr 300px; gap:18px; width:100%; }
.spending-card { background:#1a1a1a; border-radius:16px; padding:28px 28px 24px; }
.spending-title { font-size:22px; font-weight:800; color:#fff; margin-bottom:24px; }
.spending-body { display:flex; align-items:center; gap:32px; }
.donut-wrap { width:200px; height:200px; flex-shrink:0; position:relative; display:flex; align-items:center; justify-content:center; }
.donut-wrap canvas { width:200px!important; height:200px!important; }

.spending-right { flex:1; }
.total-spend-row { display:flex; align-items:center; gap:14px; margin-bottom:18px; }
.money-icon { width:42px; height:42px; border-radius:50%; background:#c8f135; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; }
.ts-label { font-size:12px; color:#888; font-weight:500; margin-bottom:2px; }
.ts-value { font-size:22px; font-weight:800; color:#c8f135; letter-spacing:-0.5px; }
.spend-divider { border:none; border-top:1px solid #2a2a2a; margin-bottom:18px; }
.legend-list { display:flex; flex-direction:column; gap:12px; }
.legend-item { display:flex; align-items:center; gap:10px; font-size:13.5px; color:#ccc; font-weight:500; }
.leg-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }

.right-col { display:flex; flex-direction:column; gap:18px; }
.stocks-card { background:#1a1a1a; border-radius:16px; padding:22px 22px 0; flex:1; overflow:hidden; }
.stocks-label { font-size:13px; color:#888; font-weight:500; margin-bottom:4px; }
.stocks-value { font-size:28px; font-weight:800; color:#fff; letter-spacing:-0.5px; margin-bottom:10px; }
.stocks-chart-wrap { margin:0 -22px; padding:0; }

.team-card { background:#1a1a1a; border-radius:16px; padding:20px 22px; display:flex; align-items:center; justify-content:space-between; }
</style>
@endpush

@section('content')
<div class="row1">
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value green">PKR {{ number_format($stats['total_revenue']) }}</div>
        <div class="stat-note green">Online: PKR {{ number_format($stats['online_payments']) }}</div>
    </div>
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.orders.index') }}'">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $stats['total_orders'] }}</div>
        <div class="stat-note text-amber-500">{{ $stats['pending_orders'] }} Pending</div>
    </div>
    <div class="stat-card" x-data @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="stat-label">Pending Payments</div>
        <div class="stat-value text-red-400">PKR {{ number_format($stats['pending_payments']) }}</div>
        <div class="stat-note text-red-400">Uncollected Cash</div>
    </div>
</div>

<div class="row2">
    <div class="spending-card">
        <div class="spending-title">Payment Types</div>
        <div class="spending-body">
            <div class="donut-wrap"><canvas id="spendingDonut"></canvas></div>
            <div class="spending-right">
                <div class="total-spend-row">
                    <div class="money-icon">💸</div>
                    <div>
                        <div class="ts-label">Total Amount</div>
                        <div class="ts-value">PKR {{ number_format($stats['total_revenue']) }}</div>
                    </div>
                </div>
                <hr class="spend-divider">
                <div class="legend-list">
                    <div class="legend-item"><div class="leg-dot" style="background:#c8f135"></div>Online Payments (PKR {{ number_format($stats['online_payments']) }})</div>
                    <div class="legend-item"><div class="leg-dot" style="background:#8ab828"></div>Cash Payments (PKR {{ number_format($stats['cash_payments']) }})</div>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <h3 class="text-xl font-bold text-white mb-4">Recent Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 text-gray-400 font-medium border-b border-gray-800">Order</th>
                            <th class="py-2 text-gray-400 font-medium border-b border-gray-800">Table</th>
                            <th class="py-2 text-gray-400 font-medium border-b border-gray-800">Status</th>
                            <th class="py-2 text-gray-400 font-medium border-b border-gray-800">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr x-data class="hover:bg-[#222] cursor-pointer transition" @click="$dispatch('open-order-modal', {{ $order->id }})">
                            <td class="py-3 font-semibold">{{ $order->order_number }}</td>
                            <td class="py-3">T-{{ $order->table->table_number ?? 'N/A' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs rounded-md font-bold uppercase {{ $order->status === 'pending' ? 'bg-amber-500/20 text-amber-500' : ($order->status === 'served' ? 'bg-[#c8f135]/20 text-[#c8f135]' : 'bg-emerald-500/20 text-emerald-500') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 font-bold">PKR {{ number_format($order->total_amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="right-col">
        <div class="stocks-card">
            <div style="padding:0 0 6px">
                <div class="stocks-label">Revenue Graph</div>
                <div class="stocks-value">Overview</div>
            </div>
            <div class="stocks-chart-wrap">
                <canvas id="stocksChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('spendingDonut').getContext('2d'),{
  type:'doughnut',
  data:{
    labels:['Online','Cash'],
    datasets:[{
      data:[{{ $stats['online_payments'] }}, {{ $stats['cash_payments'] }}],
      backgroundColor:['#c8f135','#8ab828'],
      borderWidth:0, borderRadius:4, hoverOffset:6, spacing:3
    }]
  },
  options:{
    responsive:false, cutout:'62%', animation:{duration:900},
    plugins:{legend:{display:false}}
  }
});

const sc=document.getElementById('stocksChart').getContext('2d');
const sg=sc.createLinearGradient(0,0,0,150);
sg.addColorStop(0,'rgba(200,241,53,0.35)');
sg.addColorStop(1,'rgba(200,241,53,0.01)');

new Chart(sc,{
  type:'line',
  data:{
    labels: {!! json_encode($chartData['labels']) !!},
    datasets:[{
      data: {!! json_encode($chartData['data']) !!},
      borderColor:'#c8f135', borderWidth:2.5, backgroundColor:sg, fill:true,
      tension:0.4, pointRadius:0, pointHoverRadius:5,
      pointHoverBackgroundColor:'#c8f135', pointHoverBorderColor:'#0d0d0d', pointHoverBorderWidth:2
    }]
  },
  options:{
    responsive:true, maintainAspectRatio:false,
    plugins:{legend:{display:false}},
    scales:{x:{display:false}, y:{display:false}}
  }
});
</script>
@endpush
