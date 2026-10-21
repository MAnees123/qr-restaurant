<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function own(Customer $customer): Customer
    {
        abort_unless($customer->restaurant_id === $this->rid(), 404);

        return $customer;
    }

    private function rules(?Customer $c = null): array
    {
        return [
            'name' => 'required|string|max:150',
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('customers', 'phone')->where('restaurant_id', $this->rid())->ignore($c?->id)],
            'email' => 'nullable|email|max:150',
            'notes' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:255',
        ];
    }

    private function clean(array $data): array
    {
        if (isset($data['tags'])) {
            $data['tags'] = collect(explode(',', $data['tags']))->map(fn ($t) => trim($t))->filter()->unique()->implode(',') ?: null;
        }

        return $data;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $tag = trim((string) $request->query('tag'));

        $customers = Customer::where('restaurant_id', $this->rid())
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->orWhere('email', 'like', "%$q%")))
            ->when($tag !== '', fn ($query) => $query->where('tags', 'like', "%$tag%"))
            ->withCount('orders')
            ->withSum(['orders as spent' => fn ($o) => $o->where('status', '!=', 'cancelled')], 'total_amount')
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('staff.customers.index', compact('customers', 'q', 'tag'));
    }

    public function create()
    {
        return view('staff.customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request)
    {
        $data = $this->clean($request->validate($this->rules()));
        $customer = Customer::create($data + ['restaurant_id' => $this->rid()]);

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer added.');
    }

    public function show(Customer $customer)
    {
        $this->own($customer);
        $orders = $customer->orders()->latest()->paginate(15);
        $spent = $customer->orders()->where('status', '!=', 'cancelled')->sum('total_amount');

        return view('staff.customers.show', compact('customer', 'orders', 'spent'));
    }

    public function edit(Customer $customer)
    {
        return view('staff.customers.form', ['customer' => $this->own($customer)]);
    }

    public function update(Request $request, Customer $customer)
    {
        $this->own($customer);
        $customer->update($this->clean($request->validate($this->rules($customer))));

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $this->own($customer)->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }

    public function linkOrder(Request $request, Customer $customer)
    {
        $this->own($customer);
        $data = $request->validate(['order_number' => 'required|string|max:100']);

        $order = Order::where('restaurant_id', $this->rid())->where('order_number', $data['order_number'])->first();
        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found.'])->withInput();
        }

        $order->customer_id = $customer->id;
        $order->save();

        return back()->with('success', 'Order ' . $order->order_number . ' linked.');
    }
}
