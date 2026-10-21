<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Invoice;
use App\Models\Finance\TaxRate;

class TaxTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('tax_mgmt'); }

    public function test_crud_and_single_default(): void
    {
        $this->actingAs($this->admin)->post(route('admin.taxes.store'), ['name' => 'GST', 'rate' => 17, 'type' => 'exclusive', 'is_active' => 1, 'is_default' => 1])->assertRedirect(route('admin.taxes.index'));
        $this->actingAs($this->admin)->post(route('admin.taxes.store'), ['name' => 'Service', 'rate' => 5, 'type' => 'inclusive', 'is_active' => 1, 'is_default' => 1]);
        $this->assertSame(['Service'], TaxRate::where('is_default', true)->pluck('name')->all());
        $gst = TaxRate::where('name', 'GST')->first();
        $this->actingAs($this->admin)->get(route('admin.taxes.index'))->assertOk()->assertSee('GST');
        $this->actingAs($this->admin)->put(route('admin.taxes.update', $gst), ['name' => 'GST', 'rate' => 15, 'type' => 'exclusive', 'is_active' => 1, 'is_default' => 1]);
        $this->assertSame(['GST'], TaxRate::where('is_default', true)->pluck('name')->all());
        $this->assertEquals(15, $gst->fresh()->rate);
        // inactive can't be default
        $this->actingAs($this->admin)->put(route('admin.taxes.update', $gst), ['name' => 'GST', 'rate' => 15, 'type' => 'exclusive', 'is_default' => 1]);
        $this->assertFalse($gst->fresh()->is_default);
        $this->actingAs($this->admin)->delete(route('admin.taxes.destroy', $gst));
        $this->assertDatabaseMissing('tax_rates', ['id' => $gst->id]);
    }

    public function test_summary_report(): void
    {
        $inv = fn ($r, $seq, $date, $tax) => Invoice::create(['restaurant_id' => $r->id, 'order_id' => $this->paidOrder($r)->id, 'sequence' => $seq, 'invoice_number' => "INV-$seq", 'invoice_date' => $date, 'subtotal' => 100, 'tax_rate' => 10, 'tax_name' => 'GST', 'tax_type' => 'exclusive', 'tax_amount' => $tax, 'total' => 100 + $tax]);
        $inv($this->rest, 1, '2026-10-02', 10);
        $inv($this->rest, 2, '2026-10-03', 10);
        $inv($this->rest, 3, '2026-08-03', 99);
        $inv($this->other->restaurant, 1, '2026-10-02', 777);
        $res = $this->actingAs($this->admin)->get(route('admin.taxes.summary', ['from' => '2026-10-01', 'to' => '2026-10-31']))->assertOk();
        $res->assertSee('20.00')->assertDontSee('777')->assertDontSee('99.00');
    }

    public function test_feature_not_granted_is_403(): void
    {
        $u = $this->adminWith(['billing']);
        $this->actingAs($u)->get(route('admin.taxes.index'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.taxes.summary'))->assertForbidden();
    }

    public function test_other_tenant_isolated(): void
    {
        $t = TaxRate::create(['restaurant_id' => $this->rest->id, 'name' => 'Hidden VAT', 'rate' => 5, 'type' => 'exclusive']);
        $this->actingAs($this->other)->get(route('admin.taxes.index'))->assertOk()->assertDontSee('Hidden VAT');
        $this->actingAs($this->other)->get(route('admin.taxes.edit', $t))->assertNotFound();
        $this->actingAs($this->other)->put(route('admin.taxes.update', $t), ['name' => 'x', 'rate' => 1, 'type' => 'exclusive'])->assertNotFound();
        $this->actingAs($this->other)->delete(route('admin.taxes.destroy', $t))->assertNotFound();
        $this->assertDatabaseHas('tax_rates', ['id' => $t->id, 'name' => 'Hidden VAT']);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin)->post(route('admin.taxes.store'), ['name' => '', 'rate' => 150, 'type' => 'weird'])->assertSessionHasErrors(['name', 'rate', 'type']);
        $this->actingAs($this->admin)->post(route('admin.taxes.store'), ['name' => 'N', 'rate' => -1, 'type' => 'inclusive'])->assertSessionHasErrors('rate');
        $this->assertDatabaseCount('tax_rates', 0);
    }
}
