<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use App\Models\Comms\CommsSetting;
use App\Models\Table;
use App\Services\Comms\QrRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrLogoController extends Controller
{
    public function edit()
    {
        $restaurant = auth()->user()->restaurant;
        $tables = Table::where('restaurant_id', $restaurant->id)->with('qrCode')->orderBy('table_number')->get();
        return view('admin.comms.qr-logo', [
            'restaurant' => $restaurant,
            'settings'   => CommsSetting::read($restaurant->id, 'qr_logo'),
            'tables'     => $tables,
            'preview'    => $tables->isNotEmpty() ? QrRenderer::svg($restaurant, QrRenderer::menuUrl($tables->first()), null, 220) : null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'source'       => 'required|in:restaurant,custom',
            'size_percent' => 'required|integer|min:10|max:30',
            'background'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'foreground'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo'         => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);
        $rid = auth()->user()->restaurant_id;
        $current = CommsSetting::read($rid, 'qr_logo');

        $values = [
            'enabled' => $request->boolean('enabled'), 'rounded' => $request->boolean('rounded'),
            'source' => $data['source'], 'size_percent' => (int) $data['size_percent'],
            'background' => $data['background'], 'foreground' => $data['foreground'],
        ];
        if ($request->hasFile('logo')) {
            if (!empty($current['logo'])) {
                Storage::disk('public')->delete($current['logo']);
            }
            $values['logo'] = $request->file('logo')->store("qr-logo/$rid", 'public');
        }
        CommsSetting::write($rid, 'qr_logo', $values);

        return redirect()->route('admin.qr-logo.edit')->with('success', 'QR logo settings saved.');
    }

    /** SVG of a table QR (with logo overlay); tenant scoped. ?download=1 forces download. */
    public function svg(Request $request, Table $table)
    {
        abort_unless($table->restaurant_id === auth()->user()->restaurant_id, 404);
        $svg = QrRenderer::svg(auth()->user()->restaurant, QrRenderer::menuUrl($table->load('qrCode')), null, 600);
        $headers = ['Content-Type' => 'image/svg+xml'];
        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="table-' . preg_replace('/[^A-Za-z0-9_-]/', '', $table->table_number) . '-qr.svg"';
        }
        return response($svg, 200, $headers);
    }
}
