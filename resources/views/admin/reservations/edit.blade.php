@extends('layouts.admin')

@section('header', 'Edit Reservation')

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden max-w-3xl">
    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-black text-slate-800">Reservation Details</h2>
            <p class="text-sm text-slate-400 font-bold mt-1">Update advance table booking info.</p>
        </div>
        <span class="px-3 py-1 text-xs font-black uppercase tracking-widest rounded-full 
            {{ $reservation->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
            {{ $reservation->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
            {{ $reservation->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
            {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
            {{ $reservation->status === 'cancelled' ? 'bg-slate-100 text-slate-600' : '' }}">
            {{ $reservation->status }}
        </span>
    </div>

    <div class="p-8">
        <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Customer Details -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Customer Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
            </div>

            <!-- Booking Date, Time, Guests -->
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Time <span class="text-red-500">*</span></label>
                    <input type="time" name="reservation_time" value="{{ old('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i')) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Guests <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="guests" value="{{ old('guests', $reservation->guests) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
            </div>

            <!-- Assignment and Event Type -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Assign Table (Optional)</label>
                    <select name="table_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                        <option value="">-- No specific table --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                Table {{ $table->table_number }} (Seats {{ $table->seating_capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Event Type (Optional)</label>
                    <input type="text" name="event_type" value="{{ old('event_type', $reservation->event_type) }}" placeholder="e.g. Birthday, Anniversary"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Booking Notes / Reason</label>
                <textarea name="notes" rows="3" placeholder="Any special arrangements like cake, balloons, or specific dietary requirements?"
                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">{{ old('notes', $reservation->notes) }}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Reservation Status <span class="text-red-500">*</span></label>
                <select name="status" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition" required>
                    <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Completed (Arrived)</option>
                    <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rejected" {{ old('status', $reservation->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.reservations.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">Update Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection
