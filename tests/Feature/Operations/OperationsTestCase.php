<?php

namespace Tests\Feature\Operations;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

abstract class OperationsTestCase extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function makeItem(Restaurant $r, float $price = 100, string $name = 'Burger'): MenuItem
    {
        $cat = MenuCategory::firstOrCreate(['restaurant_id' => $r->id, 'name' => 'Mains'], ['is_active' => true, 'sort_order' => 1]);

        return MenuItem::create(['restaurant_id' => $r->id, 'menu_category_id' => $cat->id, 'name' => $name, 'price' => $price, 'is_available' => true, 'sort_order' => 1]);
    }

    protected function makeTable(Restaurant $r, string $number = '1'): Table
    {
        return Table::create(['restaurant_id' => $r->id, 'table_number' => $number, 'capacity' => 4, 'is_active' => true, 'status' => 'free']);
    }

    protected function makeOrder(Restaurant $r, array $attrs = []): Order
    {
        return Order::create(array_merge([
            'restaurant_id' => $r->id, 'order_number' => strtoupper(substr(md5(uniqid()), 0, 8)),
            'status' => 'pending', 'payment_status' => 'pending', 'subtotal' => 200, 'total_amount' => 200, 'order_type' => 'dine_in',
        ], $attrs));
    }

    protected function staff(Restaurant $r, string $staffRole)
    {
        return $this->makeUser($r, 'staff', ['staff_role' => $staffRole]);
    }
}
