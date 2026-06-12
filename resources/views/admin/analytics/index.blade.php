@extends('themes.' . ($theme ?? 'default') . '.layout')

@section('title', 'Product Sales Analytics')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Hot Items & Analytics</h2>
        <p class="text-gray-500 text-sm">Track trending products, peak hours, and overall sales momentum. Data updates live with every sale.</p>
    </div>
    <div>
        <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-xl shadow-sm transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Data
        </button>
    </div>
</div>

<!-- Badges Legend -->
<div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-[#2a2a2a] mb-6 flex flex-wrap gap-4">
    <div class="flex items-center gap-2"><span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">🔥 Hot Item</span><span class="text-sm text-gray-500">3+ items sold</span></div>
    <div class="flex items-center gap-2"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">📈 Trending</span><span class="text-sm text-gray-500">Sales grew by > 50%</span></div>
    <div class="flex items-center gap-2"><span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">🏆 Best Seller</span><span class="text-sm text-gray-500">Top 15% of all products</span></div>
    <div class="flex items-center gap-2"><span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-bold">⚡ Fast Selling</span><span class="text-sm text-gray-500">10+ units sold</span></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Top Items This Month -->
    <div class="lg:col-span-2 bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-[#2a2a2a]">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Top Performing Products (This Month)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase border-b border-gray-100 dark:border-[#2a2a2a]">
                        <th class="pb-3">#</th>
                        <th class="pb-3">Product Name</th>
                        <th class="pb-3 text-center">Badge</th>
                        <th class="pb-3 text-right">Qty Sold</th>
                        <th class="pb-3 text-right">Growth</th>
                        <th class="pb-3 text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-[#2a2a2a]">
                    @forelse($hotItems as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#111] transition">
                            <td class="py-3 text-gray-400 font-bold">{{ $index + 1 }}</td>
                            <td class="py-3">
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $item->name ?? 'Unknown' }}</div>
                            </td>
                            <td class="py-3 text-center">
                                @if($item->badge_type)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#222] text-gray-700 dark:text-gray-300 rounded text-xs font-bold">
                                        {{ $item->badge_type }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-bold text-blue-600 dark:text-blue-400">{{ $item->quantity_sold }}</td>
                            <td class="py-3 text-right">
                                @if($item->growth_percentage > 0)
                                    <span class="text-emerald-500 font-medium">+{{ number_format($item->growth_percentage, 0) }}%</span>
                                @elseif($item->growth_percentage < 0)
                                    <span class="text-red-500 font-medium">{{ number_format($item->growth_percentage, 0) }}%</span>
                                @else
                                    <span class="text-gray-400">0%</span>
                                @endif
                            </td>
                            <td class="py-3 text-right text-gray-600 dark:text-gray-400 font-medium">PKR {{ number_format($item->total_revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <p class="font-bold">No sales data this month yet</p>
                                <p class="text-xs">Place orders from your menu to see analytics here.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Today's Movers -->
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-[#2a2a2a]">
            <h3 class="text-md font-bold text-gray-800 dark:text-white mb-4 flex justify-between items-center">
                Today's Fast Movers
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded font-bold">Live</span>
            </h3>
            <div class="space-y-3">
                @forelse($todayTop as $item)
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-[#2a2a2a] pb-2 last:border-0">
                        <div>
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-blue-600">{{ $item->total_qty }} sold</div>
                            <div class="text-xs text-gray-400">PKR {{ number_format($item->total_revenue) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-400 text-center py-4">
                        <p class="font-bold">No sales yet today</p>
                        <p class="text-xs mt-1">Items will appear here as orders come in.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Weekly Hot -->
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-[#2a2a2a]">
            <h3 class="text-md font-bold text-gray-800 dark:text-white mb-4">This Week's Best</h3>
            <div class="space-y-3">
                @forelse($weekTop as $index => $item)
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-[#2a2a2a] pb-2 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center">{{ $index + 1 }}</span>
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-blue-600">{{ $item->total_qty }}</div>
                            <div class="text-xs text-gray-400">PKR {{ number_format($item->total_revenue) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-400 text-center py-4">
                        <p class="font-bold">No data for this week</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Charts Row: Demand Graph + Pie Chart -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
    <!-- Demand & Sales Line Graph -->
    <div class="lg:col-span-3 bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-[#2a2a2a]">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Product Demand & Sales</h3>
            <select id="graphFilter" onchange="fetchAllCharts()" class="border border-gray-200 dark:border-[#2a2a2a] bg-gray-50 dark:bg-[#111] text-gray-700 dark:text-white rounded-lg px-3 py-1.5 text-sm outline-none">
                <option value="today">Today (Hourly)</option>
                <option value="7days" selected>Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
            </select>
        </div>
        <div class="h-80 w-full relative">
            <canvas id="salesDemandChart"></canvas>
        </div>
    </div>

    <!-- Top 5 Items Pie Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-[#2a2a2a]">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Top 5 Items Sold</h3>
        <div class="h-80 w-full relative flex items-center justify-center">
            <canvas id="topItemsPieChart"></canvas>
        </div>
        <div id="pieChartLegend" class="mt-4 space-y-2"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let salesChart = null;
    let pieChart = null;

    // Theme-specific pie chart color palettes
    const themePalettes = {
        'default':  ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6'],
        'modern':   ['#0D49AB', '#3b82f6', '#06b6d4', '#8b5cf6', '#ec4899'],
        'falcon':   ['#2c7be5', '#e63757', '#00d97e', '#f5803e', '#6b5eae'],
        'classic':  ['#1a2454', '#3b5bdb', '#e74c3c', '#27ae60', '#f39c12'],
        'dark':     ['#c8f135', '#3b82f6', '#ef4444', '#a855f7', '#06b6d4'],
    };

    const currentTheme = '{{ $theme ?? "default" }}';
    const colors = themePalettes[currentTheme] || themePalettes['default'];

    document.addEventListener('DOMContentLoaded', function() {
        fetchAllCharts();
    });

    function fetchAllCharts() {
        const filter = document.getElementById('graphFilter').value;
        fetchLineChart(filter);
        fetchPieChart(filter);
    }

    function fetchLineChart(filter) {
        fetch(`{{ route('admin.analytics.api.sales') }}?filter=${filter}`)
            .then(res => res.json())
            .then(data => {
                renderLineChart(data.labels, data.qty, data.revenue);
            })
            .catch(err => console.error(err));
    }

    function fetchPieChart(filter) {
        fetch(`{{ route('admin.analytics.api.topitems') }}?filter=${filter}`)
            .then(res => res.json())
            .then(data => {
                renderPieChart(data.names, data.qty);
            })
            .catch(err => console.error(err));
    }

    function renderLineChart(labels, qtyData, revData) {
        const ctx = document.getElementById('salesDemandChart').getContext('2d');
        if (salesChart) salesChart.destroy();

        const isDark = currentTheme === 'dark';
        const textColor = isDark ? '#aaa' : '#4b5563';
        const gridColor = isDark ? '#222' : '#f3f4f6';

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Quantity Sold',
                        data: qtyData,
                        borderColor: colors[0],
                        backgroundColor: colors[0] + '22',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: colors[0],
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (PKR)',
                        data: revData,
                        borderColor: colors[2],
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: colors[2],
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { color: textColor, usePointStyle: true, padding: 16, font: { weight: '600' } } },
                    tooltip: { backgroundColor: isDark ? '#1a1a1a' : '#fff', titleColor: isDark ? '#fff' : '#111', bodyColor: isDark ? '#ccc' : '#555', borderColor: isDark ? '#333' : '#e5e7eb', borderWidth: 1 }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Quantity', color: textColor }, ticks: { color: textColor }, grid: { color: gridColor } },
                    y1: { type: 'linear', position: 'right', title: { display: true, text: 'Revenue (PKR)', color: textColor }, ticks: { color: textColor }, grid: { drawOnChartArea: false } }
                }
            }
        });
    }

    function renderPieChart(names, qtyData) {
        const ctx = document.getElementById('topItemsPieChart').getContext('2d');
        if (pieChart) pieChart.destroy();

        const isDark = currentTheme === 'dark';
        const legendEl = document.getElementById('pieChartLegend');

        if (!names || names.length === 0) {
            legendEl.innerHTML = '<p class="text-center text-sm text-gray-400">No items sold in this period</p>';
            return;
        }

        pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: names,
                datasets: [{
                    data: qtyData,
                    backgroundColor: colors,
                    borderColor: isDark ? '#1a1a1a' : '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1a1a1a' : '#fff',
                        titleColor: isDark ? '#fff' : '#111',
                        bodyColor: isDark ? '#ccc' : '#555',
                        borderColor: isDark ? '#333' : '#e5e7eb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((context.parsed / total) * 100).toFixed(1);
                                return ` ${context.label}: ${context.parsed} sold (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Build custom legend
        let html = '';
        names.forEach((name, i) => {
            const total = qtyData.reduce((a, b) => a + b, 0);
            const pct = total > 0 ? ((qtyData[i] / total) * 100).toFixed(0) : 0;
            html += `<div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:${colors[i]}"></span>
                    <span class="${isDark ? 'text-gray-300' : 'text-gray-700'} font-medium">${name}</span>
                </div>
                <span class="${isDark ? 'text-gray-400' : 'text-gray-500'} font-bold">${qtyData[i]} <span class="text-xs font-normal">(${pct}%)</span></span>
            </div>`;
        });
        legendEl.innerHTML = html;
    }
</script>
@endpush
