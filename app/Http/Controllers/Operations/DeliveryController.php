<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\OperationsSetting;
use App\Models\Order;
use App\Services\Operations\OrderSettlement;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function order(Order $order): Order
    {
        abort_unless($order->restaurant_id === $this->rid() && $order->order_type === 'delivery', 404);

        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::where('restaurant_id', $this->rid())->where('order_type', 'delivery')
            ->when($request->filled('delivery_status'), fn ($q) => $q->where('delivery_status', $request->delivery_status))
            ->where('status', '!=', 'cancelled')
            ->with('orderItems.menuItem')->latest()->paginate(15)->withQueryString();
        $settings = OperationsSetting::forRestaurant($this->rid());

        return view('operations.delivery.index', compact('orders', 'settings'));
    }

    public function settings(Request $request)
    {
        $data = $request->validate([
            'delivery_fee' => 'required|numeric|min:0|max:100000',
            'delivery_min_order' => 'required|numeric|min:0|max:1000000',
        ]);
        OperationsSetting::forRestaurant($this->rid())->update($data);

        return back()->with('success', 'Delivery settings saved.');
    }

    public function assign(Request $request, Order $order)
    {
        $this->order($order);
        $data = $request->validate([
            'driver_name' => 'required|string|max:120',
            'driver_phone' => 'nullable|string|max:30',
        ]);
        if ($order->delivery_status === 'delivered' || $order->status === 'cancelled') {
            return back()->with('error', 'This delivery is already closed.');
        }
        $order->update($data + ['delivery_status' => $order->delivery_status === 'out' ? 'out' : 'assigned']);

        return back()->with('success', "Driver assigned to order {$order->order_number}.");
    }

    public function status(Request $request, Order $order, OrderSettlement $settlement)
    {
        $this->order($order);
        $data = $request->validate(['delivery_status' => 'required|in:out,delivered', 'collect_cash' => 'nullable|boolean']);

        if (!$order->driver_name) {
            return back()->with('error', 'Assign a driver first.');
        }
        if ($order->delivery_status === 'delivered') {
            return back()->with('error', 'This delivery is already completed.');
        }
        if ($data['delivery_status'] === 'out') {
            $order->update(['delivery_status' => 'out']);
            if ($order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
            }
        } else {
            $order->update(['delivery_status' => 'delivered', 'delivered_at' => now(), 'status' => 'served']);
            if ($request->boolean('collect_cash')) {
                $settlement->markCashPaid($order, 'Cash on delivery collected by ' . $order->driver_name . '.');
            }
        }

        return back()->with('success', "Order {$order->order_number} marked {$data['delivery_status']}.");
    }
}
