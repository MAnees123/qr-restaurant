<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\OperationsSetting;
use App\Models\Order;
use App\Models\Restaurant;
use App\Services\Operations\LoyaltyService;
use App\Services\Operations\OrderBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Public (guest) online ordering: pickup, and delivery when the delivery feature is granted. */
class OnlineOrderController extends Controller
{
    private function open(Restaurant $restaurant): array
    {
        abort_unless($restaurant->is_active && !$restaurant->is_suspended, 404);
        $types = array_values(array_filter([
            $restaurant->hasFeature('online_orders') ? 'pickup' : null,
            $restaurant->hasFeature('delivery') ? 'delivery' : null,
        ]));
        abort_if(!$types, 404);

        return $types;
    }

    public function show(Restaurant $restaurant)
    {
        $types = $this->open($restaurant);
        $settings = OperationsSetting::forRestaurant($restaurant->id);
        $categories = MenuCategory::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')->get()->filter(fn ($c) => $c->menuItems->isNotEmpty());
        $loyaltyOn = app(LoyaltyService::class)->isActive($restaurant);

        return view('operations.online.show', compact('restaurant', 'types', 'settings', 'categories', 'loyaltyOn'));
    }

    public function place(Request $request, Restaurant $restaurant, OrderBuilder $builder, LoyaltyService $loyalty)
    {
        $types = $this->open($restaurant);
        $settings = OperationsSetting::forRestaurant($restaurant->id);
        abort_unless($settings->online_ordering_open, 403, 'Online ordering is currently closed.');

        $data = $request->validate([
            'order_type' => ['required', Rule::in($types)],
            'customer_name' => 'required|string|max:120',
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,30}$/'],
            'delivery_address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'qty' => 'required|array',
            'qty.*' => 'nullable|integer|min:0|max:99',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        $lines = $builder->resolveLines($restaurant, $data['qty']);
        $subtotal = $builder->subtotal($lines);
        $isDelivery = $data['order_type'] === 'delivery';

        if ($isDelivery && $subtotal < $settings->delivery_min_order) {
            return back()->withInput()->withErrors(['qty' => 'Minimum order for delivery is ' . number_format($settings->delivery_min_order, 2) . '.']);
        }

        $phone = LoyaltyService::normalizePhone($data['customer_phone']);
        [$points, $discount] = $loyalty->quoteRedeem($restaurant, $phone, (int) ($data['redeem_points'] ?? 0), $subtotal);

        $order = DB::transaction(function () use ($builder, $loyalty, $restaurant, $lines, $data, $phone, $isDelivery, $settings, $points, $discount) {
            $order = $builder->create($restaurant, $lines, [
                'order_type' => $data['order_type'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $phone,
                'delivery_address' => $isDelivery ? $data['delivery_address'] : null,
                'delivery_fee' => $isDelivery ? $settings->delivery_fee : 0,
                'delivery_status' => $isDelivery ? 'pending' : null,
                'notes' => $data['notes'] ?? null,
                'discount_amount' => $discount,
            ]);
            if ($points > 0) {
                $loyalty->commitRedeem($order, $phone, $points);
            }

            return $order;
        });

        return redirect()->route('operations.online.track', [$restaurant, $order->order_number]);
    }

    public function track(Restaurant $restaurant, string $orderNumber)
    {
        $this->open($restaurant);
        $order = Order::where('restaurant_id', $restaurant->id)->where('order_number', $orderNumber)
            ->whereIn('order_type', ['pickup', 'delivery'])->with('orderItems.menuItem')->firstOrFail();

        return view('operations.online.track', compact('restaurant', 'order'));
    }
}
