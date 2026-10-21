<?php

namespace Tests\Feature\Finance;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

abstract class FinanceTestCase extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected Restaurant $rest;
    protected User $admin;
    protected User $other;

    protected function setUpTenants(string $feature): void
    {
        $this->rest = $this->makeRestaurant([$feature]);
        $this->admin = $this->makeUser($this->rest, 'admin');
        $this->other = $this->makeUser($this->makeRestaurant([$feature]), 'admin');
    }

    protected function paidOrder(Restaurant $r, float $total = 100, array $attrs = []): Order
    {
        $order = new Order();
        $order->forceFill(array_merge([
            'restaurant_id' => $r->id, 'order_number' => 'ORD-' . uniqid(), 'status' => 'served',
            'payment_status' => 'paid', 'payment_method' => 'cash', 'subtotal' => $total, 'total_amount' => $total,
        ], $attrs))->save(); // forceFill so tests can backdate created_at
        return $order;
    }
}
