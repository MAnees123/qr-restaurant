<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Invoice;
use App\Models\Finance\TaxRate;

class BillingTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('billing'); }

    public function test_generate_list_search_show(): void
    {
        $o1 = $this->paidOrder($this->rest, 100, ['order_number' => 'ORD-AAA']);
        $o2 = $this->paidOrder($this->rest, 50, ['order_number' => 'ORD-BBB']);
        $this->actingAs($this->admin)->get(route('admin.invoices.index'))->assertOk()->assertSee('ORD-AAA');
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $o1->id])->assertRedirect();
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $o2->id]);
        $this->assertSame(['INV-000001', 'INV-000002'], Invoice::orderBy('id')->pluck('invoice_number')->all());
        // idempotent
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $o1->id])->assertRedirect();
        $this->assertDatabaseCount('invoices', 2);
        $inv = Invoice::first();
        $this->assertEquals(0, $inv->tax_amount); // tax_mgmt not granted -> no tax
        $this->actingAs($this->admin)->get(route('admin.invoices.index', ['q' => 'INV-000002']))->assertOk()->assertSee('INV-000002')->assertDontSee('INV-000001');
        $this->actingAs($this->admin)->get(route('admin.invoices.show', $inv))->assertOk()->assertSee('INV-000001')->assertSee('ORD-AAA');
    }

    public function test_tax_applied_when_tax_feature_granted(): void
    {
        $r = $this->makeRestaurant(['billing', 'tax_mgmt']);
        $u = $this->makeUser($r);
        TaxRate::create(['restaurant_id' => $r->id, 'name' => 'GST', 'rate' => 10, 'type' => 'exclusive', 'is_active' => true, 'is_default' => true]);
        $o = $this->paidOrder($r, 200);
        $this->actingAs($u)->post(route('admin.invoices.generate'), ['order_id' => $o->id]);
        $this->assertDatabaseHas('invoices', ['restaurant_id' => $r->id, 'subtotal' => 200, 'tax_amount' => 20, 'total' => 220, 'tax_name' => 'GST']);

        TaxRate::where('restaurant_id', $r->id)->update(['type' => 'inclusive']);
        $o2 = $this->paidOrder($r, 110);
        $this->actingAs($u)->post(route('admin.invoices.generate'), ['order_id' => $o2->id]);
        $this->assertDatabaseHas('invoices', ['order_id' => $o2->id, 'subtotal' => 100, 'tax_amount' => 10, 'total' => 110]);
    }

    public function test_unpaid_order_rejected_and_numbering_per_restaurant(): void
    {
        $unpaid = $this->paidOrder($this->rest, 10, ['payment_status' => 'pending']);
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $unpaid->id])->assertSessionHasErrors('order_id');
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), [])->assertSessionHasErrors('order_id');
        $this->assertDatabaseCount('invoices', 0);
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $this->paidOrder($this->rest)->id]);
        $this->actingAs($this->other)->post(route('admin.invoices.generate'), ['order_id' => $this->paidOrder($this->other->restaurant)->id]);
        $this->assertSame(2, Invoice::where('invoice_number', 'INV-000001')->count());
    }

    public function test_feature_not_granted_is_403(): void
    {
        $this->actingAs($this->adminWith([]))->get(route('admin.invoices.index'))->assertForbidden();
    }

    public function test_other_tenant_isolated(): void
    {
        $o = $this->paidOrder($this->rest, 70, ['order_number' => 'ORD-SECRET']);
        $this->actingAs($this->admin)->post(route('admin.invoices.generate'), ['order_id' => $o->id]);
        $inv = Invoice::firstOrFail();
        $this->actingAs($this->other)->get(route('admin.invoices.show', $inv))->assertNotFound();
        $this->actingAs($this->other)->get(route('admin.invoices.index'))->assertOk()->assertDontSee('ORD-SECRET')->assertDontSee('INV-000001');
        $this->actingAs($this->other)->post(route('admin.invoices.generate'), ['order_id' => $o->id])->assertNotFound();
    }
}
