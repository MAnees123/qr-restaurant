@extends('layouts.admin')

@section('header', auth()->user()->restaurant->name ?? 'Admin Dashboard')

@section('content')
    <!-- Smart Search System -->
    <div class="mb-6" x-data="smartSearch()">
        <div class="relative group">
            <div
                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors z-20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" x-model="query" @input.debounce.300ms="search()" @keydown.down.prevent="selectNext()"
                @keydown.up.prevent="selectPrev()" @keydown.enter.prevent="goToSelected()" @click.away="open = false"
                @focus="if(results.length > 0) open = true" placeholder="Smart Search: Order ID, Table #, or Date..."
                class="input w-full md:w-1/2 !pl-14 pr-28 z-10 relative">


            <!-- Results Dropdown -->
            <div x-show="open && results.length > 0" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="card absolute left-0 right-0 mt-3 shadow-2xl z-50 overflow-hidden max-h-[400px] overflow-y-auto pb-2">

                <div class="px-6 py-4 bg-slate-50 border-b flex flex-wrap gap-2 justify-between items-center">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest"
                        x-text="results.length + ' Matches Found'"></span>
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-200 px-2 py-1 rounded">Arrows
                        to Navigate • Enter to View</span>
                </div>

                <template x-for="(order, index) in results" :key="order.id">
                    <a :href="order.url"
                        class="flex items-center gap-4 sm:gap-6 px-6 py-4 transition group border-b border-slate-50 last:border-0"
                        :class="selectedIndex === index ? 'bg-indigo-50 border-indigo-100' : 'hover:bg-slate-50'">

                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-800 group-hover:bg-indigo-600 group-hover:text-white transition shadow-sm"
                            :class="selectedIndex === index ? 'bg-indigo-600 text-white' : ''">
                            <span x-text="order.table_number !== 'N/A' ? order.table_number : '#'"></span>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="font-black text-slate-800" x-html="highlight(order.order_number)"></h4>
                                <span class="text-sm font-semibold text-slate-800"
                                    x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                            </div>
                            <div class="flex gap-3 items-center mt-1">
                                <span class="text-[10px] font-black uppercase text-slate-400" x-text="order.date"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="badge capitalize"
                                    :class="{
                                        'badge-warning': order.status === 'pending',
                                        'badge-info': order.status === 'preparing',
                                        'badge-success': order.status === 'ready' || order
                                            .status === 'served'
                                    }"
                                    x-text="order.status"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="badge capitalize"
                                    :class="{
                                        'badge-success': order.payment_status === 'paid',
                                        'badge-danger': order.payment_status === 'pending'
                                    }"
                                    x-text="order.payment_status"></span>
                            </div>
                        </div>

                        <div class="text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                    </a>
                </template>
            </div>

            <!-- No Results -->
            <div x-show="open && query.length > 2 && results.length === 0 && !loading"
                x-transition.opacity
                class="card absolute left-0 right-0 mt-3 shadow-2xl z-50 p-10 text-center">
                <div class="bg-slate-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800">No Orders Found</h3>
                <p class="text-sm text-slate-400 font-bold mt-1">Try searching with a different Order ID, Table #, or Date
                </p>
            </div>

            <button type="button" @click="search()"
                class="btn btn-primary absolute right-1.5 top-1.5 bottom-1.5 !py-0 !px-5 z-20">
                <span x-show="!loading">Search</span>
                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </button>
        </div>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('admin.revenue') }}"
            class="card card-hover p-6 block border-l-4 !border-l-emerald-500">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Paid Revenue</h3>
            <p class="text-3xl font-bold text-slate-800">Rs {{ number_format($stats['total_revenue']) }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="badge badge-success">CASH: Rs
                    {{ number_format($stats['cash_payments']) }}</span>
                <span class="badge badge-info">ONLINE: Rs
                    {{ number_format($stats['online_payments']) }}</span>
            </div>
        </a>

        <a href="{{ route('admin.orders.index') }}"
            class="card card-hover p-6 block border-l-4 !border-l-amber-500">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Pending Payments</h3>
            <p class="text-3xl font-bold text-slate-800">Rs {{ number_format($stats['pending_payments']) }}</p>
            <p class="text-[10px] font-bold text-amber-600 mt-2">Uncollected from active tables</p>
        </a>

        <a href="{{ route('admin.orders.index') }}"
            class="card card-hover p-6 block">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Total Orders</h3>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['total_orders'] }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-2">{{ $stats['pending_orders'] }} currently in kitchen</p>
        </a>

        @if(auth()->user()->restaurant?->hasFeature('table_ordering'))
        <a href="{{ route('admin.tables.index') }}"
            class="card card-hover p-6 block">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Active Tables</h3>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['active_tables'] }}</p>
            <p class="text-[10px] font-bold text-blue-600 mt-2">{{ $stats['pending_reservations'] }} pending reservations
            </p>
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 card p-6">
            <div class="flex flex-wrap gap-3 justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Sales Analytics</h2>
                    <p class="page-subtitle">Financial Performance Tracking</p>
                </div>
                <div class="flex flex-wrap bg-slate-100 p-1 rounded-xl">
                    @foreach (['hourly', 'daily', 'weekly', 'monthly', 'yearly'] as $r)
                        <a href="{{ route('admin.dashboard', ['range' => $r]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wide transition {{ $range === $r ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                            {{ $r }}
                        </a>
                    @endforeach
                </div>
            </div>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-slate-800">Quick Actions</h2>
            </div>
            <div class="space-y-3">
                <a href="{{ route('admin.menu.download') }}" target="_blank"
                    class="flex items-center gap-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100 hover:bg-emerald-100 hover:-translate-y-0.5 transition group">
                    <div class="bg-emerald-600 text-white p-3 rounded-xl shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-emerald-900 leading-tight">Download Menu PDF</p>
                        <p class="text-xs text-emerald-600 font-bold uppercase">Professional Export</p>
                    </div>
                </a>

                @if(auth()->user()->restaurant?->hasFeature('coupons'))
                <a href="{{ route('admin.discounts.create') }}"
                    class="flex items-center gap-4 p-4 bg-amber-50 rounded-xl border border-amber-100 hover:bg-amber-100 hover:-translate-y-0.5 transition">
                    <div class="bg-amber-600 text-white p-3 rounded-xl shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-900 leading-tight">Create Coupon</p>
                        <p class="text-xs text-amber-600 font-bold uppercase">Marketing Tool</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('admin.menu-items.create') }}"
                    class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100 hover:bg-blue-100 hover:-translate-y-0.5 transition">
                    <div class="bg-blue-600 text-white p-3 rounded-xl shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900 leading-tight">Add Menu Item</p>
                        <p class="text-xs text-blue-600 font-bold uppercase">Expand Offerings</p>
                    </div>
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Latest Reservations</h3>
                <div class="space-y-3">
                    @foreach ($upcomingReservations as $res)
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-800">{{ $res->customer_name }}</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase">{{ $res->reservation_time }} •
                                    {{ $res->guests }} Guests</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <div class="card card-hover p-6 mb-6" x-data style="cursor:pointer;" @if(auth()->user()->restaurant?->hasFeature('analytics')) @click="window.location.href='{{ route('admin.analytics.index') }}'" @endif>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-slate-800">Top 10 Products Sold</h2>
        </div>
        <div class="flex justify-center" style="height: 250px;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <div class="card overflow-hidden" x-data="liveOrders()"
        x-init="initPolling()">
        <div class="px-6 py-4 border-b flex flex-wrap gap-3 justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Live Kitchen Monitor</h2>
                <p class="page-subtitle">Real-time KOT Stream</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="badge badge-success !px-3 !py-1.5">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Server
                </span>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" @order-updated.window="fetchOrders()">
            <template x-for="order in orders" :key="order.id">
                <div @click="$dispatch('open-order-modal', order.id)"
                    class="card card-hover bg-slate-50 p-6 group relative overflow-hidden cursor-pointer">
                    <div class="absolute top-0 right-0 p-4">
                        <span class="badge capitalize"
                            :class="{
                                'badge-warning': order.status === 'pending',
                                'badge-info': order.status === 'preparing',
                                'badge-success': order.status === 'ready'
                            }"
                            x-text="order.status"></span>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-xl bg-white border border-slate-100 flex items-center justify-center font-bold text-slate-800 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                            <span x-text="order.table ? order.table.table_number : '#'"></span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-lg" x-text="order.order_number"></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
                                x-text="formatTime(order.created_at)"></p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-slate-400">Amount</span>
                            <span class="text-slate-800 font-semibold"
                                x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-slate-400">Payment</span>
                            <div class="flex gap-2">
                                <span class="badge capitalize" x-text="order.payment_method || 'Cash'"></span>
                                <span
                                    class="badge capitalize"
                                    :class="{
                                        'badge-success': order
                                            .payment_status === 'paid',
                                        'badge-danger': order.payment_status === 'pending'
                                    }"
                                    x-text="order.payment_status"></span>
                            </div>
                        </div>

                        <!-- Special Instructions preview -->
                        <template x-if="order.notes">
                            <div class="flex items-start gap-2 p-3 bg-amber-50 rounded-xl border border-amber-100 mt-1">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                <p class="text-[11px] font-bold text-amber-800 line-clamp-2" x-text="order.notes"></p>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex items-center justify-center text-slate-300 group-hover:text-indigo-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');

            const labels = {!! json_encode($chartData['labels']) !!};
            const data = {!! json_encode($chartData['data']) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (Rs)',
                        data: data,
                        borderColor: '#10b981', // emerald-500
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 16,
                            titleFont: {
                                size: 14,
                                family: 'Inter',
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 14,
                                family: 'Inter'
                            },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Rs ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    weight: 'bold',
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    weight: 'bold',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });

        function smartSearch() {
            return {
                query: '',
                results: [],
                open: false,
                loading: false,
                selectedIndex: -1,
                search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.open = false;
                        return;
                    }
                    this.loading = true;
                    this.open = true;
                    axios.get('{{ route('admin.orders.live-search') }}', {
                            params: {
                                q: this.query
                            }
                        })
                        .then(response => {
                            this.results = response.data;
                            this.loading = false;
                            this.selectedIndex = -1;
                        })
                        .catch(error => {
                            console.error('Search Error:', error);
                            this.loading = false;
                        });
                },
                selectNext() {
                    if (this.selectedIndex < this.results.length - 1) {
                        this.selectedIndex++;
                    }
                },
                selectPrev() {
                    if (this.selectedIndex > 0) {
                        this.selectedIndex--;
                    }
                },
                goToSelected() {
                    if (this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
                        window.location.href = this.results[this.selectedIndex].url;
                    } else if (this.results.length > 0) {
                        window.location.href = this.results[0].url;
                    }
                },
                highlight(text) {
                    if (!this.query) return text;
                    const reg = new RegExp(`(${this.query})`, 'gi');
                    return text.replace(reg, '<mark class="bg-emerald-100 text-emerald-900 px-0.5 rounded">$1</mark>');
                }
            }
        }

        function liveOrders() {
            return {
                orders: [],
                pollingInterval: null,
                initPolling() {
                    this.fetchOrders();
                    this.pollingInterval = setInterval(() => this.fetchOrders(), 10000);
                },
                fetchOrders() {
                    axios.get('{{ route('kitchen.orders') }}')
                        .then(response => {
                            this.orders = response.data.slice(0, 5);
                        })
                        .catch(error => console.error('Error fetching dashboard orders:', error));
                },
                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('pieChart').getContext('2d'),{
                type:'doughnut',
                data:{
                    labels:{!! json_encode($topProductNames) !!},
                    datasets:[{
                        data:{!! json_encode($topProductQtys) !!},
                        backgroundColor:['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6', '#0D49AB', '#06b6d4', '#ec4899', '#14b8a6', '#6366f1'],
                        borderWidth:2,borderColor:'#fff',
                        hoverOffset:6
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    animation:{duration:800},
                    plugins:{legend:{display:false}}
                }
            });
        });
    </script>
@endsection
