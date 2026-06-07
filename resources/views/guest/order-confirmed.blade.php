@extends('layouts.guest')
@section('title', 'Order Confirmed')

@section('content')
@php
    $cart = session()->get('cart', []);
    $cartCount = 0;
    foreach ($cart as $item) {
        $cartCount += $item['quantity'];
    }
    $hasActiveOrder = session()->has('active_order_number');
    $code = $order->table->qrCode->code;
@endphp
<div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative pb-24" x-data="orderConfirmedPage()">
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-amber-50 pb-20">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full text-center">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Order Confirmed!</h1>
        
        <!-- Preparation Countdown -->
        <div class="my-8" x-data="countdown('{{ $order->estimated_completion_time->toIso8601String() }}')" x-init="init()">
            <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Estimated Prep Time</p>
            <div class="flex justify-center gap-3">
                <div class="bg-slate-900 text-white rounded-2xl p-4 w-20 shadow-xl border-b-4 border-amber-500">
                    <p class="text-3xl font-black" x-text="minutes">00</p>
                    <p class="text-[10px] uppercase font-black text-slate-500">Min</p>
                </div>
                <div class="bg-slate-900 text-white rounded-2xl p-4 w-20 shadow-xl border-b-4 border-amber-500">
                    <p class="text-3xl font-black" x-text="seconds">00</p>
                    <p class="text-[10px] uppercase font-black text-slate-500">Sec</p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-center gap-2">
                <span class="relative flex h-2 w-2" x-show="!finished">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest" x-text="finished ? 'Your order is almost ready!' : 'Chef is preparing your meal...'"></p>
            </div>
        </div>
        
        <div class="bg-stone-50 p-4 rounded-2xl mb-8 border border-stone-100">
            <p class="text-sm text-slate-500 uppercase font-semibold mb-1">Order Number</p>
            <p class="text-3xl font-bold tracking-widest text-amber-600">{{ $order->order_number }}</p>
        </div>
        
        <div class="text-left border-t border-stone-100 pt-6">
            <h3 class="font-bold text-slate-800 mb-4">Order Details</h3>
            <div class="space-y-3 mb-6">
                @foreach($order->orderItems as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                        <span class="font-medium text-slate-800">PKR {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>

            @if($order->notes)
            <div class="bg-amber-50 rounded-xl p-3 mb-4 border border-amber-100">
                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1">
                    <i class="fas fa-clipboard-list mr-1"></i> Special Instructions
                </p>
                <p class="text-xs text-amber-800 font-medium whitespace-pre-wrap">{{ $order->notes }}</p>
            </div>
            @endif
            
            <div class="flex justify-between items-center border-t border-stone-100 pt-4">
                <span class="font-bold text-slate-800">Total</span>
                <span class="font-bold text-xl text-amber-600">PKR {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Nav -->
<div class="bottom-nav">
    <button class="nav-item" @click="window.location.href = '{{ route('menu.show', $code) }}#restaurant'">
        <i class="fas fa-utensils"></i>
        <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Menu</span>
    </button>
    <button class="nav-item" @click="callWaiter()" :disabled="isCalling">
        <i class="fas fa-bell text-white" :class="isCalling ? 'animate-bounce' : ''"></i>
        <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Waiter</span>
    </button>
    <button class="nav-item relative" @click="window.location.href = '{{ route('menu.show', $code) }}#cart'">
        <i class="fas fa-shopping-bag text-white"></i>
        <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Basket</span>
        @if($cartCount > 0)
        <span
            class="bg-[#e8890c] text-white text-[8px] font-black rounded-full flex items-center justify-center absolute -top-0.5 -right-0.5"
            style="min-width:16px;height:16px;padding:0 3px;">{{ $cartCount }}</span>
        @endif
    </button>
    <button class="nav-item active" @click="window.location.href = '{{ route('menu.show', $code) }}#order'">
        <i class="fas fa-receipt text-[#e8890c]"></i>
        <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#e8890c">Order</span>
        @if($hasActiveOrder)
        <span class="bg-red-500 text-white text-[8px] font-black rounded-full absolute -top-0.5 -right-0.5"
            style="min-width:8px;height:8px;"></span>
        @endif
    </button>
</div>

<!-- Toast Notifications -->
<div class="toast" id="toast" :class="toastShow ? 'show' : ''" x-text="toastMessage"></div>

</div>

<style>
/* Bottom Nav */
.bottom-nav {
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    bottom: 0;
    width: 100%;
    max-width: 448px; /* max-w-md is 448px */
    background: #1c1c1c;
    border-top: 1px solid #2a2a2a;
    display: flex;
    justify-content: space-around;
    padding: 10px 0 14px;
    z-index: 120;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    position: relative;
}

.nav-item i {
    font-size: 20px;
    color: #fff;
}

.nav-item.active i {
    color: #e8890c;
}

/* toast message */
.toast {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%) translateY(16px);
    background: #e8890c;
    color: #fff;
    padding: 10px 22px;
    border-radius: 50px;
    font-size: 12.5px;
    font-weight: 600;
    font-family: "Poppins", sans-serif;
    opacity: 0;
    pointer-events: none;
    transition:
        opacity 0.28s,
        transform 0.28s;
    z-index: 999;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}
.toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}
</style>
@endsection

@section('scripts')
<script>
function orderConfirmedPage() {
    return {
        isCalling: false,
        toastShow: false,
        toastMessage: '',
        showToast(msg) {
            this.toastMessage = msg;
            this.toastShow = true;
            setTimeout(() => this.toastShow = false, 2500);
        },
        callWaiter() {
            if (this.isCalling) return;
            this.isCalling = true;
            axios.post('{{ route('table.call') }}')
                .then(response => {
                    this.isCalling = false;
                    this.showToast('🔔 Waiter has been requested!');
                })
                .catch(error => {
                    this.showToast(error.response?.data?.error || 'Something went wrong.');
                    this.isCalling = false;
                });
        }
    }
}

function countdown(targetTime) {
    return {
        target: new Date(targetTime).getTime(),
        minutes: '00',
        seconds: '00',
        finished: false,
        init() {
            this.update();
            setInterval(() => this.update(), 1000);
        },
        update() {
            const now = new Date().getTime();
            const distance = this.target - now;

            if (distance < 0) {
                this.minutes = '00';
                this.seconds = '00';
                this.finished = true;
                return;
            }

            const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((distance % (1000 * 60)) / 1000);

            this.minutes = mins < 10 ? '0' + mins : mins;
            this.seconds = secs < 10 ? '0' + secs : secs;
        }
    }
}
</script>
@endsection
