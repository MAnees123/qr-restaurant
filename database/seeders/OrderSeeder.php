<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\MenuItem;
use App\Models\Table;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Payment::truncate();
        OrderItem::truncate();
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tables = Table::where('restaurant_id', 1)->get();
        $menuItems = MenuItem::where('restaurant_id', 1)->get();

        if ($tables->isEmpty() || $menuItems->isEmpty()) {
            return;
        }

        $paymentMethods = ['cash', 'card', 'safepay', 'jazzcash', 'easypaisa'];
        $notesOptions = [
            'Please make it extra spicy!',
            'Less sauce on the side please.',
            'No onions in the burger.',
            'Serving drinks first please.',
            'Celebrating a birthday!',
            null,
            null,
        ];

        // ──────────────────────────────────────────────────────────
        // 1. HISTORICAL COMPLETED ORDERS (Last 14 days)
        // ──────────────────────────────────────────────────────────
        // Total ~50 orders spread across days 0 to 14
        for ($i = 0; $i < 50; $i++) {
            // Distribute: 8 today, 15 this week, remaining spread up to 14 days ago
            if ($i < 8) {
                $orderDate = now()->subHours(rand(1, 10))->subMinutes(rand(1, 55));
            } elseif ($i < 25) {
                $orderDate = now()->subDays(rand(1, 6))->subHours(rand(1, 12));
            } else {
                $orderDate = now()->subDays(rand(7, 14))->subHours(rand(1, 12));
            }

            $table = $tables->random();
            $orderNum = 'ORD-' . strtoupper(Str::random(6));
            $guestToken = Str::random(32);
            $payMethod = $paymentMethods[array_rand($paymentMethods)];

            $order = Order::create([
                'restaurant_id'             => 1,
                'table_id'                  => $table->id,
                'order_number'              => $orderNum,
                'guest_token'               => $guestToken,
                'status'                    => 'served',
                'payment_status'            => 'paid',
                'payment_method'            => $payMethod,
                'subtotal'                  => 0,
                'coupon_code'               => rand(0, 3) === 0 ? 'WELCOME10' : null,
                'discount_amount'           => 0,
                'total_amount'              => 0,
                'notes'                     => $notesOptions[array_rand($notesOptions)],
                'estimated_completion_time' => (clone $orderDate)->addMinutes(rand(15, 30)),
                'created_at'                => $orderDate,
                'updated_at'                => $orderDate,
            ]);

            $subtotal = 0;
            $itemsCount = rand(1, 4);
            $selectedItems = $menuItems->random($itemsCount);

            foreach ($selectedItems as $item) {
                $qty = rand(1, 3);
                $itemSubtotal = $item->price * $qty;
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id'             => $order->id,
                    'menu_item_id'         => $item->id,
                    'quantity'             => $qty,
                    'unit_price'           => $item->price,
                    'subtotal'             => $itemSubtotal,
                    'special_instructions' => rand(0, 1) ? 'Standard prep' : null,
                    'created_at'           => $orderDate,
                    'updated_at'           => $orderDate,
                ]);
            }

            $discountAmount = 0;
            if ($order->coupon_code === 'WELCOME10') {
                $discountAmount = round($subtotal * 0.10, 2);
            }
            $totalAmount = max(0, $subtotal - $discountAmount);

            $order->update([
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
            ]);

            // Record paid payment
            Payment::create([
                'restaurant_id'  => 1,
                'order_id'       => $order->id,
                'amount'         => $totalAmount,
                'method'         => $payMethod,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'status'         => 'paid',
                'notes'          => 'Payment settled successfully via ' . ucfirst($payMethod),
                'created_at'     => $orderDate,
                'updated_at'     => $orderDate,
            ]);
        }

        // ──────────────────────────────────────────────────────────
        // 2. LIVE ACTIVE ORDERS FOR CLIENT DEMO (Kitchen & POS)
        // ──────────────────────────────────────────────────────────
        $liveOrdersConfig = [
            // Pending Orders
            [
                'status'         => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'table_idx'      => 0, // T-01
                'items'          => [1, 7, 25], // Mozzarella, Angus Burger, Mango Juice
                'mins_ago'       => 5,
                'notes'          => 'Customer requested warm water with lemon.',
            ],
            [
                'status'         => 'pending',
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'table_idx'      => 1, // T-02
                'items'          => [11, 27], // Margherita Pizza, Mint Margarita
                'mins_ago'       => 8,
                'notes'          => 'Allergy alert: Extra cheese, no chili flakes.',
            ],

            // Preparing Orders (Kitchen is currently cooking)
            [
                'status'         => 'preparing',
                'payment_status' => 'paid',
                'payment_method' => 'safepay',
                'table_idx'      => 4, // VIP-1
                'items'          => [14, 15, 21, 28], // Ribeye Steak, Mixed Grill, Lava Cake, Macchiato
                'mins_ago'       => 15,
                'notes'          => 'Steak medium-rare please. VIP guests.',
            ],
            [
                'status'         => 'preparing',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'table_idx'      => 8, // Patio-02
                'items'          => [8, 4, 26], // Zinger Burger, Buffalo Wings, Lassi
                'mins_ago'       => 12,
                'notes'          => 'Make the wings extra crispy.',
            ],

            // Ready Orders (Cooked, on the pass waiting for waiter pickup)
            [
                'status'         => 'ready',
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'table_idx'      => 10, // Roof-01
                'items'          => [17, 19, 20], // Chicken Biryani, Butter Chicken, Garlic Naan
                'mins_ago'       => 22,
                'notes'          => 'Deliver to rooftop section table 1.',
            ],
            [
                'status'         => 'ready',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'table_idx'      => 0, // T-01
                'items'          => [2, 3], // Nachos, Dynamite Shrimps
                'mins_ago'       => 18,
                'notes'          => 'Add extra napkins.',
            ],
        ];

        foreach ($liveOrdersConfig as $cfg) {
            $orderDate = now()->subMinutes($cfg['mins_ago']);
            $targetTable = $tables[$cfg['table_idx']] ?? $tables->first();
            $orderNum = 'ORD-' . strtoupper(Str::random(6));

            $order = Order::create([
                'restaurant_id'             => 1,
                'table_id'                  => $targetTable->id,
                'order_number'              => $orderNum,
                'guest_token'               => Str::random(32),
                'status'                    => $cfg['status'],
                'payment_status'            => $cfg['payment_status'],
                'payment_method'            => $cfg['payment_method'],
                'subtotal'                  => 0,
                'coupon_code'               => null,
                'discount_amount'           => 0,
                'total_amount'              => 0,
                'notes'                     => $cfg['notes'],
                'estimated_completion_time' => (clone $orderDate)->addMinutes(20),
                'created_at'                => $orderDate,
                'updated_at'                => now(),
            ]);

            $subtotal = 0;
            foreach ($cfg['items'] as $itemId) {
                $item = $menuItems->firstWhere('id', $itemId) ?? $menuItems->random();
                $qty = rand(1, 2);
                $itemSubtotal = $item->price * $qty;
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id'             => $order->id,
                    'menu_item_id'         => $item->id,
                    'quantity'             => $qty,
                    'unit_price'           => $item->price,
                    'subtotal'             => $itemSubtotal,
                    'special_instructions' => 'Live Demo Item',
                    'created_at'           => $orderDate,
                    'updated_at'           => $orderDate,
                ]);
            }

            $order->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal,
            ]);

            if ($cfg['payment_status'] === 'paid') {
                Payment::create([
                    'restaurant_id'  => 1,
                    'order_id'       => $order->id,
                    'amount'         => $subtotal,
                    'method'         => $cfg['payment_method'],
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                    'status'         => 'paid',
                    'notes'          => 'Pre-paid via ' . ucfirst($cfg['payment_method']),
                    'created_at'     => $orderDate,
                    'updated_at'     => $orderDate,
                ]);
            }
        }
    }
}
