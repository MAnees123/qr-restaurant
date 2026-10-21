<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\OperationsSetting;
use App\Models\Order;
use App\Services\Operations\OrderSettlement;
use Illuminate\Http\Request;

class OnlineOrderAdminController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function order(Order $order): Order
    {
        abort_unless($order->restaurant_id === $this->rid() && in_array($order->order_type, ['pickup', 'delivery']), 404);

        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::where('restaurant_id', $this->rid())->whereIn('order_type', ['pickup', 'delivery'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->with('orderItems.menuItem')->latest()->paginate(15)->withQueryString();
        $settings = OperationsSetting::forRestaurant($this->rid());

        return view('operations.online.admin', compact('orders', 'settings'));
    }

    public function settings(Request $request)
    {
        $request->validate(['online_ordering_open' => 'nullable|boolean']);
        OperationsSetting::forRestaurant($this->rid())->update(['online_ordering_open' => $request->boolean('online_ordering_open')]);

        return back()->with('success', 'Online ordering settings saved.');
    }

    public function accept(Order $order)
    {
        $this->order($order);
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be accepted.');
        }
        $order->update(['status' => 'confirmed']);

        return back()->with('success', "Order {$order->order_number} accepted.");
    }

    public function reject(Request $request, Order $order)
    {
        $this->order($order);
        $data = $request->validate(['reason' => 'nullable|string|max:190']);
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be rejected.');
        }
        $order->update(['status' => 'cancelled', 'rejection_reason' => $data['reason'] ?? null]);

        return back()->with('success', "Order {$order->order_number} rejected.");
    }

    public function status(Request $request, Order $order, OrderSettlement $settlement)
    {
        $this->order($order);
        $data = $request->validate([
            'status' => 'required|in:preparing,ready,served',
            'collect_cash' => 'nullable|boolean',
        ]);
        if (!in_array($order->status, ['confirmed', 'preparing', 'ready'])) {
            return back()->with('error', 'Accept the order before changing its status.');
        }
        if ($order->order_type === 'delivery' && $data['status'] === 'served') {
            return back()->with('error', 'Delivery orders are completed from the Delivery screen.');
        }
        $order->update(['status' => $data['status']]);
        if ($data['status'] === 'served' && $request->boolean('collect_cash')) {
            $settlement->markCashPaid($order, 'Paid in cash at pickup.');
        }

        return back()->with('success', "Order {$order->order_number} is now {$data['status']}.");
    }
}
