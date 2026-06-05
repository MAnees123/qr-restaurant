<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        $item = MenuItem::findOrFail($request->menu_item_id);
        
        $cart = session()->get('cart', []);

        $cartId = $item->id . '_' . md5($request->special_instructions ?? '');

        if (isset($cart[$cartId])) {
            $cart[$cartId]['quantity'] += $request->quantity;
        } else {
            $cart[$cartId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $request->quantity,
                'special_instructions' => $request->special_instructions,
                'image' => $item->image,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Item added to cart',
            'cartCount' => collect($cart)->sum('quantity')
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_id])) {
            unset($cart[$request->cart_id]);
            session()->put('cart', $cart);
        }

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_id])) {
            $cart[$request->cart_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json(['message' => 'Cart updated']);
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $discount = \App\Models\Discount::where('code', $request->code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$discount) {
            return response()->json(['error' => 'Invalid or expired coupon code'], 422);
        }

        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return response()->json(['error' => 'Coupon usage limit reached'], 422);
        }

        session()->put('discount', [
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
        ]);

        return response()->json(['message' => 'Coupon applied successfully', 'discount' => session('discount')]);
    }

    public function removeDiscount()
    {
        session()->forget('discount');
        return response()->json(['message' => 'Coupon removed']);
    }
}
