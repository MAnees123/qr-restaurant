<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\StaffRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffRoleController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function own(StaffRole $role): StaffRole
    {
        abort_unless($role->restaurant_id === $this->rid(), 404);

        return $role;
    }

    private function rules(?StaffRole $role = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('staff_roles', 'name')
                ->where('restaurant_id', $this->rid())->ignore($role?->id)],
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(array_keys(StaffRole::PERMISSIONS))],
        ];
    }

    public function index()
    {
        $roles = StaffRole::where('restaurant_id', $this->rid())->withCount('users')->orderBy('name')->get();
        $staff = User::where('restaurant_id', $this->rid())->where('role', '!=', 'customer')->orderBy('name')->get();

        return view('staff.roles.index', compact('roles', 'staff'));
    }

    public function create()
    {
        return view('staff.roles.form', ['role' => new StaffRole(), 'permissions' => StaffRole::PERMISSIONS]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        StaffRole::create([
            'restaurant_id' => $this->rid(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values($data['permissions'] ?? []),
        ]);

        return redirect()->route('admin.staff-roles.index')->with('success', 'Role created.');
    }

    public function edit(StaffRole $staff_role)
    {
        return view('staff.roles.form', ['role' => $this->own($staff_role), 'permissions' => StaffRole::PERMISSIONS]);
    }

    public function update(Request $request, StaffRole $staff_role)
    {
        $this->own($staff_role);
        $data = $request->validate($this->rules($staff_role));
        $staff_role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values($data['permissions'] ?? []),
        ]);

        return redirect()->route('admin.staff-roles.index')->with('success', 'Role updated.');
    }

    public function destroy(StaffRole $staff_role)
    {
        $this->own($staff_role)->delete();

        return redirect()->route('admin.staff-roles.index')->with('success', 'Role deleted.');
    }

    public function assign(Request $request, User $user)
    {
        abort_unless($user->restaurant_id === $this->rid() && $user->role !== 'customer', 404);

        $data = $request->validate([
            'custom_role_id' => ['nullable', Rule::exists('staff_roles', 'id')->where('restaurant_id', $this->rid())],
        ]);

        $user->custom_role_id = $data['custom_role_id'] ?? null;
        $user->save();

        return back()->with('success', 'Role assigned to ' . $user->name . '.');
    }
}
