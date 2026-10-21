<?php

namespace Tests\Feature\Staff;

use App\Http\Middleware\CheckPermission;
use App\Models\Staff\StaffRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    public function test_admin_can_create_update_delete_role(): void
    {
        $admin = $this->adminWith(['role_permission']);

        $this->actingAs($admin)->get(route('admin.staff-roles.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.staff-roles.store'), ['name' => 'Floor Lead', 'permissions' => ['view_orders', 'manage_menu']])
            ->assertRedirect(route('admin.staff-roles.index'));

        $role = StaffRole::where('restaurant_id', $admin->restaurant_id)->firstOrFail();
        $this->assertSame(['view_orders', 'manage_menu'], $role->permissions);

        $this->actingAs($admin)->put(route('admin.staff-roles.update', $role), ['name' => 'Lead', 'permissions' => ['view_orders']])->assertRedirect();
        $this->assertSame('Lead', $role->fresh()->name);
        $this->assertSame(['view_orders'], $role->fresh()->permissions);

        $this->actingAs($admin)->delete(route('admin.staff-roles.destroy', $role))->assertRedirect();
        $this->assertDatabaseMissing('staff_roles', ['id' => $role->id]);
    }

    public function test_assign_role_and_has_permission(): void
    {
        $admin = $this->adminWith(['role_permission']);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        $role = StaffRole::create(['restaurant_id' => $admin->restaurant_id, 'name' => 'R', 'permissions' => ['manage_menu']]);

        $this->assertFalse($staff->hasPermission('manage_menu'));
        $this->actingAs($admin)->post(route('admin.staff-roles.assign', $staff), ['custom_role_id' => $role->id])->assertRedirect();

        $staff = $staff->fresh();
        $this->assertSame($role->id, $staff->custom_role_id);
        $this->assertTrue($staff->hasPermission('manage_menu'));
        $this->assertFalse($staff->hasPermission('manage_payroll'));
        $this->assertTrue($admin->hasPermission('manage_payroll'));

        $this->actingAs($admin)->post(route('admin.staff-roles.assign', $staff), ['custom_role_id' => ''])->assertRedirect();
        $this->assertNull($staff->fresh()->custom_role_id);
    }

    public function test_permission_middleware(): void
    {
        $r = $this->makeRestaurant();
        $role = StaffRole::create(['restaurant_id' => $r->id, 'name' => 'R', 'permissions' => ['manage_menu']]);
        $allowed = $this->makeUser($r, 'staff', ['custom_role_id' => $role->id]);
        $denied = $this->makeUser($r, 'staff');

        Route::middleware(['web', 'auth', CheckPermission::class . ':manage_menu'])->get('/_perm-test', fn () => 'ok');

        $this->actingAs($allowed)->get('/_perm-test')->assertOk();
        $this->actingAs($denied)->get('/_perm-test')->assertForbidden();
    }

    public function test_feature_not_granted_is_403(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.staff-roles.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.staff-roles.store'), ['name' => 'X'])->assertForbidden();
        $this->assertDatabaseCount('staff_roles', 0);
    }

    public function test_cross_tenant_isolation(): void
    {
        $a = $this->adminWith(['role_permission']);
        $b = $this->adminWith(['role_permission']);
        $roleB = StaffRole::create(['restaurant_id' => $b->restaurant_id, 'name' => 'B', 'permissions' => []]);
        $staffB = $this->makeUser($b->restaurant, 'staff');
        $roleA = StaffRole::create(['restaurant_id' => $a->restaurant_id, 'name' => 'A', 'permissions' => []]);

        $this->actingAs($a)->get(route('admin.staff-roles.edit', $roleB))->assertNotFound();
        $this->actingAs($a)->put(route('admin.staff-roles.update', $roleB), ['name' => 'Hack'])->assertNotFound();
        $this->actingAs($a)->delete(route('admin.staff-roles.destroy', $roleB))->assertNotFound();
        $this->actingAs($a)->post(route('admin.staff-roles.assign', $staffB), ['custom_role_id' => $roleA->id])->assertNotFound();
        $this->actingAs($a)->post(route('admin.staff-roles.assign', $this->makeUser($a->restaurant, 'staff')), ['custom_role_id' => $roleB->id])
            ->assertSessionHasErrors('custom_role_id');
        $this->assertSame('B', $roleB->fresh()->name);
        $this->actingAs($a)->get(route('admin.staff-roles.index'))->assertDontSee('Hack');
    }

    public function test_validation(): void
    {
        $admin = $this->adminWith(['role_permission']);
        $this->actingAs($admin)->post(route('admin.staff-roles.store'), [])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post(route('admin.staff-roles.store'), ['name' => 'X', 'permissions' => ['bogus']])->assertSessionHasErrors('permissions.0');
        StaffRole::create(['restaurant_id' => $admin->restaurant_id, 'name' => 'Dup', 'permissions' => []]);
        $this->actingAs($admin)->post(route('admin.staff-roles.store'), ['name' => 'Dup'])->assertSessionHasErrors('name');
    }
}
