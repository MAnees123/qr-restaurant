@extends('layouts.admin')

@section('header', 'Create New Reservation')

@section('content')
<div class="card overflow-hidden max-w-3xl">
    <div class="px-6 py-5 border-b border-slate-100">
        <h2 class="page-title">Reservation Details</h2>
        <p class="page-subtitle">Add a new advance table booking.</p>
    </div>

    <div class="p-6">
        <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Customer Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                        class="input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Customer Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required
                        class="input">
                </div>
            </div>

            <!-- Booking Date, Time, Guests -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="reservation_date" value="{{ old('reservation_date') }}" required
                        class="input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Time <span class="text-red-500">*</span></label>
                    <input type="time" name="reservation_time" value="{{ old('reservation_time') }}" required
                        class="input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Guests <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="guests" value="{{ old('guests', 2) }}" required
                        class="input">
                </div>
            </div>

            <!-- Assignment and Event Type -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Assign Table (Optional)</label>
                    <select name="table_id" class="input">
                        <option value="">-- No specific table --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                Table {{ $table->table_number }} (Seats {{ $table->seating_capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Event Type (Optional)</label>
                    <input type="text" name="event_type" value="{{ old('event_type') }}" placeholder="e.g. Birthday, Anniversary"
                        class="input">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Booking Notes / Reason</label>
                <textarea name="notes" rows="3" placeholder="Any special arrangements like cake, balloons, or specific dietary requirements?"
                    class="input">{{ old('notes') }}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Reservation Status <span class="text-red-500">*</span></label>
                <select name="status" class="input" required>
                    <option value="pending" {{ old('status', 'confirmed') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed (Arrived)</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection
