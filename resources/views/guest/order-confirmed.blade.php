@extends('layouts.guest')
@section('title', 'Order Confirmed')

@section('content')
<div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative">
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-amber-50">
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
            
            <div class="flex justify-between items-center border-t border-stone-100 pt-4">
                <span class="font-bold text-slate-800">Total</span>
                <span class="font-bold text-xl text-amber-600">PKR {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</div>
</div>
@section('scripts')
<script>
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
