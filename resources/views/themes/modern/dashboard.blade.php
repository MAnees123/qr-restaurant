@extends('themes.modern.layout')

@push('styles')
<style>
/* Dashboard specific styles */
.page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; width: 100%; }
.page-title { font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.6px; line-height: 1.15; margin-bottom: 5px; }
.page-sub { font-size: 13px; color: #94a3b8; font-weight: 400; }
.hdr-controls { display: flex; align-items: center; gap: 10px; padding-bottom: 3px; flex-shrink: 0; }
.month-pill { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 50px; border: 1.5px solid #dde3ed; background: #ffffff; font-size: 13px; font-weight: 500; color: #334155; cursor: pointer; white-space: nowrap; }
.filter-pill { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 50px; border: none; background: #2563eb; font-size: 13px; font-weight: 600; color: #ffffff; cursor: pointer; white-space: nowrap; }
.cards-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; width: 100%; margin-bottom: 20px; }
.scard { background: #ffffff; border-radius: 16px; padding: 22px 22px 20px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.04); min-height: 122px; display: flex; flex-direction: column; justify-content: space-between; width: 100%; }
.scard.blue { background: #2563eb; box-shadow: 0 4px 22px rgba(37,99,235,0.32); }
.sc-label { font-size: 12px; font-weight: 500; color: #94a3b8; line-height: 1; }
.scard.blue .sc-label { color: rgba(255,255,255,0.82); }
.sc-icon { position: absolute; top: 20px; right: 20px; width: 34px; height: 34px; border-radius: 50%; background: rgba(15,23,42,0.07); display: flex; align-items: center; justify-content: center; }
.scard.blue .sc-icon { background: rgba(255,255,255,0.22); }
.sc-value { font-size: 29px; font-weight: 800; color: #0f172a; letter-spacing: -1px; line-height: 1.1; margin-top: 10px; margin-bottom: 6px; }
.scard.blue .sc-value { color: #ffffff; }
.accent { color: #2563eb !important; }
.sc-growth { font-size: 11.5px; font-weight: 500; color: #22c55e; }
.scard.blue .sc-growth { color: rgba(255,255,255,0.88); }
.charts-row { display: grid; grid-template-columns: 1fr 400px; gap: 20px; width: 100%; }
.chart-card { background: #ffffff; border-radius: 16px; padding: 22px 24px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.04); width: 100%; }
.chart-card-title { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 18px; }
.rev-body { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.rev-amount { font-size: 30px; font-weight: 800; color: #0f172a; letter-spacing: -1px; margin-bottom: 16px; }
.pay-btn { display: inline-flex; align-items: center; padding: 9px 24px; border-radius: 50px; background: #2563eb; color: #ffffff; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }
.donut-wrap { width: 160px; height: 160px; flex-shrink: 0; position: relative; }
.donut-wrap canvas { width: 160px !important; height: 160px !important; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-sub">Monitor your business performance in real-time</p>
    </div>
    <div class="hdr-controls">
        <button class="month-pill">
            This Month
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M2 3.5L5.5 7L9 3.5" stroke="#334155" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="filter-pill">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M1 2h11l-4 5v4l-3-1.5V7L1 2Z" fill="white"/></svg>
            Filter
        </button>
    </div>
</div>

<div class="cards-row">
    <div class="scard blue" x-data style="cursor: pointer;" @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="sc-label">Total Revenue</div>
        <div class="sc-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M7.5 1.5v12" stroke="white" stroke-width="1.5" stroke-linecap="round"/><path d="M5 5c0-1.1 1.12-2 2.5-2S10 3.9 10 5s-1.12 2-2.5 2S5 8.9 5 10s1.12 2 2.5 2 2.5-.9 2.5-2" stroke="white" stroke-width="1.4" stroke-linecap="round"/></svg></div>
        <div>
            <div class="sc-value">PKR {{ number_format($stats['total_revenue']) }}</div>
            <div class="sc-growth">Online: {{ number_format($stats['online_payments']) }}</div>
        </div>
    </div>
    <div class="scard" x-data style="cursor: pointer;" @click="window.location.href='{{ route('admin.orders.index') }}'">
        <div class="sc-label">Total Orders</div>
        <div class="sc-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="5.5" r="2.5" stroke="#334155" stroke-width="1.5"/><path d="M2 13.5c0-3.04 2.46-5.5 5.5-5.5s5.5 2.46 5.5 5.5" stroke="#334155" stroke-width="1.5" stroke-linecap="round"/></svg></div>
        <div>
            <div class="sc-value">{{ $stats['total_orders'] }}</div>
            <div class="sc-growth">{{ $stats['pending_orders'] }} pending</div>
        </div>
    </div>
    <div class="scard" x-data style="cursor: pointer;" @click="window.location.href='{{ route('admin.tables.index') }}'">
        <div class="sc-label">Active Tables</div>
        <div class="sc-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M9.5 2.5H12.5V12.5H9.5" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 10L9 7.5L6 5" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 7.5H9" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round"/></svg></div>
        <div>
            <div class="sc-value accent">{{ $stats['active_tables'] }}</div>
            <div class="sc-growth">Currently occupied</div>
        </div>
    </div>
    <div class="scard" x-data style="cursor: pointer;" @click="window.location.href='{{ route('admin.revenue') }}'">
        <div class="sc-label">Pending Payments</div>
        <div class="sc-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#334155" stroke-width="1.5"/><path d="M7.5 7.5V3.5" stroke="#334155" stroke-width="1.5" stroke-linecap="round"/><path d="M7.5 7.5L10.5 9.5" stroke="#334155" stroke-width="1.5" stroke-linecap="round"/></svg></div>
        <div>
            <div class="sc-value">PKR {{ number_format($stats['pending_payments']) }}</div>
            <div class="sc-growth">Awaiting settlement</div>
        </div>
    </div>
</div>

<div class="charts-row">
    <div class="chart-card">
        <div class="chart-card-title">Revenue Trend</div>
        <canvas id="lineChart"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title">Payment Breakdown</div>
        <div class="rev-body">
            <div>
                <div class="rev-amount">PKR {{ number_format($stats['total_revenue']) }}</div>
                <a href="{{ route('admin.revenue') }}" class="pay-btn">View Report</a>
            </div>
            <div class="donut-wrap">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="chart-card">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Recent Orders</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-slate-500 font-semibold border-b border-slate-100 bg-slate-50 rounded-tl-xl">Order ID</th>
                    <th class="py-3 px-4 text-slate-500 font-semibold border-b border-slate-100 bg-slate-50">Table</th>
                    <th class="py-3 px-4 text-slate-500 font-semibold border-b border-slate-100 bg-slate-50">Amount</th>
                    <th class="py-3 px-4 text-slate-500 font-semibold border-b border-slate-100 bg-slate-50">Status</th>
                    <th class="py-3 px-4 text-slate-500 font-semibold border-b border-slate-100 bg-slate-50 rounded-tr-xl">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr class="hover:bg-slate-50 cursor-pointer transition border-b border-slate-50 last:border-0" x-data @click="$dispatch('open-order-modal', {{ $order->id }})">
                    <td class="py-4 px-4 font-bold text-blue-600">{{ $order->order_number }}</td>
                    <td class="py-4 px-4 font-semibold text-slate-700">Table {{ $order->table->table_number ?? 'N/A' }}</td>
                    <td class="py-4 px-4 font-bold text-slate-800">PKR {{ number_format($order->total_amount) }}</td>
                    <td class="py-4 px-4">
                        <span class="px-3 py-1 text-xs rounded-full font-bold uppercase {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-600' : ($order->status === 'served' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-500">{{ $order->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const lc = document.getElementById('lineChart').getContext('2d');
    const gb = lc.createLinearGradient(0,0,0,230);
    gb.addColorStop(0,'rgba(37,99,235,0.15)');
    gb.addColorStop(1,'rgba(37,99,235,0.01)');
    
    new Chart(lc, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['data']) !!},
                borderColor: '#2563eb', borderWidth: 2.2,
                backgroundColor: gb, fill: true, tension: 0.44,
                pointRadius: 0, pointHoverRadius: 5,
                pointHoverBackgroundColor: '#2563eb',
                pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true, aspectRatio: 2.4,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { color: '#f1f5f9' }, border: { display: false } }
            }
        }
    });

    const dc = document.getElementById('donutChart').getContext('2d');
    const total = {{ $stats['cash_payments'] + $stats['online_payments'] }};
    const cashPct = total ? ({{ $stats['cash_payments'] }} / total * 100).toFixed(1) : 0;
    const onlinePct = total ? ({{ $stats['online_payments'] }} / total * 100).toFixed(1) : 0;
    
    new Chart(dc, {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'Online'],
            datasets: [{ data: [cashPct, onlinePct], backgroundColor: ['#2563eb','#93c5fd'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            cutout: '65%', responsive: false, animation: { animateRotate: true, duration: 900 },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
