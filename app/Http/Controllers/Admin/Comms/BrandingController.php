<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use App\Models\Comms\CommsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function edit()
    {
        $restaurant = auth()->user()->restaurant;
        return view('admin.comms.branding', [
            'restaurant' => $restaurant,
            'settings'   => CommsSetting::read($restaurant->id, 'branding'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'primary'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'tagline'   => 'nullable|string|max:120',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'favicon'   => 'nullable|image|mimes:png,jpg,jpeg,webp,ico|max:512',
        ]);
        $rid = auth()->user()->restaurant_id;
        $current = CommsSetting::read($rid, 'branding');

        $values = ['primary' => $data['primary'], 'secondary' => $data['secondary'], 'tagline' => $data['tagline'] ?? ''];
        foreach (['logo', 'favicon'] as $f) {
            if ($request->hasFile($f)) {
                if (!empty($current[$f])) {
                    Storage::disk('public')->delete($current[$f]);
                }
                $values[$f] = $request->file($f)->store("branding/$rid", 'public');
            } elseif ($request->boolean("remove_$f")) {
                if (!empty($current[$f])) {
                    Storage::disk('public')->delete($current[$f]);
                }
                $values[$f] = null;
            }
        }
        CommsSetting::write($rid, 'branding', $values);

        return redirect()->route('admin.branding.edit')->with('success', 'Branding saved.');
    }
}
