<?php

namespace Tests\Feature\Operations;

use App\Models\User;

class StaffAccountsTest extends OperationsTestCase
{
    public function test_admin_can_create_update_and_delete_staff(): void
    {
        $admin = $this->adminWith(['waiter_panel', 'cashier_module']);

        $this->actingAs($admin)->get(route('admin.staff-accounts.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.staff-accounts.store'), [
            'name' => 'Wally', 'email' => 'wally@test.local', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'staff_role' => 'waiter',
        ])->assertRedirect(route('admin.staff-accounts.index'));

        $u = User::where('email', 'wally@test.local')->firstOrFail();
        $this->assertSame('staff', $u->role);
        $this->assertSame('waiter', $u->staff_role);
        $this->assertSame($admin->restaurant_id, $u->restaurant_id);

        $this->actingAs($admin)->put(route('admin.staff-accounts.update', $u), [
            'name' => 'Wally 2', 'email' => 'wally@test.local', 'staff_role' => 'cashier',
        ])->assertRedirect();
        $this->assertSame('cashier', $u->fresh()->staff_role);
        $this->assertSame('Wally 2', $u->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.staff-accounts.destroy', $u))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $u->id]);
    }

    public function test_forbidden_without_either_staff_feature(): void
    {
        $admin = $this->adminWith(['loyalty']);
        $this->actingAs($admin)->get(route('admin.staff-accounts.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.staff-accounts.store'), [])->assertForbidden();
    }

    public function test_cannot_assign_role_whose_feature_is_missing(): void
    {
        $admin = $this->adminWith(['waiter_panel']);
        $this->actingAs($admin)->post(route('admin.staff-accounts.store'), [
            'name' => 'C', 'email' => 'c@test.local', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'staff_role' => 'cashier',
        ])->assertSessionHasErrors('staff_role');
        $this->assertDatabaseMissing('users', ['email' => 'c@test.local']);
    }

    public function test_validation_failures(): void
    {
        $admin = $this->adminWith(['waiter_panel']);
        $this->makeUser($admin->restaurant, 'admin', ['email' => 'dup@test.local']);
        $this->actingAs($admin)->post(route('admin.staff-accounts.store'), [
            'name' => '', 'email' => 'dup@test.local', 'password' => 'short', 'staff_role' => 'waiter',
        ])->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_other_restaurant_cannot_touch_staff(): void
    {
        $a = $this->adminWith(['waiter_panel']);
        $b = $this->adminWith(['waiter_panel']);
        $staff = $this->staff($a->restaurant, 'waiter');

        $this->actingAs($b)->get(route('admin.staff-accounts.edit', $staff))->assertNotFound();
        $this->actingAs($b)->put(route('admin.staff-accounts.update', $staff), ['name' => 'x', 'email' => 'x@test.local', 'staff_role' => 'waiter'])->assertNotFound();
        $this->actingAs($b)->delete(route('admin.staff-accounts.destroy', $staff))->assertNotFound();
        $this->assertDatabaseHas('users', ['id' => $staff->id]);
        $this->actingAs($b)->get(route('admin.staff-accounts.index'))->assertDontSee($staff->email);
    }

    public function test_staff_login_lands_on_their_panel(): void
    {
        $r = $this->makeRestaurant(['waiter_panel', 'cashier_module']);
        $w = $this->staff($r, 'waiter');
        $c = $this->staff($r, 'cashier');

        $this->post('/login', ['email' => $w->email, 'password' => 'password'])->assertRedirect(route('staff.waiter.index'));
        auth()->logout();
        $this->post('/login', ['email' => $c->email, 'password' => 'password'])->assertRedirect(route('staff.cashier.index'));
    }
}
