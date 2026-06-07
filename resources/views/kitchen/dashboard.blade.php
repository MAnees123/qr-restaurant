@extends('layouts.kitchen')

@section('header', 'Kitchen Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('kitchen.orders.status', 'pending') }}" class="block bg-white rounded-2xl shadow-sm border-2 border-amber-100 p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-amber-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-xs font-black text-amber-500 uppercase tracking-widest">New Requests</span>
        </div>
        <h3 class="text-lg font-bold text-slate-500">Pending Orders</h3>
        <p class="text-5xl font-black text-slate-800">{{ $stats['pending'] }}</p>
    </a>

    <a href="{{ route('kitchen.orders.status', 'preparing') }}" class="block bg-white rounded-2xl shadow-sm border-2 border-blue-100 p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-blue-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span class="text-xs font-black text-blue-500 uppercase tracking-widest">In Progress</span>
        </div>
        <h3 class="text-lg font-bold text-slate-500">Preparing</h3>
        <p class="text-5xl font-black text-slate-800">{{ $stats['preparing'] }}</p>
    </a>

    <a href="{{ route('kitchen.orders.status', 'ready') }}" class="block bg-white rounded-2xl shadow-sm border-2 border-emerald-100 p-8 transition duration-300 transform hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-emerald-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="text-xs font-black text-emerald-500 uppercase tracking-widest">Ready to Go</span>
        </div>
        <h3 class="text-lg font-bold text-slate-500">Ready Orders</h3>
        <p class="text-5xl font-black text-slate-800">{{ $stats['ready'] }}</p>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border p-8 text-center py-12">
    <h2 class="text-2xl font-black text-emerald-900 mb-2">Welcome to Kitchen Control</h2>
    <p class="text-slate-500 max-w-md mx-auto">Select a category from the sidebar or click a card above to manage live orders and keep the kitchen running smoothly.</p>
</div>

<!-- Order Status Notifications -->
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
@endsection

@section('scripts')
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
@endsection
