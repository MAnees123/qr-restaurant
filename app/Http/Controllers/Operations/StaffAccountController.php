<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin screen to manage waiter / cashier staff users of the admin's own restaurant. */
class StaffAccountController extends Controller
{
    private function guardFeature(): int
    {
        $user = auth()->user();
        $restaurant = $user->restaurant;
        abort_unless($restaurant && ($restaurant->hasFeature('waiter_panel') || $restaurant->hasFeature('cashier_module')), 403, 'Feature not enabled on your plan.');

        return $restaurant->id;
    }

    private function allowedRoles(): array
    {
        $r = auth()->user()->restaurant;

        return array_values(array_filter([
            $r->hasFeature('waiter_panel') ? 'waiter' : null,
            $r->hasFeature('cashier_module') ? 'cashier' : null,
        ]));
    }

    private function find(User $user, int $restaurantId): User
    {
        abort_unless($user->restaurant_id === $restaurantId && $user->role === 'staff' && $user->staff_role, 404);

        return $user;
    }

    public function index()
    {
        $rid = $this->guardFeature();
        $staff = User::where('restaurant_id', $rid)->where('role', 'staff')->whereNotNull('staff_role')->orderBy('name')->paginate(15);

        return view('operations.staff.index', compact('staff'));
    }

    public function create()
    {
        $this->guardFeature();

        return view('operations.staff.form', ['member' => null, 'roles' => $this->allowedRoles()]);
    }

    public function store(Request $request)
    {
        $rid = $this->guardFeature();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'staff_role' => ['required', Rule::in($this->allowedRoles())],
        ]);

        User::create([
            'restaurant_id' => $rid, 'role' => 'staff', 'staff_role' => $data['staff_role'],
            'name' => $data['name'], 'email' => $data['email'], 'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('admin.staff-accounts.index')->with('success', 'Staff account created.');
    }

    public function edit(User $staff_account)
    {
        $rid = $this->guardFeature();

        return view('operations.staff.form', ['member' => $this->find($staff_account, $rid), 'roles' => $this->allowedRoles()]);
    }

    public function update(Request $request, User $staff_account)
    {
        $rid = $this->guardFeature();
        $member = $this->find($staff_account, $rid);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($member->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'staff_role' => ['required', Rule::in($this->allowedRoles())],
        ]);

        $member->fill(['name' => $data['name'], 'email' => $data['email'], 'staff_role' => $data['staff_role']]);
        if (!empty($data['password'])) {
            $member->password = bcrypt($data['password']);
        }
        $member->save();

        return redirect()->route('admin.staff-accounts.index')->with('success', 'Staff account updated.');
    }

    public function destroy(User $staff_account)
    {
        $rid = $this->guardFeature();
        $this->find($staff_account, $rid)->delete();

        return redirect()->route('admin.staff-accounts.index')->with('success', 'Staff account deleted.');
    }
}
