<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\Table;
use App\Models\TableCall;
use App\Services\Operations\OrderBuilder;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function table(Table $table): Table
    {
        abort_unless($table->restaurant_id === $this->rid(), 404);

        return $table;
    }

    private function order(Order $order): Order
    {
        abort_unless($order->restaurant_id === $this->rid(), 404);

        return $order;
    }

    private function menu()
    {
        return MenuCategory::where('restaurant_id', $this->rid())
            ->with(['menuItems' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')->get()->filter(fn ($c) => $c->menuItems->isNotEmpty());
    }

    public function index()
    {
        $tables = Table::where('restaurant_id', $this->rid())->orderBy('table_number')->get();
        $orders = Order::where('restaurant_id', $this->rid())
            ->where('order_type', 'dine_in')
            ->whereNotIn('status', ['served', 'cancelled'])
            ->with(['table', 'orderItems.menuItem'])->latest()->get();
        $calls = TableCall::where('restaurant_id', $this->rid())->whereIn('status', ['pending', 'accepted'])->with('table')->latest()->get();

        return view('operations.waiter.index', compact('tables', 'orders', 'calls'));
    }

    public function create(Table $table)
    {
        $this->table($table);

        return view('operations.waiter.order', ['table' => $table, 'order' => null, 'categories' => $this->menu(), 'qty' => []]);
    }

    public function store(Request $request, Table $table, OrderBuilder $builder)
    {
        $this->table($table);
        $data = $request->validate([
            'qty' => 'required|array',
            'qty.*' => 'nullable|integer|min:0|max:99',
            'notes' => 'nullable|string|max:500',
        ]);

        $lines = $builder->resolveLines(auth()->user()->restaurant, $data['qty']);
        $order = $builder->create(auth()->user()->restaurant, $lines, [
            'table_id' => $table->id, 'notes' => $data['notes'] ?? null,
            'order_type' => 'dine_in', 'placed_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('staff.waiter.index')->with('success', "Order {$order->order_number} placed for table {$table->table_number}.");
    }

    private function editable(Order $order): void
    {
        abort_if(in_array($order->status, ['served', 'cancelled']) || $order->payment_status === 'paid', 403, 'This order can no longer be edited.');
    }

    public function edit(Order $order)
    {
        $this->order($order);
        $this->editable($order);
        $order->load('orderItems');

        return view('operations.waiter.order', [
            'table' => $order->table, 'order' => $order, 'categories' => $this->menu(),
            'qty' => $order->orderItems->pluck('quantity', 'menu_item_id')->all(),
        ]);
    }

    public function update(Request $request, Order $order, OrderBuilder $builder)
    {
        $this->order($order);
        $this->editable($order);
        $data = $request->validate([
            'qty' => 'required|array',
            'qty.*' => 'nullable|integer|min:0|max:99',
            'notes' => 'nullable|string|max:500',
        ]);

        $lines = $builder->resolveLines(auth()->user()->restaurant, $data['qty']);
        $builder->replaceItems($order, $lines, $data['notes'] ?? null);

        return redirect()->route('staff.waiter.index')->with('success', "Order {$order->order_number} updated.");
    }

    public function serve(Order $order)
    {
        $this->order($order);
        abort_if($order->status === 'cancelled', 403);
        $order->update(['status' => 'served']);

        if ($order->table && !$order->table->orders()->where('id', '!=', $order->id)->whereNotIn('status', ['served', 'cancelled'])->exists()) {
            $order->table->scheduleAutoRelease();
        }

        return back()->with('success', "Order {$order->order_number} marked as served.");
    }

    public function answerCall(TableCall $call)
    {
        abort_unless($call->restaurant_id === $this->rid(), 404);
        $call->update(['status' => 'completed']);

        return back()->with('success', 'Table call answered.');
    }
}
