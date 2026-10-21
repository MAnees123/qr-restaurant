<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;

class ExpenseTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('expense_tracking'); }

    public function test_categories_expenses_and_monthly_summary(): void
    {
        $this->actingAs($this->admin)->post(route('admin.expense-categories.store'), ['name' => 'Utilities'])->assertRedirect();
        $cat = ExpenseCategory::firstOrFail();
        $this->actingAs($this->admin)->post(route('admin.expenses.store'), ['expense_date' => '2026-10-05', 'amount' => 120.5, 'expense_category_id' => $cat->id, 'notes' => 'Electricity'])->assertRedirect(route('admin.expenses.index'));
        $this->actingAs($this->admin)->post(route('admin.expenses.store'), ['expense_date' => '2026-10-06', 'amount' => 30, 'expense_category_id' => $cat->id]);
        $e = Expense::orderBy('id')->first();
        $this->assertEquals(120.5, $e->amount);

        $this->actingAs($this->admin)->get(route('admin.expenses.index', ['month' => '2026-10']))->assertOk()->assertSee('Electricity')->assertSee('150.50');
        $this->actingAs($this->admin)->get(route('admin.expenses.index', ['month' => '2026-09']))->assertOk()->assertDontSee('Electricity');

        $this->actingAs($this->admin)->put(route('admin.expenses.update', $e), ['expense_date' => '2026-10-05', 'amount' => 99])->assertRedirect();
        $this->assertDatabaseHas('expenses', ['id' => $e->id, 'amount' => 99, 'expense_category_id' => null]);
        $this->actingAs($this->admin)->delete(route('admin.expenses.destroy', $e));
        $this->assertDatabaseMissing('expenses', ['id' => $e->id]);
        $this->actingAs($this->admin)->delete(route('admin.expense-categories.destroy', $cat));
        $this->assertDatabaseCount('expense_categories', 0);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $this->actingAs($this->adminWith(['reports']))->get(route('admin.expenses.index'))->assertForbidden();
    }

    public function test_other_tenant_isolated(): void
    {
        $cat = ExpenseCategory::create(['restaurant_id' => $this->rest->id, 'name' => 'Rent']);
        $e = Expense::create(['restaurant_id' => $this->rest->id, 'expense_date' => now()->toDateString(), 'amount' => 500, 'notes' => 'PrivateNote']);
        $this->actingAs($this->other)->get(route('admin.expenses.index'))->assertOk()->assertDontSee('PrivateNote');
        $this->actingAs($this->other)->get(route('admin.expenses.edit', $e))->assertNotFound();
        $this->actingAs($this->other)->put(route('admin.expenses.update', $e), ['expense_date' => '2026-10-01', 'amount' => 1])->assertNotFound();
        $this->actingAs($this->other)->delete(route('admin.expenses.destroy', $e))->assertNotFound();
        $this->actingAs($this->other)->delete(route('admin.expense-categories.destroy', $cat))->assertNotFound();
        $this->actingAs($this->other)->post(route('admin.expenses.store'), ['expense_date' => '2026-10-01', 'amount' => 5, 'expense_category_id' => $cat->id])->assertSessionHasErrors('expense_category_id');
        $this->assertDatabaseHas('expenses', ['id' => $e->id, 'amount' => 500]);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin)->post(route('admin.expenses.store'), ['expense_date' => '', 'amount' => 0])->assertSessionHasErrors(['expense_date', 'amount']);
        $this->actingAs($this->admin)->post(route('admin.expense-categories.store'), ['name' => ''])->assertSessionHasErrors('name');
        $this->actingAs($this->admin)->post(route('admin.expense-categories.store'), ['name' => 'Dup']);
        $this->actingAs($this->admin)->post(route('admin.expense-categories.store'), ['name' => 'Dup'])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('expense_categories', 1);
    }
}
