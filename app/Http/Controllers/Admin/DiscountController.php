<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Discount;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::where('restaurant_id', auth()->user()->restaurant_id)->latest()->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['restaurant_id'] = auth()->user()->restaurant_id;

        Discount::create($data);

        return redirect()->route('admin.discounts.index')->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        if ($discount->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        if ($discount->restaurant_id !== auth()->user()->restaurant_id) abort(403);

        $request->validate([
            'code' => 'required|unique:discounts,code,' . $discount->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $discount->update($request->all());

        return redirect()->route('admin.discounts.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        if ($discount->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted.');
    }
}
