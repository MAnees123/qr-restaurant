<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('restaurant_id', auth()->user()->restaurant_id)
            ->with(['table', 'payments'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);
            
        return view('admin.orders.index', compact('orders'));
    }

    public function liveSearch(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $orders = Order::where('restaurant_id', auth()->user()->restaurant_id)
            ->with('table')
            ->where(function($q) use ($query) {
                $q->where('order_number', 'LIKE', "%{$query}%")
                  ->orWhereHas('table', function($sq) use ($query) {
                      $sq->where('table_number', 'LIKE', "%{$query}%");
                  })
                  ->orWhere('created_at', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'table_number' => $order->table ? $order->table->table_number : 'N/A',
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'date' => $order->created_at->format('M d, h:i A'),
                    'url' => route('admin.orders.show', $order)
                ];
            });

        return response()->json($orders);
    }

    public function search(Request $request)
    {
        $request->validate(['order_number' => 'required|string']);
        
        $order = Order::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('order_number', $request->order_number)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        return redirect()->route('admin.orders.show', $order);
    }

    public function show(Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        $order->load(['table', 'orderItems.menuItem', 'payments']);
        
        if (request()->wantsJson()) {
            return response()->json($order);
        }
        
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'nullable|in:pending,confirmed,preparing,ready,served,cancelled',
            'payment_status' => 'nullable|in:pending,paid,cancelled',
        ]);

        if ($request->has('status')) {
            $order->update(['status' => $request->status]);

            // Auto-release table management
            if ($order->table) {
                $newStatus = $request->status;

                if (in_array($newStatus, ['ready', 'served'])) {
                    // Start 30-minute countdown — but only if no other active orders on this table
                    if (!$order->table->orders()
                        ->where('id', '!=', $order->id)
                        ->whereNotIn('status', ['ready', 'served', 'cancelled'])
                        ->exists()) {
                        $order->table->scheduleAutoRelease();
                    }
                } elseif (in_array($newStatus, ['pending', 'preparing'])) {
                    // Order went back to active state — cancel any pending release and keep occupied
                    $order->table->update([
                        'status' => 'occupied',
                        'auto_release_at' => null,
                    ]);
                } elseif ($newStatus === 'cancelled') {
                    // If cancelling and no other active orders, free the table immediately
                    if (!$order->table->orders()
                        ->where('id', '!=', $order->id)
                        ->whereNotIn('status', ['served', 'cancelled'])
                        ->exists()) {
                        $order->table->update([
                            'status' => 'free',
                            'auto_release_at' => null,
                        ]);
                    }
                }
            }
        }

        if ($request->has('payment_status')) {
            $order->update(['payment_status' => $request->payment_status]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'order' => $order]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
    public function recordPayment(Request $request, Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'method' => 'required|in:cash,jazzcash,easypaisa,card,safepay,bitcoin',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \App\Models\Payment::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id' => $order->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'transaction_id' => $request->transaction_id,
            'status' => 'paid',
            'notes' => $request->notes,
        ]);

        $order->update(['payment_status' => 'paid']);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Payment recorded and order locked.');
    }

    public function print(Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $order->load(['table', 'orderItems.menuItem', 'restaurant']);
        return view('admin.orders.print', compact('order'));
    }
}
