<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.restaurant.edit', auth()->user()->restaurant_id);
    }

    public function edit(Restaurant $restaurant)
    {
        if ($restaurant->id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        return view('admin.restaurant.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        if ($restaurant->id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = $restaurant->logo;
        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('restaurant_logos', 'public');
        }

        $restaurant->update([
            'name' => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.restaurant.edit', $restaurant)->with('success', 'Restaurant settings updated successfully.');
    }

    public function generateMenuPDF()
    {
        $restaurant = auth()->user()->restaurant;
        $categories = \App\Models\MenuCategory::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => function($query) {
                $query->where('is_available', true);
            }])
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.menu-pdf', compact('restaurant', 'categories'));
        
        return $pdf->stream($restaurant->name . '-Menu.pdf');
    }
}
