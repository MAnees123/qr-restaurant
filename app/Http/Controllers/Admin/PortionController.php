<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portion;
use App\Models\Branch;
use Illuminate\Http\Request;

class PortionController extends Controller
{
    public function index()
    {
        $portions = Portion::where('restaurant_id', auth()->user()->restaurant_id)->with('branch')->get();
        $branches = Branch::where('restaurant_id', auth()->user()->restaurant_id)->where('is_active', true)->get();
        return view('admin.portions.index', compact('portions', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);
        
        $exists = Portion::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('branch_id', $request->branch_id)
            ->where('name', $request->name)->exists();
            
        if ($exists) {
            return back()->withErrors(['name' => 'Portion name already exists in this branch.']);
        }

        Portion::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Portion created successfully.');
    }

    public function update(Request $request, Portion $portion)
    {
        if ($portion->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);
        
        $exists = Portion::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('branch_id', $request->branch_id)
            ->where('name', $request->name)->where('id', '!=', $portion->id)->exists();
            
        if ($exists) {
            return back()->withErrors(['name' => 'Portion name already exists in this branch.']);
        }

        $portion->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Portion updated successfully.');
    }

    public function destroy(Portion $portion)
    {
        if ($portion->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        $portion->delete();
        return back()->with('success', 'Portion deleted successfully.');
    }
}
