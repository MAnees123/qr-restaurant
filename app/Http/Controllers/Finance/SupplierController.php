<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use Concerns;

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'payment_terms' => 'nullable|string|max:150',
            'opening_balance' => 'nullable|numeric|min:0|max:9999999999',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function index(Request $request)
    {
        $q = Supplier::where('restaurant_id', $this->rid())->orderBy('name');
        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('contact_person', 'like', "%$s%"));
        }
        return view('finance.suppliers.index', ['suppliers' => $q->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('finance.suppliers.form', ['supplier' => new Supplier(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['restaurant_id'] = $this->rid();
        Supplier::create($data);
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier added.');
    }

    public function edit(Supplier $supplier)
    {
        return view('finance.suppliers.form', ['supplier' => $this->own($supplier)]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->own($supplier);
        $data = $request->validate($this->rules());
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $supplier->update($data);
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->own($supplier)->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted.');
    }
}
