<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Restaurant;

class GuestReservationController extends Controller
{
    public function create()
    {
        // For simplicity, we just pick the first restaurant if none is in session
        // In a real SaaS, this would be based on the URL or subdomain.
        $restaurant = Restaurant::first();
        return view('guest.reservations.create', compact('restaurant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'guests' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        Reservation::create($request->all());

        return redirect()->back()->with('success', 'Your reservation request has been sent! We will contact you soon.');
    }
}
