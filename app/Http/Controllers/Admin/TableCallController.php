<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableCall;
use Illuminate\Http\Request;

class TableCallController extends Controller
{
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id;
        $calls = TableCall::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['pending', 'accepted'])
            ->with('table')
            ->latest()
            ->get();
            
        return response()->json($calls);
    }

    public function accept(TableCall $call)
    {
        if ($call->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        
        $call->update(['status' => 'accepted']);
        
        return response()->json(['success' => true]);
    }

    public function complete(TableCall $call)
    {
        if ($call->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        
        $call->update(['status' => 'completed']);
        
        return response()->json(['success' => true]);
    }
}
