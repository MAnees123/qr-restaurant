<?php

namespace Tests\Feature\Staff;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class CustomerMgmtTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    private function order(int $rid, string $no, array $extra = []): Order
    {
        $o = new Order();
        $o->forceFill(array_merge(['restaurant_id' => $rid, 'order_number' => $no, 'status' => 'served', 'total_amount' => 100], $extra))->save();

        return $o;
    }

    public function test_crud_search_and_filter(): void
    {
        $admin = $this->adminWith(['customer_mgmt']);

        $this->actingAs($admin)->get(route('admin.customers.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'name' => 'Ali Khan', 'phone' => '0300111', 'email' => 'ali@x.com', 'tags' => 'vip, regular, vip', 'notes' => 'No onion',
        ])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.customers.store'), ['name' => 'Sara', 'phone' => '0300222']);

        $c = Customer::where('phone', '0300111')->firstOrFail();
        $this->assertSame('vip,regular', $c->tags);

        $this->actingAs($admin)->get(route('admin.customers.index', ['q' => 'Ali']))->assertSee('Ali Khan')->assertDontSee('Sara');
        $this->actingAs($admin)->get(route('admin.customers.index', ['tag' => 'vip']))->assertSee('Ali Khan')->assertDontSee('Sara');
        $this->actingAs($admin)->get(route('admin.customers.index', ['q' => '0300222']))->assertSee('Sara')->assertDontSee('Ali Khan');

        $this->actingAs($admin)->put(route('admin.customers.update', $c), ['name' => 'Ali K', 'phone' => '0300111'])->assertRedirect();
        $this->assertSame('Ali K', $c->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.customers.destroy', $c))->assertRedirect();
        $this->assertDatabaseMissing('customers', ['id' => $c->id]);
    }

    public function test_order_history_link_and_auto_link(): void
    {
        $admin = $this->adminWith(['customer_mgmt']);
        $c = Customer::create(['restaurant_id' => $admin->restaurant_id, 'name' => 'Ali', 'phone' => '111']);
        $o = $this->order($admin->restaurant_id, 'ORD-1', ['total_amount' => 250]);

        $this->actingAs($admin)->post(route('admin.customers.link-order', $c), ['order_number' => 'ORD-1'])->assertRedirect();
        $this->assertSame($c->id, $o->fresh()->customer_id);
        $this->actingAs($admin)->get(route('admin.customers.show', $c))->assertOk()->assertSee('ORD-1')->assertSee('250.00');
        $this->actingAs($admin)->post(route('admin.customers.link-order', $c), ['order_number' => 'NOPE'])->assertSessionHasErrors('order_number');

        // auto-link by phone: existing customer, then brand new one
        $o2 = $this->order($admin->restaurant_id, 'ORD-2', ['customer_phone' => '111']);
        $this->assertSame($c->id, $o2->fresh()->customer_id);
        $o3 = $this->order($admin->restaurant_id, 'ORD-3', ['customer_phone' => '999', 'customer_name' => 'New Guy']);
        $this->assertSame('New Guy', Customer::find($o3->fresh()->customer_id)->name);
    }

    public function test_no_auto_link_without_feature(): void
    {
        $r = $this->makeRestaurant([]);
        $o = $this->order($r->id, 'ORD-9', ['customer_phone' => '555']);
        $this->assertNull($o->fresh()->customer_id);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.customers.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.customers.store'), ['name' => 'X'])->assertForbidden();
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_cross_tenant_isolation(): void
    {
        $a = $this->adminWith(['customer_mgmt']);
        $b = $this->adminWith(['customer_mgmt']);
        $cb = Customer::create(['restaurant_id' => $b->restaurant_id, 'name' => 'Bee', 'phone' => '1']);
        $ca = Customer::create(['restaurant_id' => $a->restaurant_id, 'name' => 'Aye', 'phone' => '2']);
        $ob = $this->order($b->restaurant_id, 'ORD-B');

        $this->actingAs($a)->get(route('admin.customers.index'))->assertSee('Aye')->assertDontSee('Bee');
        $this->actingAs($a)->get(route('admin.customers.show', $cb))->assertNotFound();
        $this->actingAs($a)->get(route('admin.customers.edit', $cb))->assertNotFound();
        $this->actingAs($a)->put(route('admin.customers.update', $cb), ['name' => 'Hack'])->assertNotFound();
        $this->actingAs($a)->delete(route('admin.customers.destroy', $cb))->assertNotFound();
        $this->actingAs($a)->post(route('admin.customers.link-order', $ca), ['order_number' => 'ORD-B'])->assertSessionHasErrors('order_number');
        $this->actingAs($a)->post(route('admin.customers.link-order', $cb), ['order_number' => 'ORD-B'])->assertNotFound();
        $this->assertNull($ob->fresh()->customer_id);
        $this->assertSame('Bee', $cb->fresh()->name);
        // same phone allowed in a different restaurant
        $this->actingAs($a)->post(route('admin.customers.store'), ['name' => 'Same', 'phone' => '1'])->assertSessionHasNoErrors();
    }

    public function test_validation(): void
    {
        $admin = $this->adminWith(['customer_mgmt']);
        Customer::create(['restaurant_id' => $admin->restaurant_id, 'name' => 'A', 'phone' => '1']);
        $this->actingAs($admin)->post(route('admin.customers.store'), [])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post(route('admin.customers.store'), ['name' => 'B', 'email' => 'bad'])->assertSessionHasErrors('email');
        $this->actingAs($admin)->post(route('admin.customers.store'), ['name' => 'B', 'phone' => '1'])->assertSessionHasErrors('phone');
    }
}
