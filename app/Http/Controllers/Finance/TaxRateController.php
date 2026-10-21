<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use App\Models\Finance\TaxRate;
use App\Services\Finance\FinanceReports;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    use Concerns;

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default') && $data['is_active'];
        return $data;
    }

    private function clearDefaults(?int $exceptId = null): void
    {
        TaxRate::where('restaurant_id', $this->rid())->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->update(['is_default' => false]);
    }

    public function index()
    {
        return view('finance.taxes.index', ['rates' => TaxRate::where('restaurant_id', $this->rid())->orderByDesc('is_default')->orderBy('name')->get(), 'rate' => new TaxRate(['is_active' => true, 'type' => 'exclusive'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['restaurant_id'] = $this->rid();
        $rate = TaxRate::create($data);
        if ($rate->is_default) {
            $this->clearDefaults($rate->id);
        }
        return redirect()->route('admin.taxes.index')->with('success', 'Tax rate added.');
    }

    public function edit(TaxRate $tax)
    {
        return view('finance.taxes.edit', ['rate' => $this->own($tax)]);
    }

    public function update(Request $request, TaxRate $tax)
    {
        $this->own($tax)->update($this->validated($request));
        if ($tax->fresh()->is_default) {
            $this->clearDefaults($tax->id);
        }
        return redirect()->route('admin.taxes.index')->with('success', 'Tax rate updated.');
    }

    public function destroy(TaxRate $tax)
    {
        $this->own($tax)->delete();
        return redirect()->route('admin.taxes.index')->with('success', 'Tax rate deleted. Existing invoices keep their original tax.');
    }

    public function summary(Request $request)
    {
        [$from, $to] = FinanceReports::range($request->query('from'), $request->query('to'));
        $rows = Invoice::where('restaurant_id', $this->rid())->whereBetween('invoice_date', [$from, $to])
            ->selectRaw("COALESCE(tax_name,'No tax') as tax_name, tax_rate, tax_type, COUNT(*) as invoices, SUM(subtotal) as taxable, SUM(tax_amount) as tax, SUM(total) as total")
            ->groupBy('tax_name', 'tax_rate', 'tax_type')->orderBy('tax_name')->get();
        return view('finance.taxes.summary', compact('rows', 'from', 'to'));
    }
}
