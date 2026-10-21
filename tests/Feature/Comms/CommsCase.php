<?php

namespace Tests\Feature\Comms;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

abstract class CommsCase extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function makeTable(Restaurant $r, string $number = 'T-1'): Table
    {
        return Table::create(['restaurant_id' => $r->id, 'table_number' => $number, 'secure_token' => Str::random(24)]);
    }

    protected function makeItem(Restaurant $r, string $name = 'Burger', array $extra = []): MenuItem
    {
        $cat = MenuCategory::firstOrCreate(['restaurant_id' => $r->id, 'name' => 'Main'], ['is_active' => true]);
        $item = new MenuItem(['menu_category_id' => $cat->id, 'restaurant_id' => $r->id, 'name' => $name, 'price' => 10]);
        $item->forceFill($extra)->save();   // barcode is not mass assignable
        return $item;
    }

    protected function makeOrder(Restaurant $r, array $extra = [], ?Table $table = null): Order
    {
        $order = new Order(['restaurant_id' => $r->id, 'table_id' => $table?->id, 'order_number' => strtoupper(Str::random(8)),
            'status' => 'pending', 'subtotal' => 25, 'total_amount' => 25]);
        $order->forceFill($extra)->save();   // customer_phone / customer_email are not mass assignable
        return $order;
    }
}
