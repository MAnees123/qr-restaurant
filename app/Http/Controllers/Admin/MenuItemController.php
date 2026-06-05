<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::where('restaurant_id', auth()->user()->restaurant_id)
            ->with('category')
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->get();
            
        return view('admin.menu-items.index', compact('items'));
    }

    public function create()
    {
        $categories = MenuCategory::where('restaurant_id', auth()->user()->restaurant_id)->get();
        return view('admin.menu-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
            'preparation_time' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_items', 'public');
        }

        MenuItem::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'menu_category_id' => $request->menu_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'sort_order' => $request->sort_order,
            'preparation_time' => $request->preparation_time,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menuItem)
    {
        if ($menuItem->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        $categories = MenuCategory::where('restaurant_id', auth()->user()->restaurant_id)->get();
        return view('admin.menu-items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
            'preparation_time' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $menuItem->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('menu_items', 'public');
        }

        $menuItem->update([
            'menu_category_id' => $request->menu_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'sort_order' => $request->sort_order,
            'preparation_time' => $request->preparation_time,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();
        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item deleted successfully.');
    }
}
