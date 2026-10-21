<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OperationsSetting;
use App\Models\Payment;
use App\Services\Operations\CashSummary;
use App\Services\Operations\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashierController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function order(Order $order): Order
    {
        abort_unless($order->restaurant_id === $this->rid(), 404);

        return $order;
    }

    public function index(Request $request, CashSummary $summary)
    {
        $orders = Order::where('restaurant_id', $this->rid())
            ->where('payment_status', '!=', 'paid')->where('status', '!=', 'cancelled')
            ->when($request->filled('q'), fn ($q) => $q->where('order_number', 'like', '%' . $request->q . '%'))
            ->with('table')->latest()->paginate(20)->withQueryString();

        return view('operations.cashier.index', ['orders' => $orders, 'summary' => $summary->forDay($this->rid(), null)]);
    }

    public function summary(Request $request, CashSummary $summary)
    {
        $request->validate(['date' => 'nullable|date']);

        return view('operations.cashier.summary', ['summary' => $summary->forDay($this->rid(), $request->date), 'layout' => 'operations.layout']);
    }

    public function show(Order $order)
    {
        $this->order($order)->load(['table', 'orderItems.menuItem', 'payments']);
        $loyalty = app(LoyaltyService::class)->isActive(auth()->user()->restaurant)
            ? OperationsSetting::forRestaurant($this->rid()) : null;

        return view('operations.cashier.show', compact('order', 'loyalty'));
    }

    public function pay(Request $request, Order $order)
    {
        $this->order($order);
        $data = $request->validate([
            'method' => 'required|in:cash,card,jazzcash,easypaisa,safepay,bitcoin',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'tendered' => 'nullable|numeric|min:0',
            'customer_phone' => 'nullable|string|max:30',
            'customer_name' => 'nullable|string|max:120',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        $loyalty = app(LoyaltyService::class);

        DB::transaction(function () use ($order, $data, $loyalty) {
            $order = Order::where('id', $order->id)->lockForUpdate()->first();
            if ($order->payment_status === 'paid' || $order->status === 'cancelled') {
                throw ValidationException::withMessages(['order' => 'This order is already paid or cancelled.']);
            }

            $phone = LoyaltyService::normalizePhone($data['customer_phone'] ?? null) ?: $order->customer_phone;
            $points = (int) ($data['redeem_points'] ?? 0);
            [$points, $discount] = $loyalty->quoteRedeem($order->restaurant, $phone, $points, (float) $order->total_amount);

            $update = ['customer_phone' => $phone];
            if (!empty($data['customer_name'])) {
                $update['customer_name'] = $data['customer_name'];
            }
            if ($discount > 0) {
                $update['discount_amount'] = (float) $order->discount_amount + $discount;
                $update['total_amount'] = (float) $order->total_amount - $discount;
            }
            $order->update($update);
            if ($points > 0) {
                $loyalty->commitRedeem($order, $phone, $points);
            }

            $total = (float) $order->total_amount;
            $notes = $data['notes'] ?? '';
            if ($data['method'] === 'cash' && isset($data['tendered']) && $data['tendered'] !== '') {
                if ((float) $data['tendered'] < $total) {
                    throw ValidationException::withMessages(['tendered' => 'Cash tendered is less than the amount due (' . number_format($total, 2) . ').']);
                }
                $notes = trim($notes . ' Tendered ' . number_format((float) $data['tendered'], 2) . ', change ' . number_format((float) $data['tendered'] - $total, 2));
            }

            Payment::create([
                'restaurant_id' => $order->restaurant_id, 'order_id' => $order->id, 'amount' => $total,
                'method' => $data['method'], 'transaction_id' => $data['transaction_id'] ?? null,
                'status' => 'paid', 'notes' => $notes ?: null,
            ]);
            $order->update(['payment_status' => 'paid', 'payment_method' => $data['method']]);
        });

        return redirect()->route('staff.cashier.orders.receipt', $order)->with('success', 'Payment recorded.');
    }

    public function receipt(Order $order)
    {
        $this->order($order)->load(['table', 'orderItems.menuItem', 'restaurant', 'payments']);

        return view('operations.cashier.receipt', ['order' => $order, 'back' => route('staff.cashier.index')]);
    }
}
