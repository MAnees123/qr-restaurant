<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\BillingInvoice;

class SubscriptionBillingSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BillingInvoice::truncate();
        Subscription::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Restaurant 1: Enterprise Plan Subscription
        Subscription::create([
            'id'             => 1,
            'restaurant_id'  => 1,
            'plan_id'        => 4, // Enterprise
            'starts_at'      => now()->subMonths(6),
            'ends_at'        => now()->addMonths(6),
            'status'         => 'active',
            'trial_ends_at'  => null,
            'payment_status' => 'paid',
            'billing_cycle'  => 'yearly',
        ]);

        // Restaurant 2: Pro Plan Subscription
        Subscription::create([
            'id'             => 2,
            'restaurant_id'  => 2,
            'plan_id'        => 3, // Pro
            'starts_at'      => now()->subMonths(2),
            'ends_at'        => now()->addMonths(4),
            'status'         => 'active',
            'trial_ends_at'  => null,
            'payment_status' => 'paid',
            'billing_cycle'  => 'monthly',
        ]);

        // Restaurant 3: Starter Plan Subscription
        Subscription::create([
            'id'             => 3,
            'restaurant_id'  => 3,
            'plan_id'        => 2, // Starter
            'starts_at'      => now()->subDays(20),
            'ends_at'        => now()->addDays(10),
            'status'         => 'active',
            'trial_ends_at'  => null,
            'payment_status' => 'paid',
            'billing_cycle'  => 'monthly',
        ]);

        // Invoices for SuperAdmin Billing Management
        $invoices = [
            [
                'restaurant_id'  => 1,
                'plan_id'        => 4,
                'invoice_number' => 'INV-2026-00101',
                'amount'         => 1990.00,
                'tax'            => 0.00,
                'discount'       => 0.00,
                'payment_status' => 'paid',
                'billing_cycle'  => 'yearly',
                'payment_method' => 'stripe_card',
                'paid_at'        => now()->subMonths(6),
                'created_at'     => now()->subMonths(6),
                'updated_at'     => now()->subMonths(6),
            ],
            [
                'restaurant_id'  => 2,
                'plan_id'        => 3,
                'invoice_number' => 'INV-2026-00201',
                'amount'         => 79.00,
                'tax'            => 0.00,
                'discount'       => 0.00,
                'payment_status' => 'paid',
                'billing_cycle'  => 'monthly',
                'payment_method' => 'card',
                'paid_at'        => now()->subMonths(1),
                'created_at'     => now()->subMonths(1),
                'updated_at'     => now()->subMonths(1),
            ],
            [
                'restaurant_id'  => 2,
                'plan_id'        => 3,
                'invoice_number' => 'INV-2026-00202',
                'amount'         => 79.00,
                'tax'            => 0.00,
                'discount'       => 0.00,
                'payment_status' => 'paid',
                'billing_cycle'  => 'monthly',
                'payment_method' => 'card',
                'paid_at'        => now()->subDays(2),
                'created_at'     => now()->subDays(2),
                'updated_at'     => now()->subDays(2),
            ],
            [
                'restaurant_id'  => 3,
                'plan_id'        => 2,
                'invoice_number' => 'INV-2026-00301',
                'amount'         => 29.00,
                'tax'            => 0.00,
                'discount'       => 0.00,
                'payment_status' => 'paid',
                'billing_cycle'  => 'monthly',
                'payment_method' => 'card',
                'paid_at'        => now()->subDays(20),
                'created_at'     => now()->subDays(20),
                'updated_at'     => now()->subDays(20),
            ],
        ];

        foreach ($invoices as $inv) {
            BillingInvoice::create($inv);
        }
    }
}
