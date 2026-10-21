<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\Expense;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\PurchaseOrder;
use App\Models\Finance\Supplier;

class AccountingTest extends FinanceTestCase
{
    protected function setUp(): void { parent::setUp(); $this->setUpTenants('accounting'); }

    private function acct(string $code, string $type, $r = null): Account
    {
        return Account::create(['restaurant_id' => ($r ?? $this->rest)->id, 'code' => $code, 'name' => "Acct $code", 'type' => $type]);
    }

    public function test_accounts_crud_and_defaults(): void
    {
        $this->actingAs($this->admin)->post(route('admin.accounts.store'), ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'])->assertRedirect();
        $a = Account::firstOrFail();
        $this->actingAs($this->admin)->put(route('admin.accounts.update', $a), ['code' => '1000', 'name' => 'Cash on hand', 'type' => 'asset']);
        $this->assertSame('Cash on hand', $a->fresh()->name);
        $this->actingAs($this->admin)->post(route('admin.accounts.store'), ['code' => '1000', 'name' => 'Dup', 'type' => 'asset'])->assertSessionHasErrors('code');
        $this->actingAs($this->admin)->post(route('admin.accounts.store'), ['code' => '9', 'name' => 'Bad', 'type' => 'equity'])->assertSessionHasErrors('type');
        $this->actingAs($this->admin)->delete(route('admin.accounts.destroy', $a));
        $this->assertDatabaseCount('accounts', 0);
        $this->actingAs($this->admin)->post(route('admin.accounts.defaults'));
        $this->assertDatabaseCount('accounts', 6);
    }

    public function test_journal_entry_balanced_and_trial_balance(): void
    {
        $cash = $this->acct('1000', 'asset');
        $sales = $this->acct('4000', 'income');
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '2026-10-02', 'memo' => 'Cash sale', 'lines' => [
            ['account_id' => $cash->id, 'debit' => 300, 'credit' => ''], ['account_id' => $sales->id, 'debit' => '', 'credit' => 300]]])->assertRedirect(route('admin.journals.index'));
        $this->assertDatabaseCount('journal_lines', 2);
        // unbalanced rejected
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '2026-10-02', 'memo' => 'Bad', 'lines' => [
            ['account_id' => $cash->id, 'debit' => 100], ['account_id' => $sales->id, 'credit' => 90]]])->assertSessionHasErrors('lines');
        $this->assertDatabaseCount('journal_entries', 1);
        $this->actingAs($this->admin)->get(route('admin.journals.index'))->assertOk()->assertSee('Cash sale');
        $res = $this->actingAs($this->admin)->get(route('admin.accounting.trial-balance', ['from' => '2026-10-01', 'to' => '2026-10-31']))->assertOk();
        $res->assertSee('Totals')->assertSeeInOrder(['300.00', '300.00']);
        // deleting an account with lines is blocked
        $this->actingAs($this->admin)->delete(route('admin.accounts.destroy', $cash))->assertSessionHasErrors('account');
        $this->actingAs($this->admin)->delete(route('admin.journals.destroy', JournalEntry::first()));
        $this->assertDatabaseCount('journal_lines', 0);
    }

    public function test_profit_loss_from_orders_expenses_purchases(): void
    {
        $this->paidOrder($this->rest, 1000, ['created_at' => '2026-10-05 12:00:00']);
        $this->paidOrder($this->rest, 500, ['created_at' => '2026-10-06 12:00:00']);
        $this->paidOrder($this->rest, 999, ['created_at' => '2026-10-06 12:00:00', 'status' => 'cancelled']);
        $this->paidOrder($this->rest, 888, ['created_at' => '2026-10-06 12:00:00', 'payment_status' => 'pending']);
        $this->paidOrder($this->rest, 777, ['created_at' => '2026-09-06 12:00:00']);
        $this->paidOrder($this->other->restaurant, 5555, ['created_at' => '2026-10-06 12:00:00']);
        Expense::create(['restaurant_id' => $this->rest->id, 'expense_date' => '2026-10-07', 'amount' => 200]);
        $sup = Supplier::create(['restaurant_id' => $this->rest->id, 'name' => 'S']);
        PurchaseOrder::create(['restaurant_id' => $this->rest->id, 'supplier_id' => $sup->id, 'po_number' => 'PO-1', 'order_date' => '2026-10-08', 'status' => 'received', 'total_amount' => 300]);
        PurchaseOrder::create(['restaurant_id' => $this->rest->id, 'supplier_id' => $sup->id, 'po_number' => 'PO-2', 'order_date' => '2026-10-08', 'status' => 'ordered', 'total_amount' => 9999]);
        $sales = $this->acct('4100', 'income');
        $cash = $this->acct('1000', 'asset');
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '2026-10-09', 'memo' => 'Catering', 'lines' => [
            ['account_id' => $cash->id, 'debit' => 100], ['account_id' => $sales->id, 'credit' => 100]]]);

        $this->actingAs($this->admin)->get(route('admin.accounting.profit-loss', ['from' => '2026-10-01', 'to' => '2026-10-31']))->assertOk()
            ->assertSeeInOrder(['Sales revenue', '1,500.00', 'Acct 4100', '100.00', 'Total income', '1,600.00',
                'Operating expenses', '200.00', 'Purchases (received)', '300.00', 'Total expenses', '500.00', 'Net profit', '1,100.00']);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $u = $this->adminWith(['reports']);
        foreach (['admin.accounts.index', 'admin.journals.index', 'admin.accounting.trial-balance', 'admin.accounting.profit-loss'] as $r) {
            $this->actingAs($u)->get(route($r))->assertForbidden();
        }
    }

    public function test_other_tenant_isolated(): void
    {
        $mine = $this->acct('1000', 'asset');
        $theirs = $this->acct('2000', 'liability', $this->other->restaurant);
        $this->actingAs($this->other)->put(route('admin.accounts.update', $mine), ['code' => '1', 'name' => 'x', 'type' => 'asset'])->assertNotFound();
        $this->actingAs($this->other)->delete(route('admin.accounts.destroy', $mine))->assertNotFound();
        // cannot post a journal line against another tenant's account
        $this->actingAs($this->other)->post(route('admin.journals.store'), ['entry_date' => '2026-10-02', 'memo' => 'x', 'lines' => [
            ['account_id' => $mine->id, 'debit' => 10], ['account_id' => $theirs->id, 'credit' => 10]]])->assertSessionHasErrors('lines');
        $this->assertDatabaseCount('journal_entries', 0);
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '2026-10-02', 'memo' => 'Mine', 'lines' => [
            ['account_id' => $mine->id, 'debit' => 10], ['account_id' => $this->acct('4000', 'income')->id, 'credit' => 10]]]);
        $e = JournalEntry::firstOrFail();
        $this->actingAs($this->other)->delete(route('admin.journals.destroy', $e))->assertNotFound();
        $this->actingAs($this->other)->get(route('admin.journals.index'))->assertDontSee('Mine');
    }

    public function test_validation(): void
    {
        $a = $this->acct('1', 'asset');
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '', 'memo' => '', 'lines' => []])->assertSessionHasErrors(['entry_date', 'memo', 'lines']);
        $this->actingAs($this->admin)->post(route('admin.journals.store'), ['entry_date' => '2026-10-01', 'memo' => 'one', 'lines' => [['account_id' => $a->id, 'debit' => 5], ['account_id' => $a->id, 'debit' => 0]]])->assertSessionHasErrors('lines');
        $this->actingAs($this->admin)->post(route('admin.accounts.store'), ['code' => '', 'name' => '', 'type' => ''])->assertSessionHasErrors(['code', 'name', 'type']);
    }
}
