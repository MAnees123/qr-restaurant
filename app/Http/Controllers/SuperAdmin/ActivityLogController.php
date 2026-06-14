<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search in description
        if ($request->filled('q')) {
            $query->where('description', 'like', '%' . $request->q . '%');
        }

        $logs = $query->paginate(25);

        // Get unique action types for the filter dropdown
        $actions = Schema::hasTable('activity_logs')
            ? ActivityLog::select('action')->distinct()->pluck('action')
            : collect();

        return view('superadmin.activity-logs.index', compact('logs', 'actions'));
    }
}
