<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestOrderController extends Controller
{
    public function place(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $restaurantId = session('current_restaurant_id');
        $tableId = session('current_table_id');

        if (!$restaurantId || !$tableId) {
            return response()->json(['error' => 'Session expired. Please scan QR code again.'], 400);
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discountAmount = 0;
        $couponCode = null;

        if (session()->has('discount')) {
            $discountData = session('discount');
            $couponCode = $discountData['code'];
            
            if ($discountData['type'] === 'percentage') {
                $discountAmount = $subtotal * ($discountData['value'] / 100);
            } else {
                $discountAmount = $discountData['value'];
            }

            // Cap discount at subtotal
            $discountAmount = min($discountAmount, $subtotal);

            // Increment discount usage
            \App\Models\Discount::where('code', $couponCode)->increment('used_count');
        }

        $totalAmount = $subtotal - $discountAmount;
        $orderNumber = strtoupper(Str::random(8));

        $maxPrepTime = 15; // Default 15 mins
        $cartItemIds = array_column($cart, 'id');
        $prepTimes = \App\Models\MenuItem::whereIn('id', $cartItemIds)->pluck('preparation_time')->toArray();
        if (!empty($prepTimes)) {
            $maxPrepTime = max($prepTimes);
        }

        $order = Order::create([
            'restaurant_id' => $restaurantId,
            'table_id' => $tableId,
            'order_number' => $orderNumber,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
            'estimated_completion_time' => now()->addMinutes($maxPrepTime),
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'special_instructions' => $item['special_instructions'],
            ]);
        }

        session()->forget('cart');
        session()->forget('discount');
        session(['active_order_number' => $orderNumber]);

        // Update table status to occupied
        Table::where('id', $tableId)->update(['status' => 'occupied']);

        // We return JSON so Alpine.js can redirect
        return response()->json([
            'message' => 'Order placed successfully',
            'redirect' => route('order.confirmed', $orderNumber)
        ]);
    }

    public function confirmed($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('orderItems.menuItem')->firstOrFail();
        
        return view('guest.order-confirmed', compact('order'));
    }

    public function status($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return response()->json([
            'status' => $order->status,
            'estimated_completion_time' => $order->estimated_completion_time ? $order->estimated_completion_time->toIso8601String() : null,
            'payment_status' => $order->payment_status,
        ]);
    }
}
