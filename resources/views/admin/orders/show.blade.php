@extends('layouts.admin')

@section('header', 'Order #' . $order->order_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Order Overview Card -->
        <div class="bg-slate-900 rounded-[2rem] shadow-xl p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8">
                <svg class="w-32 h-32 text-white/5 absolute -right-8 -top-8" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-2">Primary Details</h3>
                        <h1 class="text-4xl font-black italic">{{ $order->order_number }}</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Items</p>
                        <p class="text-4xl font-black text-emerald-400">{{ $order->orderItems->sum('quantity') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div class="bg-white/5 rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Order Status</p>
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest
                            {{ $order->status === 'pending' ? 'bg-amber-500/20 text-amber-400' : '' }}
                            {{ $order->status === 'preparing' ? 'bg-blue-500/20 text-blue-400' : '' }}
                            {{ $order->status === 'ready' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                            {{ $order->status === 'served' ? 'bg-slate-500/20 text-slate-400' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>

                    <div class="bg-white/5 rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Payment Status</p>
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg tracking-widest
                            {{ $order->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </div>

                    <div class="bg-white/5 rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Method</p>
                        <p class="text-sm font-black text-white uppercase tracking-widest">
                            {{ $order->payments->last()->method ?? 'PENDING' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-[2rem] shadow-sm border p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex justify-between items-center">
                Order Items
                <span class="text-xs font-black bg-slate-100 text-slate-500 px-3 py-1 rounded-lg">KOT READY</span>
            </h3>
            <div class="divide-y divide-slate-100">
                @foreach($order->orderItems as $item)
                    <div class="py-4 flex justify-between items-center group">
                        <div class="flex gap-4 items-center">
                            <span class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-800 text-sm">{{ $item->quantity }}x</span>
                            <div>
                                <p class="font-bold text-slate-800 group-hover:text-emerald-600 transition">{{ $item->menuItem->name ?? 'Deleted Item' }}</p>
                                @if($item->special_instructions)
                                    <p class="text-xs text-red-500 font-bold italic mt-1 bg-red-50 px-2 py-0.5 rounded-lg inline-block">Note: {{ $item->special_instructions }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-slate-800">Rs {{ number_format($item->subtotal) }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">@ Rs {{ number_format($item->unit_price) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="mt-8 pt-8 border-t space-y-3">
                <div class="flex justify-between text-sm font-bold text-slate-500">
                    <span>Subtotal</span>
                    <span>Rs {{ number_format($order->subtotal) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm font-bold text-red-500">
                    <span>Discount ({{ $order->coupon_code }})</span>
                    <span>- Rs {{ number_format($order->discount_amount) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-2xl font-black text-slate-800 pt-2 border-t border-dashed">
                    <span>Total Bill</span>
                    <span class="text-emerald-600">Rs {{ number_format($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Special Instructions Section (always visible) -->
        <div class="rounded-[2rem] border-2 p-8 {{ $order->notes ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-100' }}">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4 flex items-center gap-2 {{ $order->notes ? 'text-amber-800' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                Customer Special Instructions
            </h3>
            @if($order->notes)
                <div class="bg-white rounded-xl p-4 border border-amber-100">
                    <p class="text-slate-700 font-medium leading-relaxed whitespace-pre-wrap">{{ $order->notes }}</p>
                </div>
            @else
                <p class="text-slate-400 italic text-sm">No special instructions provided.</p>
            @endif
        </div>
    </div>

    <!-- Sidebar Controls -->
    <div class="space-y-8">
        <!-- Print Button -->
        <div class="bg-slate-900 rounded-[2rem] shadow-sm p-8 text-white">
            <h3 class="text-xs font-black text-emerald-400 uppercase tracking-widest mb-4">Print Operations</h3>
            <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="flex items-center justify-center gap-3 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-emerald-900/20 uppercase tracking-widest text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Bill
            </a>
        </div>

        <!-- Status Management -->
        <div class="bg-white rounded-[2rem] shadow-sm border p-8">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Workflow Control</h3>
            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Order Status</label>
                    <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 font-bold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>🕒 Pending</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>🍳 Preparing</option>
                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>🛎️ Ready to Serve</option>
                        <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>✅ Served</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Payment Management -->
        <div class="bg-white rounded-[2rem] shadow-sm border p-8 overflow-hidden relative">
            @if($order->payment_status === 'paid')
                <div class="absolute top-4 right-4 bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Fully Paid</div>
            @endif
            
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Settlement</h3>
            
            @if($order->payment_status !== 'paid')
            <form action="{{ route('admin.orders.payment', $order) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="amount" value="{{ $order->total_amount }}">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Payment Method</label>
                    <select name="method" required class="w-full rounded-xl border-slate-200 font-bold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="cash">💵 Cash Payment</option>
                        <option value="jazzcash">📱 JazzCash</option>
                        <option value="easypaisa">📱 Easypaisa</option>
                        <option value="card">💳 Debit/Credit Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Transaction ID (Optional)</label>
                    <input type="text" name="transaction_id" class="w-full rounded-xl border-slate-200 font-bold" placeholder="E.G. TRX-12345">
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-emerald-200 uppercase tracking-widest text-sm mt-4">Record Payment</button>
            </form>
            @else
                <div class="space-y-4">
                    @foreach($order->payments as $payment)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $payment->method }}</span>
                            <span class="text-xs font-black text-emerald-600">Rs {{ number_format($payment->amount) }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-600">Ref: {{ $payment->transaction_id ?? 'N/A' }}</p>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $payment->created_at->format('h:i A, d M') }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Table Info -->
        <div class="bg-slate-900 rounded-[2rem] shadow-sm p-8 text-white">
            <h3 class="text-xs font-black text-emerald-400 uppercase tracking-widest mb-4">Service Details</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                    <span class="text-sm font-bold text-slate-400">Table</span>
                    <span class="text-2xl font-black text-white">TBL-{{ $order->table ? $order->table->table_number : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-400">Order Placed</span>
                    <span class="text-sm font-black">{{ $order->created_at->format('h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
