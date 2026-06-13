<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('restaurant_id', auth()->user()->restaurant_id)->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $exists = Branch::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('name', $request->name)->exists();
            
        if ($exists) {
            return back()->withErrors(['name' => 'Branch name already exists.']);
        }

        Branch::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        
        $request->validate(['name' => 'required|string|max:255']);
        
        $exists = Branch::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('name', $request->name)->where('id', '!=', $branch->id)->exists();
            
        if ($exists) {
            return back()->withErrors(['name' => 'Branch name already exists.']);
        }

        $branch->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        $branch->delete();
        return back()->with('success', 'Branch deleted successfully.');
    }
}
