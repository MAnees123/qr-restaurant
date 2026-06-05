@extends('layouts.admin')

@section('header', 'Table Reservations')

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Customer</th>
                <th class="px-6 py-3 font-medium">Date & Time</th>
                <th class="px-6 py-3 font-medium">Guests</th>
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
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($res->reservation_time)->format('h:i A') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ $res->guests }} Guests</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-black uppercase rounded-full
                            {{ $res->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $res->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $res->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $res->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ $res->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            @if($res->status === 'pending')
                                <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg" title="Confirm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            @elseif($res->status === 'confirmed')
                                <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button class="px-3 py-1 text-xs font-bold bg-blue-500 text-white rounded-lg hover:bg-blue-600">MARK ARRIVED</button>
                                </form>
                            @endif
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
