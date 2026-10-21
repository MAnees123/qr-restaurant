<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DarkModeController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $value = $request->has('dark_mode') ? $request->boolean('dark_mode') : !$user->dark_mode;
        $user->forceFill(['dark_mode' => $value])->save();

        if ($request->expectsJson()) {
            return response()->json(['dark_mode' => $value]);
        }
        return back()->with('success', $value ? 'Dark mode enabled.' : 'Dark mode disabled.');
    }
}
