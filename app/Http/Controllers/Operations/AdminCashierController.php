<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Operations\CashSummary;
use Illuminate\Http\Request;

class AdminCashierController extends Controller
{
    public function summary(Request $request, CashSummary $summary)
    {
        $request->validate(['date' => 'nullable|date']);

        return view('operations.cashier.summary', ['summary' => $summary->forDay(auth()->user()->restaurant_id, $request->date), 'layout' => 'layouts.admin']);
    }

    public function receipt(Order $order)
    {
        abort_unless($order->restaurant_id === auth()->user()->restaurant_id, 404);
        $order->load(['table', 'orderItems.menuItem', 'restaurant', 'payments']);

        return view('operations.cashier.receipt', ['order' => $order, 'back' => route('admin.cashier.summary')]);
    }
}
