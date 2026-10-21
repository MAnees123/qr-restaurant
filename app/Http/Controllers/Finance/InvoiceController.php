<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use App\Models\Finance\TaxRate;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use Concerns;

    public function index(Request $request)
    {
        $q = Invoice::with('order')->where('restaurant_id', $this->rid())->latest('id');
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('invoice_number', 'like', "%$s%")->orWhere('customer_name', 'like', "%$s%")
                ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%$s%")));
        }
        if ($request->filled('from')) {
            $q->whereDate('invoice_date', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('invoice_date', '<=', $request->query('to'));
        }
        $pending = Order::where('restaurant_id', $this->rid())->where('payment_status', 'paid')->where('status', '!=', 'cancelled')
            ->whereNotIn('id', Invoice::where('restaurant_id', $this->rid())->select('order_id'))
            ->latest()->limit(20)->get();
        return view('finance.invoices.index', ['invoices' => $q->paginate(15)->withQueryString(), 'pending' => $pending]);
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['order_id' => 'required|integer']);
        $order = Order::where('restaurant_id', $this->rid())->find($data['order_id']);
        abort_unless($order, 404);
        if ($order->payment_status !== 'paid') {
            return back()->withErrors(['order_id' => 'Invoices can only be created for paid orders.']);
        }
        $existing = Invoice::where('restaurant_id', $this->rid())->where('order_id', $order->id)->first();
        if ($existing) {
            return redirect()->route('admin.invoices.show', $existing);
        }

        $tax = auth()->user()->restaurant->hasFeature('tax_mgmt')
            ? TaxRate::where('restaurant_id', $this->rid())->where('is_active', true)->where('is_default', true)->first()
            : null;

        $invoice = DB::transaction(function () use ($order, $tax) {
            $seq = (int) Invoice::where('restaurant_id', $this->rid())->lockForUpdate()->max('sequence') + 1;
            $gross = round((float) $order->total_amount, 2);
            $rate = $tax ? (float) $tax->rate : 0.0;
            if ($tax && $tax->type === 'inclusive') {
                $total = $gross;
                $taxAmt = round($gross - $gross / (1 + $rate / 100), 2);
                $subtotal = round($gross - $taxAmt, 2);
            } else {
                $subtotal = $gross;
                $taxAmt = round($gross * $rate / 100, 2);
                $total = round($gross + $taxAmt, 2);
            }
            return Invoice::create([
                'restaurant_id' => $this->rid(), 'order_id' => $order->id, 'sequence' => $seq,
                'invoice_number' => 'INV-' . str_pad((string) $seq, 6, '0', STR_PAD_LEFT),
                'invoice_date' => now()->toDateString(), 'subtotal' => $subtotal,
                'tax_rate' => $rate, 'tax_name' => $tax?->name, 'tax_type' => $tax?->type,
                'tax_amount' => $taxAmt, 'total' => $total,
            ]);
        });

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice ' . $invoice->invoice_number . ' created.');
    }

    public function show(Invoice $invoice)
    {
        $this->own($invoice)->load('order.orderItems.menuItem', 'order.table');
        return view('finance.invoices.show', ['invoice' => $invoice, 'restaurant' => auth()->user()->restaurant]);
    }
}
