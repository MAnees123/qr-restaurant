<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\TableCall;
use Illuminate\Http\Request;

class TableCallController extends Controller
{
    public function call(Request $request)
    {
        $tableId = session('current_table_id');
        $restaurantId = session('current_restaurant_id');

        if (!$tableId || !$restaurantId) {
            return response()->json(['error' => 'Session expired. Please scan QR again.'], 403);
        }

        // Check if there's already a pending call for this table
        $existingCall = TableCall::where('table_id', $tableId)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->first();

        if ($existingCall) {
            return response()->json(['message' => 'Waiter is already notified!']);
        }

        TableCall::create([
            'restaurant_id' => $restaurantId,
            'table_id' => $tableId,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Waiter has been notified!']);
    }

    public function status()
    {
        $tableId = session('current_table_id');
        $restaurantId = session('current_restaurant_id');

        if (!$tableId || !$restaurantId) {
            return response()->json(['status' => 'idle']);
        }

        $latestCall = TableCall::where('table_id', $tableId)
            ->where('restaurant_id', $restaurantId)
            ->latest()
            ->first();

        return response()->json([
            'status' => $latestCall ? $latestCall->status : 'idle',
            'updated_at' => $latestCall ? $latestCall->updated_at->toDateTimeString() : null
        ]);
    }
}
