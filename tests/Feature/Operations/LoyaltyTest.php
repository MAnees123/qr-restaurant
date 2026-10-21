<?php

namespace Tests\Feature\Operations;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\OperationsSetting;

class LoyaltyTest extends OperationsTestCase
{
    public function test_points_earned_once_when_order_becomes_paid(): void
    {
        $r = $this->makeRestaurant(['loyalty']);
        OperationsSetting::forRestaurant($r->id)->update(['loyalty_points_per_currency' => 0.5]);
        $order = $this->makeOrder($r, ['total_amount' => 400, 'customer_phone' => '0300111', 'customer_name' => 'Zed']);

        $order->update(['payment_status' => 'paid']);
        $order->update(['notes' => 'again']); // re-save must not double-award

        $acc = LoyaltyAccount::where('restaurant_id', $r->id)->where('phone', '0300111')->firstOrFail();
        $this->assertSame(200, $acc->balance);
        $this->assertSame(1, LoyaltyTransaction::where('order_id', $order->id)->where('type', 'earn')->count());
        $this->assertSame('Zed', $acc->name);
    }

    public function test_no_points_without_feature_or_phone_or_when_disabled(): void
    {
        $noFeature = $this->makeRestaurant(['waiter_panel']);
        $this->makeOrder($noFeature, ['payment_status' => 'paid', 'customer_phone' => '1111111']);
        $noPhone = $this->makeRestaurant(['loyalty']);
        $this->makeOrder($noPhone, ['payment_status' => 'paid']);
        $off = $this->makeRestaurant(['loyalty']);
        OperationsSetting::forRestaurant($off->id)->update(['loyalty_enabled' => false]);
        $this->makeOrder($off, ['payment_status' => 'paid', 'customer_phone' => '2222222']);

        $this->assertSame(0, LoyaltyAccount::count());
    }

    public function test_redeem_on_online_order_and_refund_on_reject(): void
    {
        $r = $this->makeRestaurant(['online_orders', 'loyalty']);
        $item = $this->makeItem($r, 100);
        $acc = LoyaltyAccount::create(['restaurant_id' => $r->id, 'phone' => '03009999999', 'balance' => 80, 'lifetime_earned' => 80]);
        OperationsSetting::forRestaurant($r->id)->update(['loyalty_point_value' => 2, 'loyalty_min_redeem' => 10]);

        $this->post(route('operations.online.place', $r), [
            'order_type' => 'pickup', 'customer_name' => 'Q', 'customer_phone' => '0300-9999999', 'qty' => [$item->id => 2], 'redeem_points' => 30,
        ])->assertRedirect();

        $order = \App\Models\Order::where('restaurant_id', $r->id)->firstOrFail();
        $this->assertEquals(60, $order->discount_amount);
        $this->assertEquals(140, $order->total_amount);
        $this->assertSame(30, $order->loyalty_points_redeemed);
        $this->assertSame(50, $acc->fresh()->balance);
        $this->assertDatabaseHas('loyalty_transactions', ['order_id' => $order->id, 'type' => 'redeem', 'points' => -30]);

        $admin = $this->makeUser($r, 'admin');
        $this->actingAs($admin)->post(route('admin.online-orders.reject', $order), ['reason' => 'x'])->assertRedirect();
        $this->assertSame(80, $acc->fresh()->balance);
        $this->assertDatabaseHas('loyalty_transactions', ['order_id' => $order->id, 'type' => 'refund', 'points' => 30]);
    }

    public function test_redeem_validation(): void
    {
        $r = $this->makeRestaurant(['online_orders', 'loyalty']);
        $item = $this->makeItem($r, 10);
        LoyaltyAccount::create(['restaurant_id' => $r->id, 'phone' => '0301', 'balance' => 50, 'lifetime_earned' => 50]);
        $base = ['order_type' => 'pickup', 'customer_name' => 'Q', 'customer_phone' => '0301000', 'qty' => [$item->id => 1]];

        $this->post(route('operations.online.place', $r), $base + ['redeem_points' => 5])->assertSessionHasErrors('redeem_points'); // unknown account
        $base['customer_phone'] = '0301000';
        LoyaltyAccount::where('restaurant_id', $r->id)->update(['phone' => '0301000']);
        $this->post(route('operations.online.place', $r), $base + ['redeem_points' => 5])->assertSessionHasErrors('redeem_points');   // below minimum
        $this->post(route('operations.online.place', $r), $base + ['redeem_points' => 500])->assertSessionHasErrors('redeem_points'); // above balance
        $this->post(route('operations.online.place', $r), $base + ['redeem_points' => 40])->assertSessionHasErrors('redeem_points');  // exceeds order total (40 pts = Rs 40 > 10)
        $this->assertSame(50, LoyaltyAccount::first()->balance);
    }

