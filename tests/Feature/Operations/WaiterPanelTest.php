<?php

namespace Tests\Feature\Operations;

use App\Models\Order;
use App\Models\TableCall;

class WaiterPanelTest extends OperationsTestCase
{
    public function test_waiter_places_edits_and_serves_an_order(): void
    {
        $r = $this->makeRestaurant(['waiter_panel']);
        $w = $this->staff($r, 'waiter');
        $table = $this->makeTable($r);
        $burger = $this->makeItem($r, 100);
        $fries = $this->makeItem($r, 40, 'Fries');

        $this->actingAs($w)->get(route('staff.waiter.index'))->assertOk()->assertSee('Tables');
        $this->actingAs($w)->get(route('staff.waiter.orders.create', $table))->assertOk()->assertSee('Burger');

        $this->actingAs($w)->post(route('staff.waiter.orders.store', $table), ['qty' => [$burger->id => 2, $fries->id => 1]])
            ->assertRedirect(route('staff.waiter.index'));

        $order = Order::where('restaurant_id', $r->id)->firstOrFail();
        $this->assertEquals(240, $order->total_amount);
        $this->assertSame($table->id, $order->table_id);
        $this->assertSame($w->id, (int) $order->placed_by_user_id);
        $this->assertSame('occupied', $table->fresh()->status);
        $this->assertCount(2, $order->orderItems);

        $this->actingAs($w)->put(route('staff.waiter.orders.update', $order), ['qty' => [$burger->id => 1, $fries->id => 0], 'notes' => 'no onions'])->assertRedirect();
        $order->refresh();
        $this->assertEquals(100, $order->total_amount);
        $this->assertCount(1, $order->orderItems);
        $this->assertSame('no onions', $order->notes);

        $this->actingAs($w)->post(route('staff.waiter.orders.serve', $order))->assertRedirect();
        $this->assertSame('served', $order->fresh()->status);

        // served orders are no longer editable
        $this->actingAs($w)->get(route('staff.waiter.orders.edit', $order))->assertForbidden();
    }

    public function test_waiter_answers_table_call(): void
    {
        $r = $this->makeRestaurant(['waiter_panel']);
        $w = $this->staff($r, 'waiter');
        $call = TableCall::create(['restaurant_id' => $r->id, 'table_id' => $this->makeTable($r)->id, 'status' => 'pending']);

        $this->actingAs($w)->get(route('staff.waiter.index'))->assertSee('Table calls');
        $this->actingAs($w)->post(route('staff.waiter.calls.answer', $call))->assertRedirect();
        $this->assertSame('completed', $call->fresh()->status);
    }

    public function test_403_without_feature_or_wrong_role(): void
    {
        $noFeature = $this->makeRestaurant(['cashier_module']);
        $this->actingAs($this->staff($noFeature, 'waiter'))->get(route('staff.waiter.index'))->assertForbidden();

        $r = $this->makeRestaurant(['waiter_panel', 'cashier_module']);
        $this->actingAs($this->staff($r, 'cashier'))->get(route('staff.waiter.index'))->assertForbidden();
        $this->actingAs($this->makeUser($r, 'admin'))->get(route('staff.waiter.index'))->assertForbidden();
    }

    public function test_tenant_isolation(): void
    {
        $a = $this->makeRestaurant(['waiter_panel']);
        $b = $this->makeRestaurant(['waiter_panel']);
        $wb = $this->staff($b, 'waiter');
        $tableA = $this->makeTable($a);
        $itemA = $this->makeItem($a);
        $orderA = $this->makeOrder($a, ['table_id' => $tableA->id]);
        $callA = TableCall::create(['restaurant_id' => $a->id, 'table_id' => $tableA->id, 'status' => 'pending']);

        $this->actingAs($wb)->get(route('staff.waiter.orders.create', $tableA))->assertNotFound();
        $this->actingAs($wb)->post(route('staff.waiter.orders.store', $tableA), ['qty' => [$itemA->id => 1]])->assertNotFound();
        $this->actingAs($wb)->get(route('staff.waiter.orders.edit', $orderA))->assertNotFound();
        $this->actingAs($wb)->post(route('staff.waiter.orders.serve', $orderA))->assertNotFound();
        $this->actingAs($wb)->post(route('staff.waiter.calls.answer', $callA))->assertNotFound();
        $this->assertSame('pending', $orderA->fresh()->status);
        $this->assertSame('pending', $callA->fresh()->status);

        // own table but another restaurant's menu item is rejected
        $tableB = $this->makeTable($b);
        $this->actingAs($wb)->post(route('staff.waiter.orders.store', $tableB), ['qty' => [$itemA->id => 1]])->assertSessionHasErrors('qty');
        $this->assertSame(0, Order::where('restaurant_id', $b->id)->count());
    }

    public function test_validation_requires_at_least_one_item(): void
    {
        $r = $this->makeRestaurant(['waiter_panel']);
        $w = $this->staff($r, 'waiter');
        $table = $this->makeTable($r);
        $item = $this->makeItem($r);

        $this->actingAs($w)->post(route('staff.waiter.orders.store', $table), ['qty' => [$item->id => 0]])->assertSessionHasErrors('qty');
        $this->actingAs($w)->post(route('staff.waiter.orders.store', $table), [])->assertSessionHasErrors('qty');
        $this->actingAs($w)->post(route('staff.waiter.orders.store', $table), ['qty' => [$item->id => 500]])->assertSessionHasErrors('qty.' . $item->id);
    }
}
