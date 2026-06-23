<div x-data="orderModal()" 
     @open-order-modal.window="openModal($event.detail)"
     x-show="isOpen"
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
     
    <!-- Backdrop -->
    <div x-show="isOpen" 
         x-transition.opacity.duration.300ms
         @click="closeModal()"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh] md:max-h-[85vh]">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b flex justify-between items-center bg-slate-50 relative z-10">
            <div>
                <h2 class="text-xl font-black text-slate-800" x-text="order ? 'Order #' + order.order_number : 'Loading...'"></h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5" x-text="order ? formatTime(order.created_at) : ''"></p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="printBill()" x-show="order" title="Print Bill" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </button>
                <button @click="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1 relative bg-white">
            <!-- Loading State -->
            <div x-show="isLoading" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm">
                <svg class="animate-spin w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <template x-if="order">
                <div class="space-y-6">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center font-black text-slate-800 shadow-sm flex-shrink-0">
                                <span x-text="order.table ? order.table.table_number : '#'"></span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Table</p>
                                <p class="font-bold text-slate-800 text-sm" x-text="order.table ? 'Table ' + order.table.table_number : 'N/A'"></p>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Status</p>
                                <span class="px-2 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest inline-block shadow-sm"
                                    :class="{
                                        'bg-amber-100 text-amber-700': order.status === 'pending',
                                        'bg-blue-100 text-blue-700': order.status === 'preparing',
                                        'bg-indigo-100 text-indigo-700': order.status === 'ready',
                                        'bg-emerald-100 text-emerald-700': order.status === 'served',
                                        'bg-red-100 text-red-700': order.status === 'cancelled'
                                    }"
                                    x-text="order.status"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div>
                        <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-4">Order Items</h3>
                        <div class="space-y-3">
                            <template x-for="item in order.order_items" :key="item.id">
                                <div class="flex gap-4 p-4 rounded-2xl border border-slate-100 items-start bg-slate-50/50 hover:bg-slate-50 transition">
                                    <span class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center font-black text-emerald-600 text-sm flex-shrink-0 shadow-sm" x-text="item.quantity + 'x'"></span>
                                    <div class="flex-1 pt-1">
                                        <div class="flex justify-between items-start">
                                            <p class="font-bold text-slate-800 leading-tight" x-text="item.menu_item ? item.menu_item.name : 'Deleted Item'"></p>
                                            <p class="font-black text-emerald-600 text-sm whitespace-nowrap ml-4" x-text="'Rs ' + (item.price * item.quantity).toLocaleString()"></p>
                                        </div>
                                        <template x-if="item.special_instructions">
                                            <div class="flex items-start gap-1.5 mt-2">
                                                <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <p class="text-xs text-red-500 font-bold italic" x-text="item.special_instructions"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <template x-if="order.notes">
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                <span class="font-black text-[10px] uppercase tracking-widest text-amber-700">Special Instructions</span>
                            </div>
                            <p class="text-sm font-semibold text-amber-900 whitespace-pre-wrap" x-text="order.notes"></p>
                        </div>
                    </template>
                    
                    <!-- Total -->
                    <div class="flex justify-between items-center p-5 bg-slate-800 text-white rounded-2xl shadow-lg">
                        <span class="font-black text-slate-400 uppercase tracking-widest text-xs">Total Amount</span>
                        <span class="font-black text-2xl text-emerald-400" x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer Actions -->
        <div class="p-6 border-t bg-slate-50 relative z-10" x-show="order && !['served', 'cancelled'].includes(order.status)">
            
            <template x-if="order && order.status === 'pending'">
                <div class="flex gap-3 w-full">
                    <button @click="updateStatus('preparing')" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-amber-200 text-sm tracking-widest uppercase">
                        Prepare
                    </button>
                    <button @click="confirmCancel()" class="px-6 bg-red-50 hover:bg-red-100 text-red-600 font-black py-4 rounded-xl transition text-sm tracking-widest uppercase border border-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
            
            <template x-if="order && order.status === 'preparing'">
                <div class="flex gap-3 w-full">
                    <button @click="updateStatus('ready')" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-blue-200 text-sm tracking-widest uppercase">
                        Ready
                    </button>
                    <button @click="confirmCancel()" class="px-6 bg-red-50 hover:bg-red-100 text-red-600 font-black py-4 rounded-xl transition text-sm tracking-widest uppercase border border-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
            
            <template x-if="order && order.status === 'ready'">
                <div class="flex gap-3 w-full">
                    <button @click="updateStatus('served')" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 rounded-xl transition shadow-lg shadow-emerald-200 text-sm tracking-widest uppercase">
                        Served
                    </button>
                    <button @click="confirmCancel()" class="px-6 bg-red-50 hover:bg-red-100 text-red-600 font-black py-4 rounded-xl transition text-sm tracking-widest uppercase border border-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
            
        </div>
        <!-- Read-only footer if served/cancelled -->
        <div class="p-6 border-t bg-slate-50 flex items-center justify-center gap-3 relative z-10" x-show="order && ['served', 'cancelled'].includes(order.status)">
             <span class="px-6 py-3 text-xs font-black uppercase rounded-xl tracking-widest border"
                   :class="{
                       'bg-emerald-50 text-emerald-700 border-emerald-200': order && order.status === 'served',
                       'bg-red-50 text-red-700 border-red-200': order && order.status === 'cancelled'
                   }"
                   x-text="order ? 'Order ' + order.status : ''"></span>
             <button @click="printBill()" class="px-5 py-3 text-xs font-black uppercase rounded-xl tracking-widest border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition flex items-center gap-2 shadow-sm">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                 Print Bill
             </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderModal', () => ({
        isOpen: false,
        isLoading: false,
        orderId: null,
        order: null,

        openModal(id) {
            this.orderId = id;
            this.isOpen = true;
            this.isLoading = true;
            this.order = null;
            
            axios.get(`/kitchen/orders/${id}/details`)
                .then(response => {
                    this.order = response.data;
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                    alert('Error loading order details.');
                    this.closeModal();
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        closeModal() {
            this.isOpen = false;
            setTimeout(() => {
                this.order = null;
                this.orderId = null;
            }, 300);
        },

        confirmCancel() {
            if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                this.updateStatus('cancelled');
            }
        },

        updateStatus(status) {
            this.isLoading = true;
            axios.put(`/kitchen/orders/${this.orderId}/status`, { status: status })
                .then(response => {
                    // Update local order status
                    if(this.order) this.order.status = status;
                    // Dispatch an event so the parent list can refresh
                    window.dispatchEvent(new CustomEvent('order-updated', { detail: { id: this.orderId, status: status } }));
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    alert('Error updating status.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        printBill() {
            if (!this.order) return;
            const prefix = window.location.pathname.startsWith('/kitchen') ? '/kitchen' : '/admin';
            window.open(`${prefix}/orders/${this.orderId}/print`, '_blank');
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' • ' + date.toLocaleDateString();
        }
    }));
});
</script>
