<?php

namespace Tests\Feature\Operations;

use App\Models\OperationsSetting;
use App\Models\Order;

class OnlineOrdersTest extends OperationsTestCase
{
    private function payload($item, array $extra = []): array
    {
        return array_merge([
            'order_type' => 'pickup', 'customer_name' => 'Sara', 'customer_phone' => '0300 1234567',
            'qty' => [$item->id => 2],
        ], $extra);
    }

    public function test_guest_places_pickup_order_and_admin_accepts_and_completes(): void
    {
        $r = $this->makeRestaurant(['online_orders']);
        $item = $this->makeItem($r, 150);

        $this->get(route('operations.online.show', $r))->assertOk()->assertSee('Burger');
        $res = $this->post(route('operations.online.place', $r), $this->payload($item));
        $order = Order::where('restaurant_id', $r->id)->firstOrFail();
        $res->assertRedirect(route('operations.online.track', [$r, $order->order_number]));

        $this->assertSame('pickup', $order->order_type);
        $this->assertNull($order->table_id);
        $this->assertEquals(300, $order->total_amount);
        $this->assertSame('03001234567', $order->customer_phone);
        $this->get(route('operations.online.track', [$r, $order->order_number]))->assertOk()->assertSee($order->order_number);

        $admin = $this->makeUser($r, 'admin');
        $this->actingAs($admin)->get(route('admin.online-orders.index'))->assertOk()->assertSee($order->order_number);
        $this->actingAs($admin)->post(route('admin.online-orders.accept', $order))->assertRedirect();
        $this->assertSame('confirmed', $order->fresh()->status);
        $this->actingAs($admin)->post(route('admin.online-orders.status', $order), ['status' => 'ready'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.online-orders.status', $order), ['status' => 'served', 'collect_cash' => 1])->assertRedirect();
        $order->refresh();
        $this->assertSame('served', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'method' => 'cash']);
    }

    public function test_admin_rejects_order_with_reason(): void
    {
        $r = $this->makeRestaurant(['online_orders']);
        $order = $this->makeOrder($r, ['order_type' => 'pickup']);
        $admin = $this->makeUser($r, 'admin');

        $this->actingAs($admin)->post(route('admin.online-orders.reject', $order), ['reason' => 'Sold out'])->assertRedirect();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('Sold out', $order->fresh()->rejection_reason);
        // cannot accept after reject
        $this->actingAs($admin)->post(route('admin.online-orders.accept', $order))->assertSessionHas('error');
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_public_page_404_when_feature_not_granted_and_admin_403(): void
    {
        $r = $this->makeRestaurant(['waiter_panel']);
        $item = $this->makeItem($r);
        $this->get(route('operations.online.show', $r))->assertNotFound();
        $this->post(route('operations.online.place', $r), $this->payload($item))->assertNotFound();
        $this->assertSame(0, Order::count());

        $this->actingAs($this->makeUser($r, 'admin'))->get(route('admin.online-orders.index'))->assertForbidden();
    }

    public function test_closed_ordering_blocks_orders(): void
    {
        $r = $this->makeRestaurant(['online_orders']);
        $item = $this->makeItem($r);
        $admin = $this->makeUser($r, 'admin');
        $this->actingAs($admin)->post(route('admin.online-orders.settings'), [])->assertRedirect();
        $this->assertFalse(OperationsSetting::forRestaurant($r->id)->online_ordering_open);

        $this->post(route('operations.online.place', $r), $this->payload($item))->assertForbidden();
        $this->assertSame(0, Order::count());
    }

    public function test_validation_failures(): void
    {
        $r = $this->makeRestaurant(['online_orders']);
        $item = $this->makeItem($r);

        $this->post(route('operations.online.place', $r), $this->payload($item, ['customer_name' => '']))->assertSessionHasErrors('customer_name');
        $this->post(route('operations.online.place', $r), $this->payload($item, ['customer_phone' => 'abc']))->assertSessionHasErrors('customer_phone');
        $this->post(route('operations.online.place', $r), $this->payload($item, ['order_type' => 'delivery']))->assertSessionHasErrors('order_type'); // delivery not granted
        $this->post(route('operations.online.place', $r), $this->payload($item, ['qty' => [$item->id => 0]]))->assertSessionHasErrors('qty');
        $this->assertSame(0, Order::count());
    }

    public function test_tenant_isolation(): void
    {
        $a = $this->makeRestaurant(['online_orders']);
        $b = $this->makeRestaurant(['online_orders']);
        $itemA = $this->makeItem($a);
        $orderA = $this->makeOrder($a, ['order_type' => 'pickup']);
        $adminB = $this->makeUser($b, 'admin');

        // another restaurant's menu item cannot be ordered through B's page
        $this->post(route('operations.online.place', $b), $this->payload($itemA))->assertSessionHasErrors('qty');
        $this->actingAs($adminB)->get(route('admin.online-orders.index'))->assertDontSee($orderA->order_number);
        $this->actingAs($adminB)->post(route('admin.online-orders.accept', $orderA))->assertNotFound();
        $this->actingAs($adminB)->post(route('admin.online-orders.reject', $orderA))->assertNotFound();
        $this->actingAs($adminB)->post(route('admin.online-orders.status', $orderA), ['status' => 'ready'])->assertNotFound();
        $this->get(route('operations.online.track', [$b, $orderA->order_number]))->assertNotFound();
        $this->assertSame('pending', $orderA->fresh()->status);
    }
}
