<!-- Global Order Status Notifications -->
<div class="fixed top-20 right-8 w-96 z-50 max-w-[calc(100vw-4rem)]" x-data="orderStatusNotifications()" x-init="initPolling()">
    <template x-if="notifications.length > 0">
        <div class="bg-slate-900 text-white rounded-[2rem] p-6 shadow-lg animate-pulse hover:animate-none transition-all text-left">
            <div class="flex items-center justify-between mb-4 gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white text-slate-900 p-3 rounded-2xl shadow-lg shadow-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-4m0 0H6m4 0v4m0-4v-4m-2 8H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-2.5"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-white">Order Updates</h2>
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

<!-- Global Waiter Notifications -->
<div class="fixed bottom-8 right-8 w-96 z-50 max-w-[calc(100vw-4rem)]" x-data="waiterCallsGlobal()" x-init="initPolling()">
    <template x-if="calls.length > 0">
        <div class="bg-amber-50 border-2 border-amber-200 rounded-[2rem] p-6 shadow-lg animate-pulse hover:animate-none transition-all text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="bg-amber-500 text-white p-3 rounded-2xl shadow-lg shadow-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-amber-900">Waiter Requested!</h2>
                        <p class="text-xs text-amber-700 font-bold uppercase tracking-widest">Table assistance needed immediately</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="call in calls" :key="call.id">
                        <div class="bg-white px-4 py-2 rounded-xl border border-amber-200 flex items-center gap-3 shadow-sm">
                            <span class="font-black text-amber-900" x-text="'Table ' + call.table.table_number"></span>
                            <button @click="completeCall(call.id)" class="bg-amber-500 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition">Done</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    @keyframes vibrate { 0% { transform: rotate(0deg); } 25% { transform: rotate(5deg); } 50% { transform: rotate(0deg); } 75% { transform: rotate(-5deg); } 100% { transform: rotate(0deg); } }
    .animate-vibrate { animation: vibrate 0.2s linear infinite; }
</style>

@if(!isset($scriptsIncluded))
@php $scriptsIncluded = true; @endphp
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function globalNotifications() {
        return {
            calls: [],
            dropdownOpen: false,
            pollingInterval: null,
            initPolling() {
                this.fetchCalls();
                this.pollingInterval = setInterval(() => this.fetchCalls(), 5000);
            },
            fetchCalls() {
                if(typeof axios === 'undefined') return;
                axios.get('{{ route('admin.table-calls.index') }}')
                    .then(response => { this.calls = response.data; })
                    .catch(error => console.error('Notification Error:', error));
            },
            acceptCall(id) {
                axios.post(`/admin/table-calls/${id}/accept`)
                    .then(() => { this.fetchCalls(); })
                    .catch(error => console.error('Error accepting call:', error));
            },
            completeCall(id) {
                axios.post(`/admin/table-calls/${id}/complete`)
                    .then(() => { this.calls = this.calls.filter(c => c.id !== id); })
                    .catch(error => console.error('Error completing call:', error));
            },
            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }
    }

    function waiterCallsGlobal() {
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
                if(typeof axios === 'undefined') return;
                axios.get('{{ route('admin.table-calls.index') }}')
                    .then(response => {
                        if (response.data.length > this.calls.length && this.calls.length !== 0) {
                            this.playSound(); 
                        } else if (response.data.length > 0 && this.calls.length === 0) {
                            this.playSound(); 
                        }
                        this.calls = response.data;
                    })
                    .catch(error => console.error('Error fetching table calls:', error));
            },
            completeCall(id) {
                axios.post(`/admin/table-calls/${id}/complete`)
                    .then(() => { this.calls = this.calls.filter(c => c.id !== id); })
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
                if(typeof axios === 'undefined') return;
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
                    .catch(error => console.error('Order notification error:', error));
            },
            dismissNotification(index) {
                this.notifications.splice(index, 1);
            },
            notificationMessage(notification) {
                const tableNum = notification.table?.table_number ?? '#';
                if (notification.status === 'pending') {
                    return `New Order from Table ${tableNum}!`;
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
@endif
