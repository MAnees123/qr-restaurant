@extends('layouts.admin')

@section('header', auth()->user()->restaurant->name ?? 'Admin Dashboard')

@section('content')
    <!-- Smart Search System -->
    <div class="mb-8" x-data="smartSearch()">
        <div class="relative group">
            <div
                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors z-20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" x-model="query" @input.debounce.300ms="search()" @keydown.down.prevent="selectNext()"
                @keydown.up.prevent="selectPrev()" @keydown.enter.prevent="goToSelected()" @click.away="open = false"
                @focus="if(results.length > 0) open = true" placeholder="Smart Search: Order ID, Table #, or Date..."
                class="w-1/2 bg-white border-2 border-slate-100 rounded-2xl py-3 pl-14 pr-24 text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-0 transition shadow-sm hover:shadow-md z-10 relative">


            <!-- Results Dropdown -->
            <div x-show="open && results.length > 0" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute left-0 right-0 mt-4 bg-white rounded-[2rem] shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-[400px] overflow-y-auto pb-4">

                <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest"
                        x-text="results.length + ' Matches Found'"></span>
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-200 px-2 py-1 rounded">Arrows
                        to Navigate • Enter to View</span>
                </div>

                <template x-for="(order, index) in results" :key="order.id">
                    <a :href="order.url"
                        class="flex items-center gap-6 px-8 py-4 transition group border-b border-slate-50 last:border-0"
                        :class="selectedIndex === index ? 'bg-emerald-50 border-emerald-100' : 'hover:bg-slate-50'">

                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-800 group-hover:bg-emerald-600 group-hover:text-white transition shadow-sm"
                            :class="selectedIndex === index ? 'bg-emerald-600 text-white' : ''">
                            <span x-text="order.table_number !== 'N/A' ? order.table_number : '#'"></span>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="font-black text-slate-800" x-html="highlight(order.order_number)"></h4>
                                <span class="text-xs font-black text-emerald-600"
                                    x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                            </div>
                            <div class="flex gap-3 items-center mt-1">
                                <span class="text-[10px] font-black uppercase text-slate-400" x-text="order.date"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-slate-100"
                                    :class="{
                                        'text-amber-500 bg-amber-50': order.status === 'pending',
                                        'text-blue-500 bg-blue-50': order.status === 'preparing',
                                        'text-emerald-500 bg-emerald-50': order.status === 'ready' || order
                                            .status === 'served'
                                    }"
                                    x-text="order.status"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded"
                                    :class="{
                                        'text-emerald-600 bg-emerald-100': order.payment_status === 'paid',
                                        'text-red-600 bg-red-100': order.payment_status === 'pending'
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
                class="absolute left-0 right-0 mt-4 bg-white rounded-[2rem] shadow-2xl border border-slate-100 z-50 p-12 text-center">
                <div class="bg-slate-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-black text-slate-800">No Orders Found</h3>
                <p class="text-sm text-slate-400 font-bold mt-1">Try searching with a different Order ID, Table #, or Date
                </p>
            </div>

            <button type="button" @click="search()"
                class="absolute mx-8 right-1 top-1 bottom-1 bg-slate-900 text-white px-8 rounded-lg font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition z-20">
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

    <!-- Order Status Notifications -->
    <div class="fixed top-20 right-8 w-96 z-50 max-w-[calc(100vw-4rem)]" x-data="orderStatusNotifications()" x-init="initPolling()">
        <template x-if="notifications.length > 0">
            <div
                class="bg-slate-900 text-white rounded-[2rem] p-6 shadow-lg animate-pulse hover:animate-none transition-all">
                <div class="flex items-center justify-between mb-4 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-white text-slate-900 p-3 rounded-2xl shadow-lg shadow-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h-4m0 0H6m4 0v4m0-4v-4m-2 8H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-2.5">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-white">Order Updates</h2>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">New orders & status
                                changes</p>
                        </div>
                    </div>
                    <button @click="notifications = []" type="button"
                        class="text-[10px] font-black uppercase tracking-widest bg-slate-800/80 hover:bg-slate-700 px-3 py-2 rounded-full transition">
                        Done
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="(notification, index) in notifications"
                        :key="notification.id + notification.updated_at">
                        <div
                            class="bg-slate-800/90 border border-slate-700 rounded-3xl p-4 flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black text-white" x-text="notificationMessage(notification)"></p>
                                <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-1"
                                    x-text="formatTime(notification.updated_at)"></p>
                            </div>
                            <button @click="dismissNotification(index)" type="button"
                                class="text-[10px] font-black uppercase tracking-widest text-amber-300 hover:text-white transition">Dismiss</button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Waiter Notifications (Bell System) -->
    <div class="fixed bottom-8 right-8 w-96 z-50 max-w-[calc(100vw-4rem)]" x-data="waiterCalls()" x-init="initPolling()">
        <template x-if="calls.length > 0">
            <div
                class="bg-amber-50 border-2 border-amber-200 rounded-[2rem] p-6 shadow-lg animate-pulse hover:animate-none transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-500 text-white p-3 rounded-2xl shadow-lg shadow-amber-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-amber-900">Waiter Requested!</h2>
                            <p class="text-xs text-amber-700 font-bold uppercase tracking-widest">Table assistance needed
                                immediately</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="call in calls" :key="call.id">
                            <div
                                class="bg-white px-4 py-2 rounded-xl border border-amber-200 flex items-center gap-3 shadow-sm">
                                <span class="font-black text-amber-900"
                                    x-text="'Table ' + call.table.table_number"></span>
                                <button @click="completeCall(call.id)"
                                    class="bg-amber-500 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition">Done</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.revenue') }}"
            class="bg-white rounded-[2rem] shadow-sm border p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg border-l-8 border-emerald-500 block">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Paid Revenue</h3>
            <p class="text-3xl font-black text-slate-800">Rs {{ number_format($stats['total_revenue']) }}</p>
            <div class="mt-4 flex gap-2">
                <span class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-2 py-1 rounded">CASH: Rs
                    {{ number_format($stats['cash_payments']) }}</span>
                <span class="text-[10px] font-black bg-blue-100 text-blue-700 px-2 py-1 rounded">ONLINE: Rs
                    {{ number_format($stats['online_payments']) }}</span>
            </div>
        </a>

        <a href="{{ route('admin.orders.index') }}"
            class="bg-white rounded-[2rem] shadow-sm border p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg border-l-8 border-amber-500 block">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pending Payments</h3>
            <p class="text-3xl font-black text-slate-800">Rs {{ number_format($stats['pending_payments']) }}</p>
            <p class="text-[10px] font-bold text-amber-600 mt-2">Uncollected from active tables</p>
        </a>

        <a href="{{ route('admin.orders.index') }}"
            class="bg-white rounded-[2rem] shadow-sm border p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg block">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Orders</h3>
            <p class="text-3xl font-black text-slate-800">{{ $stats['total_orders'] }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-2">{{ $stats['pending_orders'] }} currently in kitchen</p>
        </a>

        <a href="{{ route('admin.tables.index') }}"
            class="bg-white rounded-[2rem] shadow-sm border p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg block">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Active Tables</h3>
            <p class="text-3xl font-black text-slate-800">{{ $stats['active_tables'] }}</p>
            <p class="text-[10px] font-bold text-blue-600 mt-2">{{ $stats['pending_reservations'] }} pending reservations
            </p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Sales Analytics</h2>
                    <p class="text-sm text-slate-400 font-bold uppercase tracking-tight">Financial Performance Tracking</p>
                </div>
                <div class="flex bg-slate-100 p-1 rounded-xl">
                    @foreach (['hourly', 'daily', 'weekly', 'monthly', 'yearly'] as $r)
                        <a href="{{ route('admin.dashboard', ['range' => $r]) }}"
                            class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition {{ $range === $r ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                            {{ $r }}
                        </a>
                    @endforeach
                </div>
            </div>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-black text-slate-800">Quick Actions</h2>
            </div>
            <div class="space-y-4">
                <a href="{{ route('admin.menu.download') }}" target="_blank"
                    class="flex items-center gap-4 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 hover:bg-emerald-100 transition group">
                    <div class="bg-emerald-600 text-white p-3 rounded-xl shadow-lg shadow-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-black text-emerald-900 leading-tight">Download Menu PDF</p>
                        <p class="text-xs text-emerald-600 font-bold uppercase">Professional Export</p>
                    </div>
                </a>

                <a href="{{ route('admin.discounts.create') }}"
                    class="flex items-center gap-4 p-4 bg-amber-50 rounded-2xl border border-amber-100 hover:bg-amber-100 transition">
                    <div class="bg-amber-600 text-white p-3 rounded-xl shadow-lg shadow-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-black text-amber-900 leading-tight">Create Coupon</p>
                        <p class="text-xs text-amber-600 font-bold uppercase">Marketing Tool</p>
                    </div>
                </a>

                <a href="{{ route('admin.menu-items.create') }}"
                    class="flex items-center gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100 hover:bg-blue-100 transition">
                    <div class="bg-blue-600 text-white p-3 rounded-xl shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-black text-blue-900 leading-tight">Add Menu Item</p>
                        <p class="text-xs text-blue-600 font-bold uppercase">Expand Offerings</p>
                    </div>
                </a>
            </div>

            <div class="mt-8">
                <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Latest Reservations</h3>
                <div class="space-y-3">
                    @foreach ($upcomingReservations as $res)
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
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

    <div class="bg-white rounded-[2rem] shadow-sm border overflow-hidden" x-data="liveOrders()"
        x-init="initPolling()">
        <div class="p-8 border-b flex justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-xl font-black text-slate-800">Live Kitchen Monitor</h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Real-time KOT Stream</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black uppercase tracking-widest">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Server
                </span>
            </div>
        </div>
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 bg-white">
            <template x-for="order in orders" :key="order.id">
                <div
                    class="bg-slate-50 rounded-[2rem] border border-slate-100 p-6 hover:shadow-xl hover:shadow-slate-200/50 transition duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4">
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest shadow-sm"
                            :class="{
                                'bg-amber-100 text-amber-700': order.status === 'pending',
                                'bg-blue-100 text-blue-700': order.status === 'preparing',
                                'bg-emerald-100 text-emerald-700': order.status === 'ready',
                                'bg-slate-100 text-slate-500': order.status === 'served'
                            }"
                            x-text="order.status"></span>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-white border border-slate-100 flex items-center justify-center font-black text-slate-800 shadow-sm group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                            <span x-text="order.table ? order.table.table_number : '#'"></span>
                        </div>
                        <div>
                            <p class="font-black text-slate-800 text-lg" x-text="order.order_number"></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
                                x-text="formatTime(order.created_at)"></p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-slate-400">Amount</span>
                            <span class="text-slate-800"
                                x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-slate-400">Payment</span>
                            <span
                                class="px-3 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest shadow-sm border"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 border-emerald-200': order
                                        .payment_status === 'paid',
                                    'bg-red-50 text-red-600 border-red-100': order.payment_status === 'pending'
                                }"
                                x-text="order.payment_status"></span>
                        </div>
                    </div>

                    <a :href="'/admin/orders/' + order.id"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 rounded-xl font-black text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-black hover:border-slate-900 transition duration-300">
                        View Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
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

        function waiterCalls() {
            return {
                calls: [],
                pollingInterval: null,
                playSound() {
                    const audio = new Audio('/audio/notification.mp3');
                    audio.play().catch(e => console.log(e));
                },
                initPolling() {
                    this.fetchCalls();
                    this.pollingInterval = setInterval(() => this.fetchCalls(), 5000);
                },
                fetchCalls() {
                    axios.get('{{ route('admin.table-calls.index') }}')
                        .then(response => {
                            if (response.data.length > this.calls.length && this.calls.length !== 0) {
                                this.playSound(); // Play sound if there's a new call
                            } else if (response.data.length > 0 && this.calls.length === 0) {
                                this.playSound(); // Play sound for initial load if there are calls
                            }
                            this.calls = response.data;
                        })
                        .catch(error => console.error('Error fetching table calls:', error));
                },
                completeCall(id) {
                    axios.post(`/admin/table-calls/${id}/complete`)
                        .then(() => {
                            this.calls = this.calls.filter(c => c.id !== id);
                        })
                        .catch(error => console.error('Error completing call:', error));
                }
            }
        }

        function orderStatusNotifications() {
            return {
                notifications: [],
                pollingInterval: null,
                lastCheckedAt: new Date().toISOString(),
                previousStates: {},
                playSound() {
                    const audio = new Audio('/audio/notification.mp3');
                    audio.play().catch(e => console.log(e));
                },
                initPolling() {
                    this.fetchNotifications();
                    this.pollingInterval = setInterval(() => this.fetchNotifications(), 5000);
                },
                shouldShowNotification(order) {
                    const prevState = this.previousStates[order.id];
                    const isNewPendingOrder = order.status === 'pending' && !prevState;
                    const isPreparingToReady = order.status === 'ready';
                    return isNewPendingOrder || isPreparingToReady;
                },
                fetchNotifications() {
                    axios.get('{{ route('admin.order-notifications') }}', {
                            params: {
                                since: this.lastCheckedAt
                            }
                        })
                        .then(response => {
                            const updates = response.data;
                            console.log('Fetched orders:', updates);
                            updates.reverse().forEach(order => {
                                if (this.shouldShowNotification(order)) {
                                    const isDuplicate = this.notifications.some(note => note.id === order.id &&
                                        note.updated_at === order.updated_at);
                                    if (!isDuplicate) {
                                        this.notifications.unshift(order);
                                        this.playSound();
                                        console.log('Added notification:', order);
                                    }
                                }
                                this.previousStates[order.id] = order;
                            });
                            this.lastCheckedAt = new Date().toISOString();
                        })
                        .catch(error => console.error('Order notification error:', error));
                },
                dismissNotification(index) {
                    this.notifications.splice(index, 1);
                },
                notificationMessage(notification) {
                    const tableNum = notification.table?.table_number ?? '#';
                    if (notification.status === 'pending') {
                        return `New Order from Table ${tableNum}! Ready to be cooked.`;
                    } else if (notification.status === 'ready') {
                        return `Order Ready! Table ${tableNum}'s food is prepared.`;
                    }
                    return `Table ${tableNum} order status: ${notification.status}`;
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
@endsection
