<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use App\Models\Comms\CommsSetting;
use App\Models\Order;
use App\Services\Comms\EscPos;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function index()
    {
        $rid = auth()->user()->restaurant_id;
        return view('admin.comms.printer', [
            'settings' => CommsSetting::read($rid, 'printer'),
            'orders'   => Order::where('restaurant_id', $rid)->with('table')->latest('id')->limit(15)->get(),
            'lastId'   => (int) Order::where('restaurant_id', $rid)->max('id'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'paper_width'  => 'required|in:58,80',
            'header'       => 'nullable|string|max:200',
            'footer'       => 'nullable|string|max:200',
            'printer_host' => ['nullable', 'string', 'max:190', 'regex:/^[A-Za-z0-9.\-]+$/'],
            'printer_port' => 'nullable|integer|min:1|max:65535',
        ]);
        CommsSetting::write(auth()->user()->restaurant_id, 'printer', [
            'paper_width'  => (int) $data['paper_width'],
            'header'       => $data['header'] ?? '',
            'footer'       => $data['footer'] ?? '',
            'printer_host' => $data['printer_host'] ?? '',
            'printer_port' => (int) ($data['printer_port'] ?? 9100),
            'auto_print'   => $request->boolean('auto_print'),
            'show_logo'    => $request->boolean('show_logo'),
        ]);
        return redirect()->route('admin.printer.index')->with('success', 'Printer settings saved.');
    }

    public function receipt(Order $order, string $type)
    {
        abort_unless(in_array($type, ['kitchen', 'bill'], true), 404);
        $restaurant = auth()->user()->restaurant;
        abort_unless($order->restaurant_id === $restaurant->id, 404);
        $order->load('orderItems.menuItem', 'table');
        return view('admin.comms.receipt', $this->receiptData($restaurant, $order, $type, false));
    }

    public function testPrint(Request $request)
    {
        $restaurant = auth()->user()->restaurant;
        $type = $request->query('type') === 'bill' ? 'bill' : 'kitchen';
        $order = new Order(['order_number' => 'TEST0001', 'status' => 'pending', 'subtotal' => 30, 'discount_amount' => 0, 'total_amount' => 30, 'notes' => 'Sample note']);
        $order->created_at = now();
        $items = collect([
            (object) ['quantity' => 2, 'unit_price' => 10, 'subtotal' => 20, 'special_instructions' => 'No onions', 'menuItem' => (object) ['name' => 'Sample Burger']],
            (object) ['quantity' => 1, 'unit_price' => 10, 'subtotal' => 10, 'special_instructions' => null, 'menuItem' => (object) ['name' => 'Sample Fries']],
        ]);
        $order->setRelation('orderItems', $items);
        $order->setRelation('table', (object) ['table_number' => 'T-0']);
        return view('admin.comms.receipt', $this->receiptData($restaurant, $order, $type, true));
    }

    public function networkTest()
    {
        $s = CommsSetting::read(auth()->user()->restaurant_id, 'printer');
        if (empty($s['printer_host'])) {
            return back()->with('error', 'Enter a printer host / IP and save first.');
        }
        $payload = "\x1B@\x1Ba\x01TEST PRINT\n" . auth()->user()->restaurant->name . "\n" . now()->toDateTimeString() . "\n\n\n\x1DV\x00";
        $err = EscPos::send($s['printer_host'], (int) $s['printer_port'], $payload);
        return $err
            ? back()->with('error', $err)
            : back()->with('success', 'Test data sent to ' . $s['printer_host'] . ':' . $s['printer_port'] . ' (the printer must be reachable and ESC/POS compatible).');
    }

    /** New orders since an id; used by the auto-print monitor on the printer page. */
    public function poll(Request $request)
    {
        $after = (int) $request->query('after', 0);
        $ids = Order::where('restaurant_id', auth()->user()->restaurant_id)->where('id', '>', $after)->orderBy('id')->pluck('id');
        return response()->json(['ids' => $ids, 'last' => (int) ($ids->last() ?? $after)]);
    }

    private function receiptData($restaurant, $order, string $type, bool $test): array
    {
        return [
            'restaurant' => $restaurant,
            'order'      => $order,
            'type'       => $type,
            'test'       => $test,
            's'          => CommsSetting::read($restaurant->id, 'printer'),
            'branding'   => $restaurant->hasFeature('custom_branding') ? CommsSetting::read($restaurant->id, 'branding') : null,
        ];
    }
}
