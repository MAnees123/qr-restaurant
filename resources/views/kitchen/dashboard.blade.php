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

@endsection
