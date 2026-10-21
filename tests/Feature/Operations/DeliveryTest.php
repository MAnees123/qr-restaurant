<?php

namespace Tests\Feature\Operations;

use App\Models\OperationsSetting;
use App\Models\Order;

class DeliveryTest extends OperationsTestCase
{
    public function test_guest_delivery_order_gets_fee_then_admin_assigns_and_delivers(): void
    {
        $r = $this->makeRestaurant(['delivery']);
        $item = $this->makeItem($r, 200);
        $admin = $this->makeUser($r, 'admin');

        $this->actingAs($admin)->post(route('admin.delivery.settings'), ['delivery_fee' => 50, 'delivery_min_order' => 100])->assertRedirect();
        auth()->logout();

        $this->post(route('operations.online.place', $r), [
            'order_type' => 'delivery', 'customer_name' => 'Ali', 'customer_phone' => '03111111111',
            'delivery_address' => 'House 5, Street 2', 'qty' => [$item->id => 1],
        ])->assertRedirect();

        $order = Order::where('restaurant_id', $r->id)->firstOrFail();
        $this->assertSame('delivery', $order->order_type);
        $this->assertEquals(50, $order->delivery_fee);
        $this->assertEquals(250, $order->total_amount);
        $this->assertSame('pending', $order->delivery_status);
        $this->assertSame('House 5, Street 2', $order->delivery_address);

        $this->actingAs($admin)->get(route('admin.delivery.index'))->assertOk()->assertSee($order->order_number)->assertSee('House 5');
        $this->actingAs($admin)->post(route('admin.delivery.status', $order), ['delivery_status' => 'out'])->assertSessionHas('error'); // no driver yet
        $this->actingAs($admin)->post(route('admin.delivery.assign', $order), ['driver_name' => 'Bilal', 'driver_phone' => '0322'])->assertRedirect();
        $this->assertSame('assigned', $order->fresh()->delivery_status);
        $this->actingAs($admin)->post(route('admin.delivery.status', $order), ['delivery_status' => 'out'])->assertRedirect();
        $this->assertSame('out', $order->fresh()->delivery_status);
        $this->actingAs($admin)->post(route('admin.delivery.status', $order), ['delivery_status' => 'delivered', 'collect_cash' => 1])->assertRedirect();

        $order->refresh();
        $this->assertSame('delivered', $order->delivery_status);
        $this->assertSame('served', $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('Bilal', $order->driver_name);
    }

    public function test_min_order_and_address_validation(): void
    {
        $r = $this->makeRestaurant(['delivery']);
        $item = $this->makeItem($r, 50);
        OperationsSetting::forRestaurant($r->id)->update(['delivery_min_order' => 500]);
        $base = ['order_type' => 'delivery', 'customer_name' => 'Ali', 'customer_phone' => '03111111111', 'qty' => [$item->id => 1]];

        $this->post(route('operations.online.place', $r), $base)->assertSessionHasErrors('delivery_address');
        $this->post(route('operations.online.place', $r), $base + ['delivery_address' => 'Somewhere'])->assertSessionHasErrors('qty');
        $this->assertSame(0, Order::count());

        $admin = $this->makeUser($r, 'admin');
        $this->actingAs($admin)->post(route('admin.delivery.settings'), ['delivery_fee' => -1, 'delivery_min_order' => 'x'])->assertSessionHasErrors(['delivery_fee', 'delivery_min_order']);
        $this->actingAs($admin)->post(route('admin.delivery.assign', $this->makeOrder($r, ['order_type' => 'delivery'])), ['driver_name' => ''])->assertSessionHasErrors('driver_name');
    }

    public function test_403_without_feature(): void
    {
        $admin = $this->adminWith(['online_orders']);
        $this->actingAs($admin)->get(route('admin.delivery.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.delivery.settings'), ['delivery_fee' => 1, 'delivery_min_order' => 1])->assertForbidden();
    }

    public function test_tenant_isolation(): void
    {
        $a = $this->makeRestaurant(['delivery']);
        $b = $this->makeRestaurant(['delivery']);
        $orderA = $this->makeOrder($a, ['order_type' => 'delivery', 'delivery_status' => 'pending', 'delivery_address' => 'Secret Lane']);
        $adminB = $this->makeUser($b, 'admin');

        $this->actingAs($adminB)->get(route('admin.delivery.index'))->assertDontSee('Secret Lane');
        $this->actingAs($adminB)->post(route('admin.delivery.assign', $orderA), ['driver_name' => 'Eve'])->assertNotFound();
        $this->actingAs($adminB)->post(route('admin.delivery.status', $orderA), ['delivery_status' => 'out'])->assertNotFound();
        $this->assertNull($orderA->fresh()->driver_name);
    }
}