    public function test_cashier_redeems_points_and_customer_earns_on_discounted_total(): void
    {
        $r = $this->makeRestaurant(['cashier_module', 'loyalty']);
        $c = $this->staff($r, 'cashier');
        $acc = LoyaltyAccount::create(['restaurant_id' => $r->id, 'phone' => '0302', 'balance' => 100, 'lifetime_earned' => 100]);
        $order = $this->makeOrder($r, ['total_amount' => 300, 'subtotal' => 300]);

        $this->actingAs($c)->post(route('staff.cashier.orders.pay', $order), ['method' => 'cash', 'customer_phone' => '0302', 'redeem_points' => 50])->assertRedirect();
        $order->refresh();
        $this->assertEquals(250, $order->total_amount);
        $this->assertEquals(250, $order->payments()->first()->amount);
        // 100 - 50 redeemed + 250 earned (1 pt per currency unit)
        $this->assertSame(300, $acc->fresh()->balance);
    }

    public function test_admin_settings_ledger_and_adjust(): void
    {
        $admin = $this->adminWith(['loyalty']);
        $rid = $admin->restaurant_id;
        $acc = LoyaltyAccount::create(['restaurant_id' => $rid, 'phone' => '0303', 'name' => 'Mo', 'balance' => 20, 'lifetime_earned' => 20]);

        $this->actingAs($admin)->get(route('admin.loyalty.index'))->assertOk()->assertSee('0303');
        $this->actingAs($admin)->post(route('admin.loyalty.settings'), [
            'loyalty_points_per_currency' => 2, 'loyalty_point_value' => 0.5, 'loyalty_min_redeem' => 25, 'loyalty_enabled' => 1,
        ])->assertRedirect();
        $s = OperationsSetting::forRestaurant($rid);
        $this->assertSame(2.0, $s->loyalty_points_per_currency);
        $this->assertSame(25, (int) $s->loyalty_min_redeem);

        $this->actingAs($admin)->post(route('admin.loyalty.adjust', $acc), ['points' => 15, 'note' => 'Goodwill'])->assertRedirect();
        $this->assertSame(35, $acc->fresh()->balance);
        $this->actingAs($admin)->get(route('admin.loyalty.show', $acc))->assertOk()->assertSee('Goodwill');
        $this->actingAs($admin)->post(route('admin.loyalty.adjust', $acc), ['points' => -100, 'note' => 'too much'])->assertSessionHasErrors('points');
        $this->assertSame(35, $acc->fresh()->balance);
    }

    public function test_settings_validation(): void
    {
        $admin = $this->adminWith(['loyalty']);
        $this->actingAs($admin)->post(route('admin.loyalty.settings'), ['loyalty_points_per_currency' => -1, 'loyalty_point_value' => 'a', 'loyalty_min_redeem' => 0])
            ->assertSessionHasErrors(['loyalty_points_per_currency', 'loyalty_point_value', 'loyalty_min_redeem']);
    }

    public function test_403_without_feature(): void
    {
        $admin = $this->adminWith(['delivery']);
        $this->actingAs($admin)->get(route('admin.loyalty.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.loyalty.settings'), [])->assertForbidden();
    }

    public function test_tenant_isolation(): void
    {
        $a = $this->makeRestaurant(['loyalty']);
        $accA = LoyaltyAccount::create(['restaurant_id' => $a->id, 'phone' => '0304-secret', 'balance' => 10, 'lifetime_earned' => 10]);
        $adminB = $this->makeUser($this->makeRestaurant(['loyalty']), 'admin');

        $this->actingAs($adminB)->get(route('admin.loyalty.index'))->assertDontSee('0304-secret');
        $this->actingAs($adminB)->get(route('admin.loyalty.show', $accA))->assertNotFound();
        $this->actingAs($adminB)->post(route('admin.loyalty.adjust', $accA), ['points' => 5, 'note' => 'x'])->assertNotFound();
        $this->assertSame(10, $accA->fresh()->balance);
    }
}
