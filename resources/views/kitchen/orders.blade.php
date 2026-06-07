@extends('layouts.kitchen')

@section('header', ucfirst($status) . ' Orders')

@section('content')
<div x-data="kitchenOrders('{{ $status }}')" x-init="initPolling()" class="space-y-6">
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl"
                 :class="{
                    'bg-amber-100 text-amber-600': status === 'pending',
                    'bg-blue-100 text-blue-600': status === 'preparing',
                    'bg-emerald-100 text-emerald-600': status === 'ready'
                 }" x-text="orders.length"></div>
            <div>
                <h3 class="font-black text-slate-800 text-lg uppercase tracking-tight" x-text="status + ' Orders'"></h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Live Updates Enabled</p>
            </div>
        </div>
        <button @click="fetchOrders()" class="p-2 text-slate-400 hover:text-emerald-600 transition">
            <svg class="w-6 h-6" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </button>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="order in orders" :key="order.id">
            <div class="bg-white border-2 rounded-2xl shadow-sm overflow-hidden flex flex-col transition hover:shadow-md"
                 :class="{
                    'border-amber-200': order.status === 'pending',
                    'border-blue-200': order.status === 'preparing',
                    'border-emerald-200': order.status === 'ready'
                 }">
                <!-- Order Header -->
                <div class="p-4 flex justify-between items-center border-b"
                     :class="{
                        'bg-amber-50/50 border-amber-100': order.status === 'pending',
                        'bg-blue-50/50 border-blue-100': order.status === 'preparing',
                        'bg-emerald-50/50 border-emerald-100': order.status === 'ready'
                     }">
                    <div>
                        <h4 class="font-black text-slate-800 text-lg" x-text="'Table ' + (order.table ? order.table.table_number : 'N/A')"></h4>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest" x-text="'#' + order.order_number"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-slate-400" x-text="formatTime(order.created_at)"></p>
                    </div>
                </div>

                <!-- Items List -->
                <div class="p-5 flex-1 space-y-4">
                    <template x-for="item in order.order_items" :key="item.id">
                        <div class="flex gap-4">
                            <span class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-800 text-sm flex-shrink-0" x-text="item.quantity + 'x'"></span>
                            <div class="flex-1">
                                <p class="font-bold text-slate-800 text-md leading-tight" x-text="item.menu_item ? item.menu_item.name : 'Deleted Item'"></p>
                    <template x-if="item.special_instructions">
                                    <p class="text-xs text-red-500 mt-1 font-bold italic bg-red-50 p-1 px-2 rounded-lg inline-block" x-text="'NOTE: ' + item.special_instructions"></p>
                                </template>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Order-level Special Instructions (always visible) -->
                    <div class="mt-4 rounded-xl border overflow-hidden"
                         :class="order.notes ? 'border-amber-300 bg-amber-50' : 'border-slate-100 bg-slate-50'">
                        <div class="px-3 py-2 flex items-center gap-2"
                             :class="order.notes ? 'bg-amber-100/60' : 'bg-slate-100/60'">
                            <svg class="w-4 h-4 flex-shrink-0" :class="order.notes ? 'text-amber-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            <span class="font-black text-[10px] uppercase tracking-widest"
                                  :class="order.notes ? 'text-amber-700' : 'text-slate-400'">Special Instructions</span>
                        </div>
                        <div class="px-3 py-2">
                            <template x-if="order.notes">
                                <p class="text-sm font-semibold whitespace-pre-wrap"
                                   :class="order.notes ? 'text-amber-900' : ''" x-text="order.notes"></p>
                            </template>
                            <template x-if="!order.notes">
                                <p class="text-xs text-slate-400 italic">No special notes</p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="p-4 bg-slate-50 border-t flex gap-2">
                    <template x-if="order.status === 'pending'">
                        <button @click="updateStatus(order.id, 'preparing')" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-amber-200 text-sm tracking-widest uppercase">START COOKING</button>
                    </template>
                    <template x-if="order.status === 'preparing'">
                        <button @click="updateStatus(order.id, 'ready')" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-blue-200 text-sm tracking-widest uppercase">MARK READY</button>
                    </template>
                    <template x-if="order.status === 'ready'">
                        <button @click="updateStatus(order.id, 'served')" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-emerald-200 text-sm tracking-widest uppercase">COMPLETE SERVING</button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <template x-if="orders.length === 0">
        <div class="text-center py-24 bg-white rounded-[2rem] border-2 border-dashed border-slate-200">
            <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-800" x-text="'No ' + status + ' orders' "></h3>
            <p class="text-slate-500 font-medium">Sit tight! New orders will appear here automatically.</p>
        </div>
    </template>
</div>
@endsection

@section('scripts')
<script>
function kitchenOrders(status) {
    return {
        status: status,
        orders: [],
        loading: false,
        pollingInterval: null,

        initPolling() {
            this.fetchOrders();
            this.pollingInterval = setInterval(() => this.fetchOrders(), 5000); 
        },

        fetchOrders() {
            this.loading = true;
            axios.get('{{ route("kitchen.orders") }}', { params: { status: this.status } })
                .then(response => {
                    this.orders = response.data;
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        updateStatus(orderId, nextStatus) {
            axios.put(`/kitchen/orders/${orderId}/status`, { status: nextStatus })
                .then(() => {
                    this.fetchOrders();
                })
                .catch(error => console.error('Error updating order status:', error));
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>
@endsection
