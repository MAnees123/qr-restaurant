<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('reservation_date', 'asc')
            ->orderBy('reservation_time', 'asc')
            ->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        if ($reservation->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,rejected,completed,cancelled',
        ]);

        $reservation->update(['status' => $request->status]);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated.');
    }
}
