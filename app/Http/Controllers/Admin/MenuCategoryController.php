<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('sort_order')
            ->get();
            
        return view('admin.menu-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.menu-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        MenuCategory::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.menu-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(MenuCategory $menuCategory)
    {
        if ($menuCategory->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        return view('admin.menu-categories.edit', compact('menuCategory'));
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        if ($menuCategory->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        $menuCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.menu-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        if ($menuCategory->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        $menuCategory->delete();
        return redirect()->route('admin.menu-categories.index')->with('success', 'Category deleted successfully.');
    }
}
