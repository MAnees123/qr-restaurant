@extends('layouts.admin')

@section('header', 'Table Reservations')

@section('header_actions')
    <a href="{{ route('admin.reservations.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Reservation
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Customer & Event</th>
                <th class="px-6 py-3 font-medium">Date & Time</th>
                <th class="px-6 py-3 font-medium">Table & Guests</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($reservations as $res)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800">{{ $res->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $res->customer_phone }}</p>
                        @if($res->event_type)
                            <span class="inline-block mt-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded">{{ $res->event_type }}</span>
                        @endif
                        @if($res->notes)
                            <p class="text-[10px] text-slate-400 mt-1 italic max-w-xs truncate">{{ $res->notes }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($res->reservation_time)->format('h:i A') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-start gap-1">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ $res->guests }} Guests</span>
                            @if($res->table)
                                <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest">Table {{ $res->table->table_number }}</span>
                            @else
                                <span class="text-[10px] text-slate-400 font-bold uppercase">No table</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-black uppercase rounded-full
                            {{ $res->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $res->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $res->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $res->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $res->status === 'cancelled' ? 'bg-slate-100 text-slate-600' : '' }}">
                            {{ $res->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <!-- Quick Status Actions -->
                            @if($res->status === 'pending')
                                <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <!-- Keep hidden inputs for other required fields so they don't get overwritten -->
                                    <input type="hidden" name="customer_name" value="{{ $res->customer_name }}">
                                    <input type="hidden" name="customer_phone" value="{{ $res->customer_phone }}">
                                    <input type="hidden" name="reservation_date" value="{{ $res->reservation_date }}">
                                    <input type="hidden" name="reservation_time" value="{{ $res->reservation_time }}">
                                    <input type="hidden" name="guests" value="{{ $res->guests }}">
                                    <button class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Confirm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <input type="hidden" name="customer_name" value="{{ $res->customer_name }}">
                                    <input type="hidden" name="customer_phone" value="{{ $res->customer_phone }}">
                                    <input type="hidden" name="reservation_date" value="{{ $res->reservation_date }}">
                                    <input type="hidden" name="reservation_time" value="{{ $res->reservation_time }}">
                                    <input type="hidden" name="guests" value="{{ $res->guests }}">
                                    <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            @endif

                            <div class="h-6 border-l border-slate-200 mx-1"></div>

                            <!-- Edit -->
                            <a href="{{ route('admin.reservations.edit', $res) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('admin.reservations.destroy', $res) }}" method="POST" onsubmit="return confirm('Delete this reservation?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">No reservations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $reservations->links() }}
</div>
@endsection
