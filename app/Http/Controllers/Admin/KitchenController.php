<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id;
        
        $stats = [
            'pending' => Order::where('restaurant_id', $restaurantId)->where('status', 'pending')->count(),
            'preparing' => Order::where('restaurant_id', $restaurantId)->where('status', 'preparing')->count(),
            'ready' => Order::where('restaurant_id', $restaurantId)->where('status', 'ready')->count(),
        ];

        return view('kitchen.dashboard', compact('stats'));
    }

    public function statusView($status)
    {
        return view('kitchen.orders', compact('status'));
    }

    public function getOrders(Request $request)
    {
        $query = Order::where('restaurant_id', auth()->user()->restaurant_id)
            ->with(['table', 'orderItems.menuItem'])
            ->orderBy('created_at', 'asc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'preparing', 'ready']);
        }

        $orders = $query->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:preparing,ready,served',
        ]);

        $order->update(['status' => $request->status]);

        // Auto-release table management
        if ($order->table) {
            $newStatus = $request->status;

            if (in_array($newStatus, ['ready', 'served'])) {
                // Start 30-minute countdown — only if no other active orders on this table
                if (!$order->table->orders()
                    ->where('id', '!=', $order->id)
                    ->whereNotIn('status', ['ready', 'served', 'cancelled'])
                    ->exists()) {
                    $order->table->scheduleAutoRelease();
                }
            } elseif ($newStatus === 'preparing') {
                // Order went back to active state — cancel any pending release
                $order->table->update([
                    'status' => 'occupied',
                    'auto_release_at' => null,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
