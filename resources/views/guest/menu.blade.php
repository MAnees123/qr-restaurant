@extends('layouts.guest')
@section('title', $restaurant->name . ' - Menu')

@section('content')
<div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative" x-data="customerSPA()" x-init="init()">
    
    <!-- Compact Restaurant Header (Not Sticky) -->
    <div class="px-6 py-3 bg-slate-50 border-b">
        <div class="flex items-center gap-4">
            <!-- Left: Small Circle Logo -->
            <div class="flex-shrink-0">
                @if($restaurant->logo)
                    <img src="{{ Storage::url($restaurant->logo) }}" alt="Logo" class="w-10 h-10 rounded-full object-cover shadow-sm border border-white">
                @else
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-black text-white shadow-sm">
                        {{ substr($restaurant->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Left: Restaurant Name & Table -->
            <div class="flex-1">
                <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none">{{ $restaurant->name }}</h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Table {{ $table->table_number }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Waiter Status Banner (Dismissible) -->
    <template x-if="!statusDismissed && (callStatus === 'sent' || callStatus === 'accepted')">
        <div class="bg-amber-50 border-b border-amber-100 px-6 py-3 flex items-center justify-between animate-pulse">
            <div class="flex items-center gap-3">
                <div class="bg-amber-500 text-white p-1.5 rounded-lg">
                    <svg class="w-4 h-4" :class="callStatus === 'sent' ? 'animate-bounce' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-amber-900 uppercase tracking-widest" x-text="callStatus === 'sent' ? 'Waiter Requested' : 'Waiter are coming'"></p>
                    <p class="text-[9px] text-amber-700 font-bold" x-text="callStatus === 'sent' ? 'Waiting for staff to acknowledge...' : 'A staff member is on their way!'"></p>
                </div>
            </div>
            <button @click="dismissCallStatus()" class="text-amber-400 hover:text-amber-600 p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>

    <!-- Sticky Action Bar (Tabs + Search + Bell in one row) -->
    <div class="sticky top-0 z-[100] bg-white/95 backdrop-blur-md p-3 border-b shadow-sm">
        <div class="flex items-center gap-2">
            <!-- Navigation Buttons (Compact) -->
            <div class="flex gap-1 p-1 bg-slate-100 rounded-xl flex-shrink-0">
                <button @click="activeTab = 'restaurant'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        :class="activeTab === 'restaurant' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400'"
                        class="px-3 py-2 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all duration-300">
                    Menu
                </button>
                <button @click="activeTab = 'order'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        :class="activeTab === 'order' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400'"
                        class="px-3 py-2 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all duration-300 relative">
                    Order
                    <template x-if="activeOrder && activeOrder.status !== 'served' && activeOrder.status !== 'cancelled'">
                        <span class="absolute top-1 right-1 w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse border border-white"></span>
                    </template>
                </button>
            </div>

            <!-- Search Bar (Flexible) -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="searchItems()"
                       placeholder="Search..." 
                       class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-9 pr-3 text-[11px] font-bold text-slate-700 shadow-inner focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <!-- Waiter Bell Button -->
            <button @click="callWaiter()" 
                    :disabled="isCalling || (!statusDismissed && (callStatus === 'sent' || callStatus === 'accepted'))"
                    class="p-2.5 rounded-xl transition-all flex-shrink-0 relative"
                    :class="(callStatus === 'accepted' && !statusDismissed) ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600 hover:bg-amber-200'">
                <svg class="w-5 h-5" :class="isCalling ? 'animate-bounce' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <template x-if="!statusDismissed && (callStatus === 'sent' || callStatus === 'accepted')">
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="callStatus === 'accepted' ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" :class="callStatus === 'accepted' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    </span>
                </template>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="min-h-[60vh]">
        
        <!-- GLOBAL SEARCH RESULTS -->
        <template x-if="searchQuery.length > 0">
            <div class="p-6 bg-white min-h-[60vh]">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest">Search Results</h2>
                    <button @click="searchQuery = ''" class="text-[10px] font-black text-red-500 uppercase">Clear</button>
                </div>
                <div class="space-y-6">
                    <template x-for="item in searchResults" :key="item.id">
                        <div class="bg-white rounded-[2rem] border p-5 flex gap-5 shadow-sm">
                            <img x-show="item.image" :src="'/storage/' + item.image" class="w-20 h-20 object-cover rounded-2xl flex-shrink-0">
                            <div x-show="!item.image" class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <h3 class="font-black text-slate-800 text-sm leading-tight" x-text="item.name"></h3>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-black text-emerald-600 text-sm" x-text="'Rs ' + item.price"></span>
                                    <button @click="addToCart(item.id, item.name, item.price)" class="bg-slate-900 text-white px-3 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest">ADD +</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Tab 1: Restaurant Menu -->
        <div x-show="activeTab === 'restaurant' && searchQuery.length === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="overflow-x-auto whitespace-nowrap py-6 px-6 flex gap-3 no-scrollbar">
                @foreach($categories as $category)
                    <a href="#cat-{{ $category->id }}" class="px-5 py-2 rounded-2xl bg-slate-100 text-slate-500 hover:bg-emerald-600 hover:text-white font-black text-xs uppercase tracking-widest transition-all duration-300 shadow-sm">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <div class="p-6 space-y-10">
                @foreach($categories as $category)
                    @if($category->menuItems->count() > 0)
                        <section id="cat-{{ $category->id }}" class="scroll-mt-[200px]">
                            <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                                {{ $category->name }}
                                <span class="h-1 flex-1 bg-slate-100 rounded-full"></span>
                            </h2>
                            <div class="space-y-6">
                                @foreach($category->menuItems as $item)
                                    <div class="bg-white rounded-[2rem] border-2 border-slate-50 p-5 flex gap-5 hover:border-emerald-100 transition-all group shadow-sm">
                                        @if($item->image)
                                            <img src="{{ Storage::url($item->image) }}" class="w-24 h-24 object-cover rounded-3xl flex-shrink-0 shadow-md group-hover:scale-105 transition-transform" alt="{{ $item->name }}">
                                        @else
                                            <div class="w-24 h-24 bg-slate-50 rounded-3xl flex items-center justify-center flex-shrink-0 border-2 border-dashed border-slate-200">
                                                <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                <h3 class="font-black text-slate-800 leading-tight">{{ $item->name }}</h3>
                                                <p class="text-[10px] text-slate-400 font-bold mt-1">Ready in ~{{ $item->preparation_time }} mins</p>
                                            </div>
                                            <div class="flex items-center justify-between mt-4">
                                                <span class="font-black text-emerald-600">Rs {{ number_format($item->price) }}</span>
                                                <button @click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})" class="bg-slate-900 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition shadow-lg">
                                                    ADD +
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Tab 2: Order Status -->
        <div x-show="activeTab === 'order' && searchQuery.length === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="p-6">
            <template x-if="!activeOrder">
                <div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-8">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">No Active Orders</h3>
                    <p class="text-sm text-slate-400 font-bold leading-relaxed">Place an order from the menu to see its live status here.</p>
                    <button @click="activeTab = 'restaurant'" class="mt-8 bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-200 transition">Browse Menu</button>
                </div>
            </template>

            <template x-if="activeOrder">
                <div class="space-y-8">
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <h3 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-2">Live Status</h3>
                                    <h1 class="text-2xl font-black italic uppercase" x-text="activeOrder.status"></h1>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1" x-text="activeOrder.order_number"></p>
                                    <span class="px-3 py-1 bg-white/10 rounded-lg text-[10px] font-black uppercase tracking-widest" x-text="activeOrder.payment_status"></span>
                                </div>
                            </div>
                            <div class="flex justify-center gap-4 py-4" x-show="activeOrder.status !== 'served' && activeOrder.status !== 'cancelled'">
                                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4 w-20 text-center">
                                    <p class="text-3xl font-black" x-text="countdown.minutes">00</p>
                                    <p class="text-[8px] uppercase font-black text-slate-500 mt-1">Mins</p>
                                </div>
                                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4 w-20 text-center">
                                    <p class="text-3xl font-black" x-text="countdown.seconds">00</p>
                                    <p class="text-[8px] uppercase font-black text-slate-500 mt-1">Secs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-[2rem] border shadow-sm p-8">
                        <h3 class="text-sm font-black text-slate-800 mb-6 uppercase tracking-widest">Order Summary</h3>
                        <div class="space-y-4 divide-y divide-slate-50">
                            <template x-for="item in activeOrder.order_items" :key="item.id">
                                <div class="flex justify-between items-center pt-4 first:pt-0">
                                    <div class="flex gap-4 items-center">
                                        <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-black text-slate-800 text-xs" x-text="item.quantity + 'x'"></span>
                                        <p class="font-bold text-slate-800 text-sm" x-text="item.menu_item.name"></p>
                                    </div>
                                    <span class="font-black text-slate-800 text-sm" x-text="'Rs ' + parseFloat(item.subtotal).toLocaleString()"></span>
                                </div>
                            </template>
                        </div>
                        <div class="mt-8 pt-6 border-t border-dashed border-slate-200 flex justify-between items-center">
                            <span class="font-black text-slate-800 uppercase tracking-widest text-xs">Total</span>
                            <span class="font-black text-xl text-emerald-600" x-text="'Rs ' + parseFloat(activeOrder.total_amount).toLocaleString()"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Basket Modal & UI -->
    <div x-show="activeTab === 'restaurant' && cartCount > 0" class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-6 z-[120]">
        <button @click="isCartOpen = true" class="w-full bg-slate-900 text-white rounded-[2rem] p-6 shadow-2xl flex items-center justify-between transform hover:scale-[1.02] transition-all ring-8 ring-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm" x-text="cartCount"></div>
                <span class="font-black uppercase tracking-[0.2em] text-xs">View Basket</span>
            </div>
            <span class="font-black text-lg" x-text="'Rs ' + cartTotal.toLocaleString()"></span>
        </button>
    </div>

    <!-- Basket Modal Content -->
    <div x-show="isCartOpen" class="fixed inset-0 z-[130] flex items-end sm:items-center justify-center max-w-md mx-auto" style="display: none;">
        <div x-show="isCartOpen" x-transition.opacity @click="isCartOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"></div>
        <div x-show="isCartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" class="relative bg-white w-full rounded-t-[3rem] max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="p-8 border-b flex items-center justify-between bg-slate-50">
                <div>
                    <h2 class="text-2xl font-black text-slate-800">Your Basket</h2>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Review your selections</p>
                </div>
                <button @click="isCartOpen = false" class="p-3 text-slate-400 hover:bg-slate-100 rounded-2xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8 overflow-y-auto flex-1 space-y-8">
                <div class="space-y-6">
                    <template x-for="(item, key) in cart" :key="key">
                        <div class="flex items-center justify-between group">
                            <div class="flex-1">
                                <h4 class="font-black text-slate-800 leading-tight" x-text="item.name"></h4>
                                <p class="text-emerald-600 font-black text-xs mt-1" x-text="'Rs ' + item.price.toLocaleString()"></p>
                            </div>
                            <div class="flex items-center gap-4 bg-slate-50 rounded-2xl px-3 py-2 border border-slate-100">
                                <button @click="updateQuantity(key, item.quantity - 1)" class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-red-500 font-black transition">-</button>
                                <span class="w-4 text-center text-sm font-black text-slate-800" x-text="item.quantity"></span>
                                <button @click="updateQuantity(key, item.quantity + 1)" class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-emerald-600 font-black transition">+</button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <template x-if="!appliedDiscount">
                        <div class="flex gap-2">
                            <input type="text" x-model="couponCode" placeholder="COUPON" class="flex-1 rounded-xl border-slate-200 text-xs font-black uppercase tracking-widest focus:ring-emerald-500 focus:border-emerald-500">
                            <button @click="applyCoupon()" class="bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition">Apply</button>
                        </div>
                    </template>
                    <template x-if="appliedDiscount">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg" x-text="'Coupon: ' + appliedDiscount.code"></span>
                                <p class="text-[10px] text-slate-400 font-bold mt-1" x-text="appliedDiscount.type === 'percentage' ? appliedDiscount.value + '% Discount Applied' : 'Rs ' + appliedDiscount.value + ' Discount Applied'"></p>
                            </div>
                            <button @click="removeCoupon()" class="text-red-500 font-black text-[10px] uppercase hover:underline">Remove</button>
                        </div>
                    </template>
                </div>
                <div class="pt-4">
                    <textarea x-model="notes" placeholder="Any special cooking instructions?" class="w-full border-slate-100 bg-slate-50 rounded-2xl text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 min-h-[100px]"></textarea>
                </div>
            </div>
            <div class="p-8 bg-slate-900">
                <div class="flex justify-between text-white mb-6">
                    <span class="font-black text-lg uppercase tracking-[0.2em]">Total</span>
                    <span class="font-black text-2xl text-emerald-400" x-text="'Rs ' + (cartTotal - discountAmount).toLocaleString()"></span>
                </div>
                <button @click="placeOrder()" :disabled="isPlacing || cartCount === 0" class="w-full bg-emerald-500 hover:bg-emerald-400 text-white font-black py-5 rounded-2xl flex justify-center items-center gap-3 disabled:opacity-50 transition-all shadow-xl shadow-emerald-900/50 uppercase tracking-[0.2em] text-sm">
                    <span x-show="!isPlacing">Place Order</span>
                    <span x-show="isPlacing">Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function customerSPA() {
    return {
        activeTab: 'restaurant',
        cart: {},
        isCartOpen: false,
        notes: '',
        isPlacing: false,
        isCalling: false,
        callStatus: 'idle', 
        statusDismissed: false,
        couponCode: '',
        appliedDiscount: null,
        searchQuery: '',
        searchResults: [],
        allItems: [],
        activeOrder: {!! $activeOrder ? json_encode($activeOrder) : 'null' !!},
        countdown: {
            minutes: '00',
            seconds: '00',
            finished: false
        },
        
        get cartCount() { return Object.values(this.cart).reduce((sum, item) => sum + item.quantity, 0); },
        get cartTotal() { return Object.values(this.cart).reduce((sum, item) => sum + (item.price * item.quantity), 0); },
        get discountAmount() {
            if (!this.appliedDiscount) return 0;
            return this.appliedDiscount.type === 'percentage' ? this.cartTotal * (this.appliedDiscount.value / 100) : Math.min(this.appliedDiscount.value, this.cartTotal);
        },
        
        init() {
            this.cart = {!! json_encode(session('cart', [])) !!};
            if (Array.isArray(this.cart) && this.cart.length === 0) {
                this.cart = {};
            }
            this.appliedDiscount = {!! json_encode(session('discount')) !!};
            const categories = {!! json_encode($categories) !!};
            categories.forEach(cat => cat.menu_items.forEach(item => this.allItems.push(item)));
            if (this.activeOrder) {
                this.updateCountdown();
                setInterval(() => this.updateCountdown(), 1000);
                setInterval(() => this.pollOrderStatus(), 5000);
            }
            setInterval(() => this.pollCallStatus(), 3000);
            this.pollCallStatus();
        },

        pollCallStatus() {
            axios.get('{{ route("table.call.status") }}')
                .then(response => {
                    if (this.callStatus !== response.data.status) {
                        this.callStatus = response.data.status;
                        // Reset dismissed flag if status changes
                        this.statusDismissed = false;
                    }
                });
        },

        dismissCallStatus() {
            this.statusDismissed = true;
        },

        searchItems() {
            if (this.searchQuery.length < 2) { this.searchResults = []; return; }
            const query = this.searchQuery.toLowerCase();
            this.searchResults = this.allItems.filter(item => item.name.toLowerCase().includes(query) || (item.description && item.description.toLowerCase().includes(query)));
        },

        callWaiter() {
            if (this.isCalling) return;
            this.isCalling = true;
            axios.post('{{ route("table.call") }}')
                .then(response => {
                    this.callStatus = 'sent';
                    this.statusDismissed = false;
                    this.isCalling = false;
                })
                .catch(error => {
                    alert(error.response.data.error || 'Something went wrong.');
                    this.isCalling = false;
                });
        },
        
        addToCart(id, name, price) {
            axios.post('{{ route("cart.add") }}', { menu_item_id: id, quantity: 1 }).then(response => {
                let key = id + '_d41d8cd98f00b204e9800998ecf8427e';
                const newCart = { ...this.cart };
                if (newCart[key]) {
                    newCart[key] = { ...newCart[key], quantity: newCart[key].quantity + 1 };
                } else {
                    newCart[key] = { id, name, price, quantity: 1 };
                }
                this.cart = newCart;
            });
        },
        
        updateQuantity(key, newQuantity) {
            if (newQuantity <= 0) {
                axios.post('{{ route("cart.remove") }}', { cart_id: key }).then(() => {
                    const newCart = { ...this.cart };
                    delete newCart[key];
                    this.cart = newCart;
                    if (Object.keys(this.cart).length === 0) this.isCartOpen = false;
                });
            } else {
                axios.post('{{ route("cart.update") }}', { cart_id: key, quantity: newQuantity }).then(() => {
                    const newCart = { ...this.cart };
                    newCart[key] = { ...newCart[key], quantity: newQuantity };
                    this.cart = newCart;
                });
            }
        },

        applyCoupon() {
            if (!this.couponCode) return;
            axios.post('{{ route("cart.discount.apply") }}', { code: this.couponCode }).then(response => { this.appliedDiscount = response.data.discount; this.couponCode = ''; }).catch(error => alert(error.response.data.error || 'Invalid Coupon'));
        },

        removeCoupon() { axios.post('{{ route("cart.discount.remove") }}').then(() => this.appliedDiscount = null); },
        
        placeOrder() {
            if(Object.keys(this.cart).length === 0) return;
            this.isPlacing = true;
            axios.post('{{ route("order.place") }}', { notes: this.notes }).then(response => location.reload()).catch(error => { alert(error.response.data.error || 'Something went wrong.'); this.isPlacing = false; });
        },

        updateCountdown() {
            if (!this.activeOrder || !this.activeOrder.estimated_completion_time) return;
            const target = new Date(this.activeOrder.estimated_completion_time).getTime();
            const now = new Date().getTime();
            const distance = target - now;
            if (distance < 0) { this.countdown.minutes = '00'; this.countdown.seconds = '00'; this.countdown.finished = true; return; }
            const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((distance % (1000 * 60)) / 1000);
            this.countdown.minutes = mins < 10 ? '0' + mins : mins;
            this.countdown.seconds = secs < 10 ? '0' + secs : secs;
            this.countdown.finished = false;
        },

        pollOrderStatus() {
            if (!this.activeOrder) return;
            axios.get('/order/status/' + this.activeOrder.order_number).then(response => { this.activeOrder.status = response.data.status; this.activeOrder.estimated_completion_time = response.data.estimated_completion_time; this.activeOrder.payment_status = response.data.payment_status; });
        }
    }
}
</script>
@endsection
@endsection
