<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Platform\PwaSetting;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PwaController extends Controller
{
    private function tenant(int|string $id): Restaurant
    {
        $r = Restaurant::find($id);
        abort_unless($r && $r->is_active && ! $r->is_suspended && $r->hasFeature('mobile_app'), 404);
        return $r;
    }

    public function manifest($restaurant)
    {
        $r = $this->tenant($restaurant);
        $s = PwaSetting::forRestaurant($r->id);
        $icons = [['src' => route('pwa.icon', $r->id), 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any']];
        if ($r->logo && Storage::disk('public')->exists($r->logo)) {
            $icons[] = ['src' => asset('storage/' . $r->logo), 'sizes' => '512x512', 'purpose' => 'any'];
        }
        return response()->json([
            'name'             => $r->name,
            'short_name'       => $s->short_name ?: mb_substr($r->name, 0, 12),
            'start_url'        => url('/'),
            'scope'            => url('/'),
            'display'          => 'standalone',
            'theme_color'      => $s->theme_color,
            'background_color' => $s->background_color,
            'icons'            => $icons,
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function serviceWorker($restaurant)
    {
        $r = $this->tenant($restaurant);
        $offline = route('pwa.offline', $r->id, false);
        $js = <<<JS
const CACHE = 'qr-pwa-{$r->id}-v1';
const OFFLINE_URL = '{$offline}';
self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE).then(c => c.add(OFFLINE_URL)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k.startsWith('qr-pwa-{$r->id}-') && k !== CACHE).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
  if (e.request.mode === 'navigate') {
    e.respondWith(fetch(e.request).catch(() => caches.match(OFFLINE_URL)));
  }
});
JS;
        return response($js, 200, [
            'Content-Type'           => 'application/javascript',
            'Service-Worker-Allowed' => '/',
            'Cache-Control'          => 'no-cache',
        ]);
    }

    public function offline($restaurant)
    {
        return view('platform.pwa.offline', ['restaurant' => $this->tenant($restaurant)]);
    }

    public function icon($restaurant)
    {
        $r = $this->tenant($restaurant);
        $s = PwaSetting::forRestaurant($r->id);
        $letter = e(mb_strtoupper(mb_substr($r->name, 0, 1)));
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="512" height="512" rx="96" fill="' . e($s->theme_color) . '"/>'
            . '<text x="256" y="340" font-family="Arial,sans-serif" font-size="280" font-weight="700" text-anchor="middle" fill="#fff">' . $letter . '</text></svg>';
        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function install($restaurant)
    {
        return view('platform.pwa.install', ['restaurant' => $this->tenant($restaurant)]);
    }

    public function admin()
    {
        $restaurant = auth()->user()->restaurant;
        return view('platform.pwa.admin', [
            'restaurant' => $restaurant,
            'setting'    => PwaSetting::forRestaurant($restaurant->id),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'short_name'       => 'nullable|string|max:30',
            'theme_color'      => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        PwaSetting::updateOrCreate(['restaurant_id' => auth()->user()->restaurant_id], $data);
        return redirect()->route('admin.pwa.index')->with('success', 'App settings saved.');
    }
}
