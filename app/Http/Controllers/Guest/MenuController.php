<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Order;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function show($code)
    {
        $qrCode = QrCode::where('code', $code)->with('table.restaurant')->firstOrFail();
        $table = $qrCode->table;
        $restaurant = $table->restaurant;

        if (!$table->is_active) {
            abort(404, 'Table #' . $table->table_number . ' is currently inactive.');
        }

        if (!$restaurant->is_active) {
            abort(404, 'Restaurant is currently unavailable.');
        }

        $categories = MenuCategory::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('is_available', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Save table id in session for cart and order processing
        session(['current_table_id' => $table->id, 'current_restaurant_id' => $restaurant->id]);

        // Get active order from session if exists
        $activeOrder = null;
        if (session()->has('active_order_number')) {
            $activeOrder = Order::where('order_number', session('active_order_number'))
                ->with('orderItems.menuItem')
                ->first();
        }

        // Fetch active banners
        $banners = \App\Models\Banner::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest.menu', compact('restaurant', 'table', 'categories', 'activeOrder', 'code', 'banners'));
    }
}
