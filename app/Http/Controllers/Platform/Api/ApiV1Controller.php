<?php

namespace App\Http\Controllers\Platform\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiV1Controller extends Controller
{
    private function rid(Request $r): int
    {
        return (int) $r->attributes->get('api_restaurant_id');
    }

    public function categories(Request $r)
    {
        return response()->json(['data' => MenuCategory::where('restaurant_id', $this->rid($r))
            ->orderBy('sort_order')->get(['id', 'name', 'description', 'sort_order', 'is_active'])]);
    }

    public function items(Request $r)
    {
        $q = MenuItem::where('restaurant_id', $this->rid($r));
        if ($r->filled('category_id')) {
            $q->where('menu_category_id', (int) $r->query('category_id'));
        }
        return response()->json(['data' => $q->orderBy('sort_order')
            ->get(['id', 'menu_category_id', 'name', 'description', 'price', 'is_available'])]);
    }

    public function tables(Request $r)
    {
        return response()->json(['data' => Table::where('restaurant_id', $this->rid($r))
            ->get(['id', 'table_number', 'capacity', 'is_active', 'status'])]);
    }

    public function orders(Request $r)
    {
        $q = Order::where('restaurant_id', $this->rid($r))->latest('id');
        if ($r->filled('status')) {
            $q->where('status', $r->query('status'));
        }
        $p = $q->paginate(min(100, max(1, (int) $r->query('per_page', 25))),
            ['id', 'order_number', 'table_id', 'status', 'payment_status', 'total_amount', 'created_at']);
        return response()->json($p);
    }

    public function order(Request $r, string $id)
    {
        $order = Order::with('orderItems')->where('restaurant_id', $this->rid($r))->find($id);
        if (! $order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }
        return response()->json(['data' => $order]);
    }

    public function updateStatus(Request $r, string $id)
    {
        $order = Order::where('restaurant_id', $this->rid($r))->find($id);
        if (! $order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }
        $v = Validator::make($r->all(), ['status' => 'required|in:pending,confirmed,preparing,ready,served,cancelled']);
        if ($v->fails()) {
            return response()->json(['error' => 'Validation failed.', 'errors' => $v->errors()], 422);
        }
        $order->update(['status' => $r->input('status')]);
        return response()->json(['data' => $order->only(['id', 'order_number', 'status'])]);
    }
}
