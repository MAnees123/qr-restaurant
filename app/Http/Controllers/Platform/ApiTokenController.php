<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Platform\ApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::where('restaurant_id', auth()->user()->restaurant_id)->latest('id')->get();
        return view('platform.api.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'scope' => 'required|in:read,write',
        ]);
        [, $plain] = ApiToken::issue(auth()->user()->restaurant_id, $data['name'], $data['scope'] === 'write', auth()->id());

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'Token created. Copy it now; it will not be shown again.')
            ->with('new_token', $plain);
    }

    public function destroy(ApiToken $apiToken)
    {
        if ($apiToken->restaurant_id !== auth()->user()->restaurant_id) {
            abort(404);
        }
        $apiToken->update(['revoked_at' => now()]);
        return redirect()->route('admin.api-tokens.index')->with('success', 'Token revoked.');
    }
}
