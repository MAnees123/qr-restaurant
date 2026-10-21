<?php

namespace App\Services\Operations;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\OperationsSetting;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoyaltyService
{
    public static function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/[^0-9+]/', '', (string) $phone);

        return $phone !== '' ? $phone : null;
    }

    public function isActive(Restaurant $restaurant): bool
    {
        return $restaurant->hasFeature('loyalty')
            && OperationsSetting::forRestaurant($restaurant->id)->loyalty_enabled;
    }

    public function account(int $restaurantId, string $phone, ?string $name = null): LoyaltyAccount
    {
        $account = LoyaltyAccount::firstOrCreate(
            ['restaurant_id' => $restaurantId, 'phone' => $phone],
            ['name' => $name, 'balance' => 0, 'lifetime_earned' => 0]
        );
        if ($name && !$account->name) {
            $account->update(['name' => $name]);
        }

        return $account;
    }

    /** Points a paid order earns. */
    public function pointsFor(Order $order, OperationsSetting $s): int
    {
        return (int) floor(max(0, (float) $order->total_amount) * $s->loyalty_points_per_currency);
    }

    /** Award points for a paid order exactly once. */
    public function earnForOrder(Order $order): ?LoyaltyTransaction
    {
        $phone = self::normalizePhone($order->customer_phone);
        $restaurant = $order->restaurant;
        if (!$phone || !$restaurant || !$this->isActive($restaurant)) {
            return null;
        }

        return DB::transaction(function () use ($order, $phone, $restaurant) {
            $exists = LoyaltyTransaction::where('restaurant_id', $order->restaurant_id)
                ->where('order_id', $order->id)->where('type', 'earn')->lockForUpdate()->exists();
            if ($exists) {
                return null;
            }
            $points = $this->pointsFor($order, OperationsSetting::forRestaurant($restaurant->id));
            if ($points < 1) {
                return null;
            }
            $account = $this->account($order->restaurant_id, $phone, $order->customer_name);
            $account->increment('balance', $points);
            $account->increment('lifetime_earned', $points);

            return LoyaltyTransaction::create([
                'restaurant_id' => $order->restaurant_id, 'loyalty_account_id' => $account->id,
                'order_id' => $order->id, 'type' => 'earn', 'points' => $points,
                'note' => 'Order ' . $order->order_number,
            ]);
        });
    }

    /**
     * Validate a redemption request and return [points, discount]. Does not move points.
     * Use commitRedeem() after the order exists.
     */
    public function quoteRedeem(Restaurant $restaurant, ?string $phone, int $points, float $maxDiscountBase): array
    {
        if ($points <= 0) {
            return [0, 0.0];
        }
        if (!$this->isActive($restaurant)) {
            throw ValidationException::withMessages(['redeem_points' => 'Loyalty is not enabled.']);
        }
        $phone = self::normalizePhone($phone);
        $s = OperationsSetting::forRestaurant($restaurant->id);
        $account = $phone ? LoyaltyAccount::where('restaurant_id', $restaurant->id)->where('phone', $phone)->first() : null;
        if (!$account) {
            throw ValidationException::withMessages(['redeem_points' => 'No loyalty account found for this phone number.']);
        }
        if ($points < $s->loyalty_min_redeem) {
            throw ValidationException::withMessages(['redeem_points' => "Minimum redemption is {$s->loyalty_min_redeem} points."]);
        }
        if ($points > $account->balance) {
            throw ValidationException::withMessages(['redeem_points' => "Only {$account->balance} points available."]);
        }
        $discount = round($points * $s->loyalty_point_value, 2);
        if ($discount > $maxDiscountBase) {
            throw ValidationException::withMessages(['redeem_points' => 'Redeemed value cannot exceed the order total.']);
        }

        return [$points, $discount];
    }

    public function commitRedeem(Order $order, string $phone, int $points): void
    {
        DB::transaction(function () use ($order, $phone, $points) {
            $account = LoyaltyAccount::where('restaurant_id', $order->restaurant_id)
                ->where('phone', self::normalizePhone($phone))->lockForUpdate()->firstOrFail();
            if ($account->balance < $points) {
                throw ValidationException::withMessages(['redeem_points' => 'Insufficient points.']);
            }
            $account->decrement('balance', $points);
            LoyaltyTransaction::create([
                'restaurant_id' => $order->restaurant_id, 'loyalty_account_id' => $account->id,
                'order_id' => $order->id, 'type' => 'redeem', 'points' => -$points,
                'note' => 'Redeemed on order ' . $order->order_number,
            ]);
            $order->update(['loyalty_points_redeemed' => $order->loyalty_points_redeemed + $points]);
        });
    }

    /** Give redeemed points back when an unpaid order gets cancelled. */
    public function refundForOrder(Order $order): void
    {
        if ($order->loyalty_points_redeemed < 1 || !$order->customer_phone) {
            return;
        }
        DB::transaction(function () use ($order) {
            if (LoyaltyTransaction::where('order_id', $order->id)->where('restaurant_id', $order->restaurant_id)->where('type', 'refund')->exists()) {
                return;
            }
            $account = LoyaltyAccount::where('restaurant_id', $order->restaurant_id)
                ->where('phone', self::normalizePhone($order->customer_phone))->lockForUpdate()->first();
            if (!$account) {
                return;
            }
            $account->increment('balance', $order->loyalty_points_redeemed);
            LoyaltyTransaction::create([
                'restaurant_id' => $order->restaurant_id, 'loyalty_account_id' => $account->id,
                'order_id' => $order->id, 'type' => 'refund', 'points' => $order->loyalty_points_redeemed,
                'note' => 'Order ' . $order->order_number . ' cancelled',
            ]);
        });
    }
}
