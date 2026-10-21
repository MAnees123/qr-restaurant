<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\PurchaseOrder;
use App\Models\Finance\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    use Concerns;

    public function index(Request $request)
    {
        $q = PurchaseOrder::with('supplier')->where('restaurant_id', $this->rid())->latest('order_date')->latest('id');
        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }
        if ($request->query('payment') === 'unpaid') {
            $q->where('status', '!=', 'cancelled')->whereColumn('paid_amount', '<', 'total_amount');
        }
        $payable = (float) PurchaseOrder::where('restaurant_id', $this->rid())->where('status', '!=', 'cancelled')
            ->selectRaw('COALESCE(SUM(total_amount - paid_amount),0) as due')->value('due');
        return view('finance.purchases.index', ['orders' => $q->paginate(15)->withQueryString(), 'payable' => $payable]);
    }

    public function create()
    {
        return view('finance.purchases.create', ['suppliers' => Supplier::where('restaurant_id', $this->rid())->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|integer',
            'order_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:200',
            'items.*.quantity' => 'required|numeric|gt:0|max:9999999',
            'items.*.unit_cost' => 'required|numeric|min:0|max:9999999',
        ]);
        $supplier = Supplier::where('restaurant_id', $this->rid())->find($data['supplier_id']);
        if (!$supplier) {
            return back()->withErrors(['supplier_id' => 'Select a valid supplier.'])->withInput();
        }

        $po = DB::transaction(function () use ($data) {
            $count = PurchaseOrder::where('restaurant_id', $this->rid())->lockForUpdate()->count() + 1;
            $po = PurchaseOrder::create([
                'restaurant_id' => $this->rid(),
                'supplier_id' => $data['supplier_id'],
                'po_number' => 'PO-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT),
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
                'status' => 'ordered',
            ]);
            $total = 0;
            foreach ($data['items'] as $row) {
                $line = round($row['quantity'] * $row['unit_cost'], 2);
                $total += $line;
                $po->items()->create(['description' => $row['description'], 'quantity' => $row['quantity'], 'unit_cost' => $row['unit_cost'], 'line_total' => $line]);
            }
            $po->update(['total_amount' => $total]);
            return $po;
        });

        return redirect()->route('admin.purchases.show', $po)->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchase)
    {
        $this->own($purchase)->load('items', 'supplier');
        return view('finance.purchases.show', ['po' => $purchase]);
    }

    public function receive(PurchaseOrder $purchase)
    {
        $this->own($purchase);
        if ($purchase->status !== 'ordered') {
            return back()->withErrors(['status' => 'Only orders in "ordered" status can be received.']);
        }
        $purchase->update(['status' => 'received', 'received_at' => now()]);
        return back()->with('success', 'Marked as received.');
    }

    public function cancel(PurchaseOrder $purchase)
    {
        $this->own($purchase);
        if ($purchase->status !== 'ordered' || (float) $purchase->paid_amount > 0) {
            return back()->withErrors(['status' => 'Only unpaid orders that are not yet received can be cancelled.']);
        }
        $purchase->update(['status' => 'cancelled']);
        return back()->with('success', 'Purchase order cancelled.');
    }

    public function pay(Request $request, PurchaseOrder $purchase)
    {
        $this->own($purchase);
        if ($purchase->status === 'cancelled') {
            return back()->withErrors(['amount' => 'Cancelled orders cannot be paid.']);
        }
        $data = $request->validate(['amount' => 'nullable|numeric|gt:0']);
        $amount = isset($data['amount']) ? (float) $data['amount'] : $purchase->due;
        if ($amount > $purchase->due + 0.001) {
            return back()->withErrors(['amount' => 'Payment exceeds the amount due (' . number_format($purchase->due, 2) . ').']);
        }
        $purchase->update(['paid_amount' => round((float) $purchase->paid_amount + $amount, 2)]);
        return back()->with('success', 'Payment recorded.');
    }

    public function destroy(PurchaseOrder $purchase)
    {
        $this->own($purchase);
        if ($purchase->status === 'received') {
            return back()->withErrors(['status' => 'Received orders cannot be deleted.']);
        }
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase order deleted.');
    }
}
