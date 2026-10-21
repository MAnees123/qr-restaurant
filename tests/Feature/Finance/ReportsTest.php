<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Expense;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\OrderItem;

class ReportsTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('reports'); }

    private function seedSales(): void
    {
        $cat = MenuCategory::create(['restaurant_id' => $this->rest->id, 'name' => 'Burgers']);
        $item = MenuItem::create(['restaurant_id' => $this->rest->id, 'menu_category_id' => $cat->id, 'name' => 'Zinger', 'price' => 50]);
        $o1 = $this->paidOrder($this->rest, 150, ['created_at' => '2026-10-05 10:00:00', 'payment_method' => 'cash']);
        $o2 = $this->paidOrder($this->rest, 100, ['created_at' => '2026-10-06 10:00:00', 'payment_method' => 'card']);
        OrderItem::create(['order_id' => $o1->id, 'menu_item_id' => $item->id, 'quantity' => 3, 'unit_price' => 50, 'subtotal' => 150]);
        OrderItem::create(['order_id' => $o2->id, 'menu_item_id' => $item->id, 'quantity' => 2, 'unit_price' => 50, 'subtotal' => 100]);
        // other tenant data must never leak
        $oc = MenuCategory::create(['restaurant_id' => $this->other->restaurant_id, 'name' => 'OtherCat']);
        $oi = MenuItem::create(['restaurant_id' => $this->other->restaurant_id, 'menu_category_id' => $oc->id, 'name' => 'Leaky', 'price' => 1]);
        $oo = $this->paidOrder($this->other->restaurant, 9000, ['created_at' => '2026-10-05 10:00:00']);
        OrderItem::create(['order_id' => $oo->id, 'menu_item_id' => $oi->id, 'quantity' => 1, 'unit_price' => 9000, 'subtotal' => 9000]);
        Expense::create(['restaurant_id' => $this->rest->id, 'expense_date' => '2026-10-05', 'amount' => 40]);
    }

    public function test_hub_reports_use_real_data(): void
    {
        $this->seedSales();
        $q = ['from' => '2026-10-01', 'to' => '2026-10-31'];
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q))->assertOk()->assertSeeInOrder(['Revenue', 'Rs 250.00', 'Paid orders', '2', 'Expenses', 'Rs 40.00', 'Purchases', 'Rs 0.00']);
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'daily']))->assertSee('2026-10-05')->assertSee('2026-10-06');
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'items']))->assertSee('Zinger')->assertDontSee('Leaky');
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'top']))->assertSee('Zinger');
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'categories']))->assertSee('Burgers')->assertDontSee('OtherCat');
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'payments']))->assertSee('cash')->assertSee('card');
        $this->actingAs($this->admin)->get(route('admin.reports.index', $q + ['report' => 'expense-vs-revenue']))->assertOk()->assertSee('110.00');
    }

    public function test_csv_download_and_date_filter(): void
    {
        $this->seedSales();
        $res = $this->actingAs($this->admin)->get(route('admin.reports.index', ['report' => 'items', 'from' => '2026-10-01', 'to' => '2026-10-31', 'format' => 'csv']));
        $res->assertOk();
        $this->assertStringContainsString('text/csv', $res->headers->get('Content-Type'));
        $csv = $res->streamedContent();
        $this->assertStringContainsString('Item,"Qty sold",Revenue', $csv);
        $this->assertStringContainsString('Zinger,5,250', $csv);
        $empty = $this->actingAs($this->admin)->get(route('admin.reports.index', ['report' => 'items', 'from' => '2026-01-01', 'to' => '2026-01-31', 'format' => 'csv']))->streamedContent();
        $this->assertStringNotContainsString('Zinger', $empty);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $this->actingAs($this->adminWith(['billing']))->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_other_tenant_sees_only_own_and_invalid_input_is_safe(): void
    {
        $this->seedSales();
        $this->actingAs($this->other)->get(route('admin.reports.index', ['from' => '2026-10-01', 'to' => '2026-10-31']))->assertSeeInOrder(['Revenue', 'Rs 9,000.00', 'Paid orders', '1', 'Expenses', 'Rs 0.00'])->assertDontSee('Rs 250.00');
        // unknown report falls back to daily; reversed range is swapped
        $this->actingAs($this->admin)->get(route('admin.reports.index', ['report' => 'bogus', 'from' => '2026-10-31', 'to' => '2026-10-01']))->assertOk()->assertSee('Sales by day')->assertSee('value="2026-10-01"', false);
    }
}
