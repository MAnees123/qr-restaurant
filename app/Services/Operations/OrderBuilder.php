<?php

namespace App\Services\Operations;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Shared order creation/editing used by waiter panel and online ordering. Prices always come from the DB. */
class OrderBuilder
{
    /** @param array<int,int|string> $quantities menu_item_id => qty  @return array<int,array{item:MenuItem,qty:int}> */
    public function resolveLines(Restaurant $restaurant, array $quantities): array
    {
        $quantities = array_filter(array_map('intval', $quantities), fn ($q) => $q > 0);
        if (!$quantities) {
            throw ValidationException::withMessages(['qty' => 'Add at least one item to the order.']);
        }

        $items = MenuItem::where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
            ->whereIn('id', array_keys($quantities))
            ->get()->keyBy('id');

        $lines = [];
        foreach ($quantities as $id => $qty) {
            if (!isset($items[$id])) {
                throw ValidationException::withMessages(['qty' => 'One of the selected items is not available.']);
            }
            $lines[] = ['item' => $items[$id], 'qty' => $qty];
        }

        return $lines;
    }

    public function subtotal(array $lines): float
    {
        return round(array_sum(array_map(fn ($l) => $l['item']->price * $l['qty'], $lines)), 2);
    }

    public function create(Restaurant $restaurant, array $lines, array $attrs = []): Order
    {
        return DB::transaction(function () use ($restaurant, $lines, $attrs) {
            $subtotal = $this->subtotal($lines);
            $discount = (float) ($attrs['discount_amount'] ?? 0);
            $fee = (float) ($attrs['delivery_fee'] ?? 0);
            $prep = max(array_map(fn ($l) => (int) ($l['item']->preparation_time ?: 15), $lines));

            $order = Order::create(array_merge([
                'status' => 'pending',
                'payment_status' => 'pending',
                'order_type' => 'dine_in',
            ], $attrs, [
                'restaurant_id' => $restaurant->id,
                'order_number' => strtoupper(Str::random(8)),
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'delivery_fee' => $fee,
                'total_amount' => max(0, $subtotal - $discount) + $fee,
                'estimated_completion_time' => now()->addMinutes($prep),
            ]));

            $this->writeItems($order, $lines);

            if ($order->table_id) {
                Table::where('id', $order->table_id)->where('restaurant_id', $restaurant->id)->update(['status' => 'occupied', 'auto_release_at' => null]);
            }

            return $order;
        });
    }

    /** Replace all items of an order and recompute totals. */
    public function replaceItems(Order $order, array $lines, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $lines, $notes) {
            $order->orderItems()->delete();
            $this->writeItems($order, $lines);
            $subtotal = $this->subtotal($lines);
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => max(0, $subtotal - (float) $order->discount_amount) + (float) $order->delivery_fee,
                'notes' => $notes,
            ]);

            return $order->refresh();
        });
    }

    private function writeItems(Order $order, array $lines): void
    {
        foreach ($lines as $l) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $l['item']->id,
                'quantity' => $l['qty'],
                'unit_price' => $l['item']->price,
                'subtotal' => round($l['item']->price * $l['qty'], 2),
            ]);
        }
    }
}
