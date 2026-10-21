<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Supplier;

class SupplierTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('supplier_mgmt'); }

    public function test_crud_works_with_feature(): void
    {
        $this->actingAs($this->admin)->post(route('admin.suppliers.store'), ['name' => 'Fresh Farms', 'phone' => '123', 'opening_balance' => 50, 'is_active' => 1])->assertRedirect(route('admin.suppliers.index'));
        $s = Supplier::where('name', 'Fresh Farms')->firstOrFail();
        $this->assertSame($this->rest->id, $s->restaurant_id);
        $this->assertEquals(50, $s->balance);
        $this->actingAs($this->admin)->get(route('admin.suppliers.index'))->assertOk()->assertSee('Fresh Farms');
        $this->actingAs($this->admin)->put(route('admin.suppliers.update', $s), ['name' => 'Fresh Farms Ltd'])->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['id' => $s->id, 'name' => 'Fresh Farms Ltd', 'is_active' => 0]);
        $this->actingAs($this->admin)->delete(route('admin.suppliers.destroy', $s))->assertRedirect();
        $this->assertDatabaseMissing('suppliers', ['id' => $s->id]);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $u = $this->adminWith([]);
        $this->actingAs($u)->get(route('admin.suppliers.index'))->assertForbidden();
        $this->actingAs($u)->post(route('admin.suppliers.store'), ['name' => 'X'])->assertForbidden();
        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_other_tenant_cannot_see_or_modify(): void
    {
        $s = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'Secret Supplier']);
        $this->actingAs($this->other)->get(route('admin.suppliers.index'))->assertOk()->assertDontSee('Secret Supplier');
        $this->actingAs($this->other)->get(route('admin.suppliers.edit', $s))->assertNotFound();
        $this->actingAs($this->other)->put(route('admin.suppliers.update', $s), ['name' => 'Hacked'])->assertNotFound();
        $this->actingAs($this->other)->delete(route('admin.suppliers.destroy', $s))->assertNotFound();
        $this->assertDatabaseHas('suppliers', ['id' => $s->id, 'name' => 'Secret Supplier']);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin)->post(route('admin.suppliers.store'), ['name' => '', 'email' => 'nope', 'opening_balance' => -5])->assertSessionHasErrors(['name', 'email', 'opening_balance']);
        $this->assertDatabaseCount('suppliers', 0);
    }
}
