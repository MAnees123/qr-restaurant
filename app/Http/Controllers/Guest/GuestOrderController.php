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
        $request->validate([
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|in:safepay,bitcoin',
            'payment_status' => 'nullable|string|in:pending,paid',
            'transaction_id' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

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

        // Build notes: read from request first, fallback to session
        $specialInstruction = trim(strip_tags($request->input('notes', '') ?: session('order_notes', '')));

        $orderNotes = '';
        $address = $request->address ? trim(preg_replace('/^[\s,]+|[\s,]+$/', '', $request->address)) : '';
        if ($address && $address !== ',') {
            $orderNotes .= "Address: " . strip_tags($address) . "\n";
        }
        if ($specialInstruction) {
            $orderNotes .= $specialInstruction;
        }

        $order = Order::create([
            'restaurant_id' => $restaurantId,
            'table_id' => $tableId,
            'order_number' => $orderNumber,
            'guest_token' => $request->guest_token,
            'status' => 'pending',
            'payment_status' => $request->input('payment_status', 'pending'),
            'payment_method' => $request->input('payment_method'),
            'subtotal' => $subtotal,
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'notes' => trim($orderNotes),
            'estimated_completion_time' => now()->addMinutes($maxPrepTime),
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'special_instructions' => $item['special_instructions'] ?? null,
            ]);
        }

        if ($order->payment_status === 'paid') {
            \App\Models\Payment::create([
                'restaurant_id' => $restaurantId,
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => $request->input('payment_method'),
                'transaction_id' => $request->input('transaction_id'),
                'status' => 'paid',
                'notes' => 'Online payment completed successfully via guest checkout.',
            ]);
        }

        session()->forget('cart');
        session()->forget('discount');
        session()->forget('order_notes');
        session(['active_order_number' => $orderNumber]);

        // Update table status to occupied
        Table::where('id', $tableId)->update(['status' => 'occupied']);

        // We return JSON so Alpine.js can redirect
        return response()->json([
            'message' => 'Order placed successfully',
            'redirect' => route('order.confirmed', $orderNumber)
        ]);
    }

    public function confirmed(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('guest_token', $request->guest_token)
            ->with(['orderItems.menuItem', 'table.qrCode'])
            ->firstOrFail();
        
        return view('guest.order-confirmed', compact('order'));
    }

    public function status(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('guest_token', $request->guest_token)
            ->firstOrFail();
            
        return response()->json([
            'status' => $order->status,
            'estimated_completion_time' => $order->estimated_completion_time ? $order->estimated_completion_time->toIso8601String() : null,
            'payment_status' => $order->payment_status,
        ]);
    }

    public function activeOrders(Request $request)
    {
        $token = $request->guest_token;
        if (!$token) {
            return response()->json([]);
        }

        // Return orders within the last 3 hours
        $orders = Order::where('guest_token', $token)
            ->where('created_at', '>=', now()->subHours(3))
            ->with(['orderItems.menuItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function paymentForm($code)
    {
        $qrCode = \App\Models\QrCode::where('code', $code)->with('table.restaurant')->firstOrFail();
        $table = $qrCode->table;
        $restaurant = $table->restaurant;

        if (!$table->is_active) {
            abort(404, 'Table #' . $table->table_number . ' is currently inactive.');
        }

        if (!$restaurant->is_active) {
            abort(404, 'Restaurant is currently unavailable.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.show', $code)->with('error', 'Cart is empty');
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
            $discountAmount = min($discountAmount, $subtotal);
        }

        $totalAmount = $subtotal - $discountAmount;

        return view('guest.payment', compact('restaurant', 'table', 'code', 'subtotal', 'discountAmount', 'totalAmount'));
    }
}
