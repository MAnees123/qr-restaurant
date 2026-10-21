<?php

namespace Tests\Feature\Operations;

use App\Models\Payment;

class CashierModuleTest extends OperationsTestCase
{
    public function test_cashier_lists_pays_and_prints_receipt(): void
    {
        $r = $this->makeRestaurant(['cashier_module']);
        $c = $this->staff($r, 'cashier');
        $order = $this->makeOrder($r, ['total_amount' => 250, 'subtotal' => 250, 'status' => 'served']);
        $paid = $this->makeOrder($r, ['payment_status' => 'paid']);

        $this->actingAs($c)->get(route('staff.cashier.index'))->assertOk()->assertSee($order->order_number)->assertDontSee($paid->order_number);
        $this->actingAs($c)->get(route('staff.cashier.orders.show', $order))->assertOk();

        $this->actingAs($c)->post(route('staff.cashier.orders.pay', $order), ['method' => 'cash', 'tendered' => 300])
            ->assertRedirect(route('staff.cashier.orders.receipt', $order));

        $this->assertSame('paid', $order->fresh()->payment_status);
        $p = Payment::where('order_id', $order->id)->firstOrFail();
        $this->assertEquals(250, $p->amount);
        $this->assertSame('cash', $p->method);
        $this->assertStringContainsString('change 50.00', $p->notes);

        $this->actingAs($c)->get(route('staff.cashier.orders.receipt', $order))->assertOk()->assertSee('TOTAL')->assertSee('250.00');

        // cannot pay twice
        $this->actingAs($c)->post(route('staff.cashier.orders.pay', $order), ['method' => 'cash'])->assertSessionHasErrors('order');
        $this->assertSame(1, Payment::where('order_id', $order->id)->count());
    }

    public function test_daily_summary_totals_by_method(): void
    {
        $r = $this->makeRestaurant(['cashier_module']);
        $c = $this->staff($r, 'cashier');
        foreach ([['cash', 100], ['cash', 50], ['card', 70]] as [$m, $amt]) {
            $o = $this->makeOrder($r, ['total_amount' => $amt, 'subtotal' => $amt]);
            $this->actingAs($c)->post(route('staff.cashier.orders.pay', $o), ['method' => $m])->assertRedirect();
        }
        $this->makeOrder($r, ['total_amount' => 30]); // unpaid

        $res = $this->actingAs($c)->get(route('staff.cashier.summary'))->assertOk();
        $res->assertSee('220.00')->assertSee('150.00')->assertSee('70.00');

        $admin = $this->makeUser($r, 'admin');
        $this->actingAs($admin)->get(route('admin.cashier.summary'))->assertOk()->assertSee('220.00');
        $this->actingAs($admin)->get(route('admin.cashier.summary', ['date' => '2001-01-01']))->assertOk()->assertSee('No payments on this date');
    }

    public function test_403_without_feature_or_role(): void
    {
        $no = $this->makeRestaurant(['waiter_panel']);
        $this->actingAs($this->staff($no, 'cashier'))->get(route('staff.cashier.index'))->assertForbidden();
        $this->actingAs($this->makeUser($no, 'admin'))->get(route('admin.cashier.summary'))->assertForbidden();

        $r = $this->makeRestaurant(['cashier_module', 'waiter_panel']);
        $this->actingAs($this->staff($r, 'waiter'))->get(route('staff.cashier.index'))->assertForbidden();
    }

    public function test_tenant_isolation(): void
    {
        $a = $this->makeRestaurant(['cashier_module']);
        $b = $this->makeRestaurant(['cashier_module']);
        $cb = $this->staff($b, 'cashier');
        $orderA = $this->makeOrder($a);

        $this->actingAs($cb)->get(route('staff.cashier.index'))->assertDontSee($orderA->order_number);
        $this->actingAs($cb)->get(route('staff.cashier.orders.show', $orderA))->assertNotFound();
        $this->actingAs($cb)->post(route('staff.cashier.orders.pay', $orderA), ['method' => 'cash'])->assertNotFound();
        $this->actingAs($cb)->get(route('staff.cashier.orders.receipt', $orderA))->assertNotFound();
        $this->actingAs($this->makeUser($b, 'admin'))->get(route('admin.cashier.receipt', $orderA))->assertNotFound();
        $this->assertSame('pending', $orderA->fresh()->payment_status);
    }

    public function test_validation(): void
    {
        $r = $this->makeRestaurant(['cashier_module']);
        $c = $this->staff($r, 'cashier');
        $o = $this->makeOrder($r, ['total_amount' => 100]);

        $this->actingAs($c)->post(route('staff.cashier.orders.pay', $o), ['method' => 'cheque'])->assertSessionHasErrors('method');
        $this->actingAs($c)->post(route('staff.cashier.orders.pay', $o), ['method' => 'cash', 'tendered' => 50])->assertSessionHasErrors('tendered');
        $this->assertSame('pending', $o->fresh()->payment_status);
        $this->assertSame(0, Payment::count());
    }
}
