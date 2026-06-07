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
            ->with('table')
            ->orderBy('reservation_date', 'asc')
            ->orderBy('reservation_time', 'asc')
            ->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $tables = \App\Models\Table::where('restaurant_id', auth()->user()->restaurant_id)->get();
        return view('admin.reservations.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'guests' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
            'event_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,rejected,completed,cancelled',
        ]);

        Reservation::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'guests' => $request->guests,
            'table_id' => $request->table_id,
            'event_type' => $request->event_type,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation)
    {
        if ($reservation->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        $tables = \App\Models\Table::where('restaurant_id', auth()->user()->restaurant_id)->get();
        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        if ($reservation->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'guests' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
            'event_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,rejected,completed,cancelled',
        ]);

        $reservation->update($request->only([
            'customer_name', 'customer_phone', 'reservation_date', 'reservation_time', 
            'guests', 'table_id', 'event_type', 'notes', 'status'
        ]));

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Reservation deleted.');
    }
}
