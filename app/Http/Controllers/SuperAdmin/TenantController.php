<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Plan;
use App\Models\User;
use App\Services\FeatureRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::withCount('users')->with('plan');

        if ($request->filled('status')) {
            if ($request->status === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($request->status === 'active') {
                $query->where('is_suspended', false)->where('is_active', true);
            }
        }

        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $restaurants = $query->latest()->paginate(15)->withQueryString();
        $plans = Plan::where('is_active', true)->get();

        return view('superadmin.tenants.index', compact('restaurants', 'plans'));
    }

    public function create()
    {
        $plans    = Plan::where('is_active', true)->get();
        $features = FeatureRegistry::all();
        return view('superadmin.tenants.create', compact('plans', 'features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'owner_name'    => 'required|string|max:200',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:30',
            'password'      => 'required|string|min:8|confirmed',
            'country'       => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',
            'timezone'      => 'nullable|string|max:50',
            'currency'      => 'nullable|string|max:10',
            'cuisine_type'  => 'nullable|string|max:100',
            'plan_id'       => 'nullable|exists:plans,id',
            'billing_cycle' => 'nullable|in:monthly,yearly,lifetime',
            'payment_status'=> 'nullable|in:paid,unpaid,trial',
            'subscription_ends_at' => 'nullable|date',
            'trial_days'    => 'nullable|integer|min:0',
            'max_branches'  => 'nullable|integer|min:1',
            'max_users'     => 'nullable|integer|min:1',
            'max_tables'    => 'nullable|integer|min:1',
            'max_storage_mb'=> 'nullable|integer|min:1',
            'theme'         => 'nullable|string|max:50',
            'logo'          => 'nullable|image|max:2048',
            'granted_features' => 'nullable|array',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('restaurant_logos', 'public');
        }

        // Create the restaurant record
        $trialEndsAt = null;
        if ($request->payment_status === 'trial' && $request->trial_days) {
            $trialEndsAt = now()->addDays((int) $request->trial_days);
        }

        $restaurant = Restaurant::create([
            'plan_id'              => $data['plan_id'] ?? null,
            'name'                 => $data['name'],
            'owner_name'           => $data['owner_name'],
            'phone'                => $data['phone'] ?? null,
            'country'              => $data['country'] ?? null,
            'city'                 => $data['city'] ?? null,
            'timezone'             => $data['timezone'] ?? 'UTC',
            'currency'             => $data['currency'] ?? 'USD',
            'cuisine_type'         => $data['cuisine_type'] ?? null,
            'billing_cycle'        => $data['billing_cycle'] ?? 'monthly',
            'payment_status'       => $data['payment_status'] ?? 'unpaid',
            'subscription_ends_at' => $data['subscription_ends_at'] ?? null,
            'trial_ends_at'        => $trialEndsAt,
            'max_branches'         => $data['max_branches'] ?? 1,
            'max_users'            => $data['max_users'] ?? 5,
            'max_tables'           => $data['max_tables'] ?? 20,
            'max_storage_mb'       => $data['max_storage_mb'] ?? 100,
            'theme'                => $data['theme'] ?? 'default',
            'logo'                 => $logoPath,
            'granted_features'     => $request->input('granted_features', []),
            'is_active'            => true,
            'is_suspended'         => false,
        ]);

        // Create the owner user
        User::create([
            'restaurant_id' => $restaurant->id,
            'name'          => $data['owner_name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => 'admin',
        ]);

        // Create initial billing invoice
        \App\Models\BillingInvoice::create([
            'restaurant_id'  => $restaurant->id,
            'plan_id'        => $restaurant->plan_id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'amount'         => $restaurant->plan ? ($restaurant->billing_cycle === 'yearly' ? $restaurant->plan->price_yearly : $restaurant->plan->price_monthly) : 0.00,
            'payment_status' => $restaurant->payment_status === 'paid' ? 'paid' : 'unpaid',
            'billing_cycle'  => $restaurant->billing_cycle,
            'paid_at'        => $restaurant->payment_status === 'paid' ? now() : null,
        ]);

        \App\Models\ActivityLog::log('tenant_created', "Tenant restaurant '{$restaurant->name}' created successfully.", $restaurant->id);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Restaurant '{$restaurant->name}' created successfully.");
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->loadCount(['users', 'tables', 'menuItems', 'orders']);
        $users    = $restaurant->users()->latest()->get();
        $features = FeatureRegistry::all();
        $plans    = Plan::where('is_active', true)->get();
        $invoices = \App\Models\BillingInvoice::where('restaurant_id', $restaurant->id)->latest()->get();
        $logs     = \App\Models\ActivityLog::where('restaurant_id', $restaurant->id)->with('user')->latest()->take(20)->get();
        return view('superadmin.tenants.show', compact('restaurant', 'users', 'features', 'plans', 'invoices', 'logs'));
    }

    public function edit(Restaurant $restaurant)
    {
        $plans    = Plan::where('is_active', true)->get();
        $features = FeatureRegistry::all();
        return view('superadmin.tenants.edit', compact('restaurant', 'plans', 'features'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:200',
            'owner_name'           => 'nullable|string|max:200',
            'phone'                => 'nullable|string|max:30',
            'country'              => 'nullable|string|max:100',
            'city'                 => 'nullable|string|max:100',
            'timezone'             => 'nullable|string|max:50',
            'currency'             => 'nullable|string|max:10',
            'plan_id'              => 'nullable|exists:plans,id',
            'billing_cycle'        => 'nullable|in:monthly,yearly,lifetime',
            'payment_status'       => 'nullable|in:paid,unpaid,trial',
            'subscription_ends_at' => 'nullable|date',
            'max_branches'         => 'nullable|integer|min:1',
            'max_users'            => 'nullable|integer|min:1',
            'max_tables'           => 'nullable|integer|min:1',
            'max_storage_mb'       => 'nullable|integer|min:1',
            'theme'                => 'nullable|string|max:50',
            'logo'                 => 'nullable|image|max:2048',
            'granted_features'     => 'nullable|array',
        ]);

        if ($request->hasFile('logo')) {
            if ($restaurant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($restaurant->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('restaurant_logos', 'public');
        }

        $data['granted_features'] = $request->input('granted_features', []);
        $data['is_active']        = $request->boolean('is_active', true);

        $restaurant->update($data);

        \App\Models\ActivityLog::log('tenant_updated', "Tenant restaurant settings updated.", $restaurant->id);

        return redirect()->route('superadmin.tenants.show', $restaurant)
            ->with('success', 'Tenant updated successfully.');
    }

    public function toggleSuspension(Restaurant $restaurant)
    {
        $restaurant->update(['is_suspended' => !$restaurant->is_suspended]);
        $status = $restaurant->is_suspended ? 'suspended' : 'activated';
        
        \App\Models\ActivityLog::log($restaurant->is_suspended ? 'tenant_suspended' : 'tenant_unsuspended', "Tenant has been {$status}.", $restaurant->id);

        return back()->with('success', "Restaurant has been {$status}.");
    }

    public function destroy(Restaurant $restaurant)
    {
        $name = $restaurant->name;
        \App\Models\ActivityLog::log('tenant_deleted', "Tenant restaurant '{$name}' permanently deleted.");
        $restaurant->delete();
        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Restaurant '{$name}' deleted.");
    }

    public function updateFeatures(Request $request, Restaurant $restaurant)
    {
        $restaurant->update([
            'granted_features' => $request->input('granted_features', []),
        ]);
        
        \App\Models\ActivityLog::log('tenant_features_updated', "Tenant features updated.", $restaurant->id);

        return back()->with('success', 'Feature access updated successfully.');
    }

    public function impersonate(Restaurant $restaurant)
    {
        $owner = $restaurant->users()->where('role', 'admin')->first();
        if (!$owner) {
            return back()->with('error', 'No admin user found for this restaurant.');
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($owner);

        \App\Models\ActivityLog::log('impersonated', "Super Admin impersonated owner '{$owner->email}'.", $restaurant->id);

        return redirect()->route('admin.dashboard')->with('success', "You are now logged in as owner of {$restaurant->name}.");
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonator_id')) {
            return redirect('/');
        }

        $superAdmin = User::findOrFail(session('impersonator_id'));
        auth()->login($superAdmin);
        session()->forget('impersonator_id');

        return redirect()->route('superadmin.dashboard')->with('success', 'Returned to Super Admin panel.');
    }

    public function resetPassword(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $owner = $restaurant->users()->where('role', 'admin')->first();
        if (!$owner) {
            return back()->with('error', 'No admin user found for this restaurant.');
        }

        $owner->update([
            'password' => Hash::make($request->password),
        ]);

        \App\Models\ActivityLog::log('password_reset', "Owner password has been manually reset by Super Admin.", $restaurant->id);

        return back()->with('success', 'Admin user password has been reset successfully.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        \App\Models\ActivityLog::log('user_password_reset', "Password for user '{$user->email}' ({$user->role}) has been reset by Super Admin.", $user->restaurant_id);

        return back()->with('success', "Password for user '{$user->name}' ({$user->email}) has been reset successfully.");
    }
}
