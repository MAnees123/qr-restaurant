<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kitchen Panel - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="h-screen flex overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-emerald-900 text-white flex-shrink-0 sticky top-0 self-start h-screen overflow-y-auto">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-amber-400">Kitchen KOT</h1>
                <p class="text-xs text-emerald-300 mt-1 uppercase tracking-widest font-bold">
                    {{ auth()->user()->restaurant->name ?? 'System' }}</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('kitchen.dashboard') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->routeIs('kitchen.dashboard') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">Kitchen
                    Overview</a>
                @if(auth()->user()->role === 'admin' || auth()->user()->is_super_admin)
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">Admin
                    Dashboard</a>
                @endif

                <div class="px-6 py-2 text-xs font-bold text-emerald-400 uppercase mt-4">Order Status</div>
                <a href="{{ route('kitchen.orders.status', 'pending') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/pending') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Pending Orders
                    </span>
                </a>
                <a href="{{ route('kitchen.orders.status', 'preparing') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/preparing') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Preparing
                    </span>
                </a>
                <a href="{{ route('kitchen.orders.status', 'ready') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/ready') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Ready to Serve
                    </span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-12">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-6 py-3 hover:bg-emerald-800 text-red-300 font-medium">Log
                        Out</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 h-screen overflow-x-hidden overflow-y-auto">
            <header class="sticky top-0 z-20 bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                        @yield('header', 'hboard')
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span class="text-sm font-bold text-emerald-600">LIVE CONNECTION</span>
                    </div>
                </div>
            </header>

            <div class="p-6">
                @if (session('success'))
                    <div
                        class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Global Kitchen Order Status Notifications -->
            <div class="fixed top-20 right-8 w-96 z-50 max-w-[calc(100vw-4rem)]" x-data="orderStatusNotifications()" x-init="initPolling()">
                <template x-if="notifications.length > 0">
                    <div class="bg-slate-900 text-white rounded-[2rem] p-6 shadow-lg animate-pulse hover:animate-none transition-all">
                        <div class="flex items-center justify-between mb-4 gap-4">
                            <div class="flex items-center gap-4">
                                <div class="bg-white text-slate-900 p-3 rounded-2xl shadow-lg shadow-slate-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-4m0 0H6m4 0v4m0-4v-4m-2 8H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-2.5"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-black text-white">Kitchen Updates</h2>
                                    <p class="text-xs font-bold uppercase tracking-widest text-slate-300">New orders & status changes</p>
                                </div>
                            </div>
                            <button @click="notifications = []" type="button" class="text-[10px] font-black uppercase tracking-widest bg-slate-800/80 hover:bg-slate-700 px-3 py-2 rounded-full transition">
                                Done
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(notification, index) in notifications" :key="notification.id + notification.updated_at">
                                <div class="bg-slate-800/90 border border-slate-700 rounded-3xl p-4 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-black text-white" x-text="notificationMessage(notification)"></p>
                                        <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-1" x-text="formatTime(notification.updated_at)"></p>
                                    </div>
                                    <button @click="dismissNotification(index)" type="button" class="text-[10px] font-black uppercase tracking-widest text-amber-300 hover:text-white transition">Dismiss</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Set axios CSRF header from meta token
        (function() {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        })();
    </script>
    @yield('scripts')
    <script>
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
                    const isStatusChanged = prevState && prevState.status !== order.status;
                    return isNewPendingOrder || isStatusChanged;
                },
                fetchNotifications() {
                    axios.get('{{ route('admin.order-notifications') }}', { params: { since: this.lastCheckedAt } })
                        .then(response => {
                            const updates = response.data;
                            updates.reverse().forEach(order => {
                                if (this.shouldShowNotification(order)) {
                                    const isDuplicate = this.notifications.some(note => note.id === order.id && note.updated_at === order.updated_at);
                                    if (!isDuplicate) {
                                        this.notifications.unshift(order);
                                        this.playSound();
                                    }
                                }
                                this.previousStates[order.id] = order;
                            });
                            this.lastCheckedAt = new Date().toISOString();
                        })
                        .catch(error => console.error('Kitchen notification error:', error));
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
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
    @include('components.order-modal')
</body>

</html>
