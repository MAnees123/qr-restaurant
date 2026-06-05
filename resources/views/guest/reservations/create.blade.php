@extends('layouts.guest')

@section('title', 'Book a Table')

@section('content')
<div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-8 text-white rounded-b-[3rem] shadow-lg">
        <h1 class="text-3xl font-black mb-2">Book a Table</h1>
        <p class="text-amber-100 opacity-90">Plan your visit at {{ $restaurant->name }}</p>
    </div>

    <!-- Form -->
    <div class="p-6 -mt-8">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-center">
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('reservations.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="customer_name" required class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="Enter your name">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="customer_phone" required class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="03xx-xxxxxxx">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Date</label>
                        <input type="date" name="reservation_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Time</label>
                        <input type="time" name="reservation_time" required class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Number of Guests</label>
                    <select name="guests" required class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Person' : 'People' }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Special Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="Any special requests?"></textarea>
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-amber-200 transition transform active:scale-95">
                    REQUEST RESERVATION
                </button>
            </form>
        </div>
    </div>
    
    <div class="px-6 py-4 text-center">
        <a href="/" class="text-slate-400 font-medium hover:text-amber-500">Back to Home</a>
    </div>
</div>
@endsection
