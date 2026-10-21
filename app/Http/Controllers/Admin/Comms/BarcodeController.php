<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Services\Comms\Barcode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarcodeController extends Controller
{
    private function items()
    {
        return MenuItem::where('restaurant_id', auth()->user()->restaurant_id);
    }

    private function own(MenuItem $item): MenuItem
    {
        abort_unless($item->restaurant_id === auth()->user()->restaurant_id, 404);
        return $item;
    }

    public function index(Request $request)
    {
        $q = $this->items()->orderBy('name');
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('barcode', 'like', $term));
        }
        return view('admin.comms.barcodes', ['items' => $q->paginate(20)->withQueryString()]);
    }

    public function update(Request $request, MenuItem $item)
    {
        $this->own($item);
        $rid = auth()->user()->restaurant_id;
        $request->validate([
            'barcode' => ['nullable', 'string', 'max:40', 'regex:/^[\x20-\x7E]+$/',
                Rule::unique('menu_items', 'barcode')->where('restaurant_id', $rid)->ignore($item->id)],
        ]);
        $code = trim((string) $request->barcode);
        if (preg_match('/^\d{13}$/', $code) && !Barcode::isValidEan13($code)) {
            return back()->withErrors(['barcode' => 'Invalid EAN-13 check digit. Expected last digit ' . Barcode::ean13CheckDigit(substr($code, 0, 12)) . '.']);
        }
        $item->forceFill(['barcode' => $code === '' ? null : $code])->save();
        return back()->with('success', 'Barcode saved for ' . $item->name . '.');
    }

    public function generate(MenuItem $item)
    {
        $this->own($item);
        $rid = auth()->user()->restaurant_id;
        // 200 + restaurant id (4 digits) + item id (5 digits) => deterministic, unique per item
        $code = Barcode::makeEan13('200' . str_pad((string) ($rid % 10000), 4, '0', STR_PAD_LEFT) . str_pad((string) ($item->id % 100000), 5, '0', STR_PAD_LEFT));
        $exists = $this->items()->where('barcode', $code)->where('id', '!=', $item->id)->exists();
        if ($exists) {
            return back()->with('error', 'Generated code already used by another item; enter one manually.');
        }
        $item->forceFill(['barcode' => $code])->save();
        return back()->with('success', 'Generated EAN-13 ' . $code . ' for ' . $item->name . '.');
    }

    public function svg(MenuItem $item)
    {
        $this->own($item);
        abort_unless($item->barcode, 404);
        return response(Barcode::svg($item->barcode), 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function labels(Request $request)
    {
        $q = $this->items()->whereNotNull('barcode')->where('barcode', '!=', '')->orderBy('name');
        if ($request->filled('ids')) {
            $q->whereIn('id', array_map('intval', (array) $request->ids));
        }
        $items = $q->get()->map(function ($i) {
            $i->barcode_svg = Barcode::svg($i->barcode, 50, 2);
            return $i;
        });
        return view('admin.comms.barcode-labels', ['items' => $items, 'restaurant' => auth()->user()->restaurant]);
    }

    public function scan()
    {
        return view('admin.comms.barcode-scan');
    }

    public function lookup(Request $request)
    {
        $request->validate(['code' => 'required|string|max:64']);
        $item = $this->items()->where('barcode', trim($request->code))->first();
        if (!$item) {
            return response()->json(['found' => false, 'message' => 'No item with this barcode.'], 404);
        }
        return response()->json(['found' => true, 'item' => [
            'id' => $item->id, 'name' => $item->name, 'price' => (float) $item->price, 'barcode' => $item->barcode,
            'is_available' => (bool) $item->is_available,
            'url' => route('admin.menu-items.edit', $item->id),
        ]]);
    }
}
