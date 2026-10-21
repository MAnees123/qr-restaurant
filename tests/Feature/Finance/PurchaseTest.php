<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\PurchaseOrder;
use App\Models\Finance\Supplier;

class PurchaseTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('purchase_mgmt'); }

    private function payload(int $supplierId): array
    {
        return ['supplier_id' => $supplierId, 'order_date' => '2026-10-01', 'items' => [
            ['description' => 'Flour', 'quantity' => 10, 'unit_cost' => 12.5], ['description' => 'Oil', 'quantity' => 2, 'unit_cost' => 40],
        ]];
    }

    public function test_create_receive_pay_flow(): void
    {
        $sup = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'S1']);
        $this->actingAs($this->admin)->post(route('admin.purchases.store'), $this->payload($sup->id))->assertRedirect();
        $po = PurchaseOrder::firstOrFail();
        $this->assertSame('PO-00001', $po->po_number);
        $this->assertEquals(205, $po->total_amount);
        $this->assertCount(2, $po->items);

        $this->actingAs($this->admin)->post(route('admin.purchases.receive', $po))->assertRedirect();
        $this->assertSame('received', $po->fresh()->status);

        $this->actingAs($this->admin)->post(route('admin.purchases.pay', $po), ['amount' => 100])->assertRedirect();
        $this->assertEquals(100, $po->fresh()->paid_amount);
        $this->assertSame('partial', $po->fresh()->payment_status);
        $this->actingAs($this->admin)->post(route('admin.purchases.pay', $po), ['amount' => 500])->assertSessionHasErrors('amount');
        $this->actingAs($this->admin)->post(route('admin.purchases.pay', $po))->assertRedirect();
        $this->assertSame('paid', $po->fresh()->payment_status);
        $this->actingAs($this->admin)->get(route('admin.purchases.index'))->assertOk()->assertSee('PO-00001');
        $this->actingAs($this->admin)->get(route('admin.purchases.show', $po))->assertOk()->assertSee('Flour');
        $this->assertEquals(0, $sup->fresh()->balance);
    }

    public function test_cancel_and_delete(): void
    {
        $sup = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'S1']);
        $this->actingAs($this->admin)->post(route('admin.purchases.store'), $this->payload($sup->id));
        $po = PurchaseOrder::firstOrFail();
        $this->actingAs($this->admin)->post(route('admin.purchases.cancel', $po));
        $this->assertSame('cancelled', $po->fresh()->status);
        $this->actingAs($this->admin)->delete(route('admin.purchases.destroy', $po))->assertRedirect(route('admin.purchases.index'));
        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $this->actingAs($this->adminWith([]))->get(route('admin.purchases.index'))->assertForbidden();
    }

    public function test_other_tenant_isolated(): void
    {
        $sup = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'S1']);
        $this->actingAs($this->admin)->post(route('admin.purchases.store'), $this->payload($sup->id));
        $po = PurchaseOrder::firstOrFail();
        $this->actingAs($this->other)->get(route('admin.purchases.show', $po))->assertNotFound();
        $this->actingAs($this->other)->post(route('admin.purchases.pay', $po))->assertNotFound();
        $this->actingAs($this->other)->post(route('admin.purchases.receive', $po))->assertNotFound();
        $this->actingAs($this->other)->get(route('admin.purchases.index'))->assertDontSee('PO-00001');
        // cannot create PO against someone else's supplier
        $this->actingAs($this->other)->post(route('admin.purchases.store'), $this->payload($sup->id))->assertSessionHasErrors('supplier_id');
        $this->assertDatabaseCount('purchase_orders', 1);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin)->post(route('admin.purchases.store'), ['supplier_id' => '', 'order_date' => 'x', 'items' => []])->assertSessionHasErrors(['supplier_id', 'order_date', 'items']);
        $sup = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'S1']);
        $this->actingAs($this->admin)->post(route('admin.purchases.store'), ['supplier_id' => $sup->id, 'order_date' => '2026-10-01', 'items' => [['description' => 'a', 'quantity' => 0, 'unit_cost' => -1]]])->assertSessionHasErrors(['items.0.quantity', 'items.0.unit_cost']);
    }
}
