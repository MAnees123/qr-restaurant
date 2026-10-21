<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\OperationsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoyaltyController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function account(LoyaltyAccount $account): LoyaltyAccount
    {
        abort_unless($account->restaurant_id === $this->rid(), 404);

        return $account;
    }

    public function index(Request $request)
    {
        $accounts = LoyaltyAccount::where('restaurant_id', $this->rid())
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w->where('phone', 'like', '%' . $request->q . '%')->orWhere('name', 'like', '%' . $request->q . '%')))
            ->orderByDesc('balance')->paginate(20)->withQueryString();
        $settings = OperationsSetting::forRestaurant($this->rid());
        $outstanding = (int) LoyaltyAccount::where('restaurant_id', $this->rid())->sum('balance');

        return view('operations.loyalty.index', compact('accounts', 'settings', 'outstanding'));
    }

    public function settings(Request $request)
    {
        $data = $request->validate([
            'loyalty_points_per_currency' => 'required|numeric|min:0|max:1000',
            'loyalty_point_value' => 'required|numeric|min:0|max:100000',
            'loyalty_min_redeem' => 'required|integer|min:1|max:1000000',
        ]);
        $data['loyalty_enabled'] = $request->boolean('loyalty_enabled');
        OperationsSetting::forRestaurant($this->rid())->update($data);

        return back()->with('success', 'Loyalty settings saved.');
    }

    public function show(LoyaltyAccount $account)
    {
        $this->account($account);
        $transactions = $account->transactions()->paginate(20);

        return view('operations.loyalty.show', compact('account', 'transactions'));
    }

    public function adjust(Request $request, LoyaltyAccount $account)
    {
        $this->account($account);
        $data = $request->validate([
            'points' => 'required|integer|not_in:0|between:-1000000,1000000',
            'note' => 'required|string|max:190',
        ]);

        DB::transaction(function () use ($account, $data) {
            $locked = LoyaltyAccount::where('id', $account->id)->lockForUpdate()->first();
            if ($locked->balance + $data['points'] < 0) {
                throw ValidationException::withMessages(['points' => 'Adjustment would make the balance negative.']);
            }
            $locked->increment('balance', $data['points']);
            if ($data['points'] > 0) {
                $locked->increment('lifetime_earned', $data['points']);
            }
            LoyaltyTransaction::create([
                'restaurant_id' => $locked->restaurant_id, 'loyalty_account_id' => $locked->id,
                'type' => 'adjust', 'points' => $data['points'], 'note' => $data['note'],
            ]);
        });

        return back()->with('success', 'Balance adjusted.');
    }
}
