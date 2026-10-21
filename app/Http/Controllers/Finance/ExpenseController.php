<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    use Concerns;

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|gt:0|max:9999999999',
            'expense_category_id' => 'nullable|integer',
            'supplier_id' => 'nullable|integer',
            'reference' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
        ]);
        $data['expense_category_id'] = $data['expense_category_id'] ?? null;
        $data['supplier_id'] = $data['supplier_id'] ?? null;
        if ($data['expense_category_id'] && !ExpenseCategory::where('restaurant_id', $this->rid())->whereKey($data['expense_category_id'])->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['expense_category_id' => 'Invalid category.']);
        }
        if ($data['supplier_id'] && !Supplier::where('restaurant_id', $this->rid())->whereKey($data['supplier_id'])->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['supplier_id' => 'Invalid supplier.']);
        }
        return $data;
    }

    private function lookups(): array
    {
        return [
            'categories' => ExpenseCategory::where('restaurant_id', $this->rid())->orderBy('name')->get(),
            'suppliers' => Supplier::where('restaurant_id', $this->rid())->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $month = $request->query('month') && preg_match('/^\d{4}-\d{2}$/', $request->query('month')) ? $request->query('month') : now()->format('Y-m');
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        $base = Expense::where('expenses.restaurant_id', $this->rid())->whereBetween('expenses.expense_date', [$start, $end]);
        $summary = (clone $base)->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->selectRaw("COALESCE(expense_categories.name,'Uncategorised') as category, SUM(expenses.amount) as total")
            ->groupBy('expense_categories.id', 'expense_categories.name')->orderByDesc('total')->get();
        $expenses = (clone $base)->with('category', 'supplier')->latest('expenses.expense_date')->latest('expenses.id')->paginate(15)->withQueryString();
        return view('finance.expenses.index', compact('expenses', 'summary', 'month') + ['monthTotal' => (float) $summary->sum('total')]);
    }

    public function create()
    {
        return view('finance.expenses.form', ['expense' => new Expense(['expense_date' => now()])] + $this->lookups());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['restaurant_id'] = $this->rid();
        Expense::create($data);
        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded.');
    }

    public function edit(Expense $expense)
    {
        return view('finance.expenses.form', ['expense' => $this->own($expense)] + $this->lookups());
    }

    public function update(Request $request, Expense $expense)
    {
        $this->own($expense)->update($this->validated($request));
        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $this->own($expense)->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        if (ExpenseCategory::where('restaurant_id', $this->rid())->where('name', $data['name'])->exists()) {
            return back()->withErrors(['name' => 'This category already exists.']);
        }
        ExpenseCategory::create(['restaurant_id' => $this->rid(), 'name' => $data['name']]);
        return back()->with('success', 'Category added.');
    }

    public function destroyCategory(ExpenseCategory $category)
    {
        $this->own($category)->delete();
        return back()->with('success', 'Category deleted.');
    }
}
