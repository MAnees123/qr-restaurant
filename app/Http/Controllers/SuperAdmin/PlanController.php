<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\FeatureRegistry;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('restaurants')->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        $features = FeatureRegistry::all();
        return view('superadmin.plans.create', compact('features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly'  => 'required|numeric|min:0',
            'trial_days'    => 'required|integer|min:0',
            'max_branches'  => 'required|integer|min:-1',
            'max_users'     => 'required|integer|min:-1',
            'max_tables'    => 'required|integer|min:-1',
            'features'      => 'nullable|array',
            'is_active'     => 'nullable|boolean',
        ]);

        $data['features'] = $request->input('features', []);
        $data['is_active'] = $request->boolean('is_active', true);

        Plan::create($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $features = FeatureRegistry::all();
        return view('superadmin.plans.edit', compact('plan', 'features'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly'  => 'required|numeric|min:0',
            'trial_days'    => 'required|integer|min:0',
            'max_branches'  => 'required|integer|min:-1',
            'max_users'     => 'required|integer|min:-1',
            'max_tables'    => 'required|integer|min:-1',
            'features'      => 'nullable|array',
        ]);

        $data['features']  = $request->input('features', []);
        $data['is_active'] = $request->boolean('is_active', true);

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->restaurants()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a plan that has active restaurants.']);
        }
        $plan->delete();
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted.');
    }
}
