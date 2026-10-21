<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    use Concerns;

    public const TYPES = ['asset', 'liability', 'income', 'expense'];

    private function rules(?int $ignore = null): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('accounts')->where('restaurant_id', $this->rid())->ignore($ignore)],
            'name' => 'required|string|max:150',
            'type' => ['required', Rule::in(self::TYPES)],
        ];
    }

    public function index()
    {
        return view('finance.accounts.index', ['accounts' => Account::where('restaurant_id', $this->rid())->orderBy('code')->get(), 'types' => self::TYPES]);
    }

    public function store(Request $request)
    {
        Account::create($request->validate($this->rules()) + ['restaurant_id' => $this->rid()]);
        return redirect()->route('admin.accounts.index')->with('success', 'Account added.');
    }

    public function update(Request $request, Account $account)
    {
        $this->own($account)->update($request->validate($this->rules($account->id)));
        return redirect()->route('admin.accounts.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $account)
    {
        $this->own($account);
        if ($account->lines()->exists()) {
            return back()->withErrors(['account' => 'This account has journal lines and cannot be deleted.']);
        }
        $account->delete();
        return redirect()->route('admin.accounts.index')->with('success', 'Account deleted.');
    }

    /** Convenience: create a standard starter chart of accounts when none exists. */
    public function seedDefaults()
    {
        if (Account::where('restaurant_id', $this->rid())->exists()) {
            return back()->withErrors(['account' => 'Accounts already exist.']);
        }
        foreach ([['1000', 'Cash', 'asset'], ['1100', 'Bank', 'asset'], ['2000', 'Accounts Payable', 'liability'], ['4000', 'Sales Revenue', 'income'], ['5000', 'Cost of Goods', 'expense'], ['6000', 'Operating Expenses', 'expense']] as [$c, $n, $t]) {
            Account::create(['restaurant_id' => $this->rid(), 'code' => $c, 'name' => $n, 'type' => $t]);
        }
        return back()->with('success', 'Starter chart of accounts created.');
    }
}
