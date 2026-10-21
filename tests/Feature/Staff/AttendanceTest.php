<?php

namespace Tests\Feature\Staff;

use App\Models\Staff\AttendanceRecord;
use App\Models\Staff\AttendanceSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    public function test_staff_clock_in_out(): void
    {
        $admin = $this->adminWith(['attendance']);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        AttendanceSetting::forRestaurant($admin->restaurant_id)->update(['shift_start' => '09:00', 'late_grace_minutes' => 10]);

        $this->travelTo(now()->setTime(9, 5));
        $this->actingAs($staff)->get(route('staff.attendance.my'))->assertOk();
        $this->actingAs($staff)->post(route('staff.attendance.clock-in'))->assertRedirect();
        $rec = AttendanceRecord::where('user_id', $staff->id)->firstOrFail();
        $this->assertSame('present', $rec->status);

        $this->actingAs($staff)->post(route('staff.attendance.clock-in'))->assertSessionHasErrors('clock');

        $this->travelTo(now()->setTime(17, 35));
        $this->actingAs($staff)->post(route('staff.attendance.clock-out'))->assertRedirect();
        $this->assertSame(510, $rec->fresh()->workedMinutes());
        $this->actingAs($staff)->post(route('staff.attendance.clock-out'))->assertSessionHasErrors('clock');
    }

    public function test_late_marking(): void
    {
        $admin = $this->adminWith(['attendance']);
        $staff = $this->makeUser($admin->restaurant, 'kitchen');
        $this->travelTo(now()->setTime(10, 30));
        $this->actingAs($staff)->post(route('staff.attendance.clock-in'));
        $this->assertSame('late', AttendanceRecord::where('user_id', $staff->id)->first()->status);
    }

    public function test_clock_out_without_clock_in_fails(): void
    {
        $admin = $this->adminWith(['attendance']);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        $this->actingAs($staff)->post(route('staff.attendance.clock-out'))->assertSessionHasErrors('clock');
    }

    public function test_admin_sheet_correction_mark_absent_and_delete(): void
    {
        $admin = $this->adminWith(['attendance']);
        $s1 = $this->makeUser($admin->restaurant, 'staff');
        $s2 = $this->makeUser($admin->restaurant, 'staff');
        $d = '2026-05-10';

        $this->actingAs($admin)->get(route('admin.attendance.index', ['date' => $d]))->assertOk()->assertSee($s1->name);

        $this->actingAs($admin)->post(route('admin.attendance.store'), [
            'user_id' => $s1->id, 'work_date' => $d, 'status' => 'present', 'clock_in' => '09:00', 'clock_out' => '18:00',
        ])->assertRedirect();
        $rec = AttendanceRecord::where('user_id', $s1->id)->firstOrFail();
        $this->assertSame(540, $rec->workedMinutes());

        // correction updates the same row
        $this->actingAs($admin)->post(route('admin.attendance.store'), [
            'user_id' => $s1->id, 'work_date' => $d, 'status' => 'late', 'clock_in' => '10:00', 'clock_out' => '18:00',
        ]);
        $this->assertSame(1, AttendanceRecord::where('user_id', $s1->id)->count());
        $this->assertSame('late', $rec->fresh()->status);

        $this->actingAs($admin)->post(route('admin.attendance.mark-absent'), ['date' => $d])->assertRedirect();
        $this->assertSame('absent', AttendanceRecord::where('user_id', $s2->id)->first()->status);
        $this->assertSame('late', $rec->fresh()->status);

        $this->actingAs($admin)->delete(route('admin.attendance.destroy', $rec))->assertRedirect();
        $this->assertDatabaseMissing('attendance_records', ['id' => $rec->id]);

        $this->actingAs($admin)->put(route('admin.attendance.settings'), ['shift_start' => '08:30', 'late_grace_minutes' => 5, 'standard_hours' => 7.5])->assertRedirect();
        $this->assertSame('08:30', AttendanceSetting::where('restaurant_id', $admin->restaurant_id)->first()->shift_start);
    }

    public function test_feature_not_granted_is_403(): void
    {
        $admin = $this->adminWith([]);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        $this->actingAs($admin)->get(route('admin.attendance.index'))->assertForbidden();
        $this->actingAs($staff)->post(route('staff.attendance.clock-in'))->assertForbidden();
        $this->assertDatabaseCount('attendance_records', 0);
    }

    public function test_cross_tenant_isolation(): void
    {
        $a = $this->adminWith(['attendance']);
        $b = $this->adminWith(['attendance']);
        $staffB = $this->makeUser($b->restaurant, 'staff');
        $recB = AttendanceRecord::create(['restaurant_id' => $b->restaurant_id, 'user_id' => $staffB->id, 'work_date' => '2026-05-10', 'status' => 'present']);

        $this->actingAs($a)->get(route('admin.attendance.index', ['date' => '2026-05-10']))->assertDontSee($staffB->name);
        $this->actingAs($a)->post(route('admin.attendance.store'), ['user_id' => $staffB->id, 'work_date' => '2026-05-10', 'status' => 'absent'])
            ->assertSessionHasErrors('user_id');
        $this->actingAs($a)->delete(route('admin.attendance.destroy', $recB))->assertNotFound();
        $this->actingAs($a)->post(route('admin.attendance.mark-absent'), ['date' => '2026-05-11']);
        $this->assertSame('present', $recB->fresh()->status);
        $this->assertSame(0, AttendanceRecord::where('user_id', $staffB->id)->where('status', 'absent')->count());
    }

    public function test_validation(): void
    {
        $admin = $this->adminWith(['attendance']);
        $s = $this->makeUser($admin->restaurant, 'staff');
        $this->actingAs($admin)->post(route('admin.attendance.store'), [])->assertSessionHasErrors(['user_id', 'work_date', 'status']);
        $this->actingAs($admin)->post(route('admin.attendance.store'), ['user_id' => $s->id, 'work_date' => '2026-05-10', 'status' => 'x'])->assertSessionHasErrors('status');
        $this->actingAs($admin)->post(route('admin.attendance.store'), ['user_id' => $s->id, 'work_date' => '2026-05-10', 'status' => 'present', 'clock_in' => '10:00', 'clock_out' => '09:00'])->assertSessionHasErrors('clock_out');
        $this->actingAs($admin)->put(route('admin.attendance.settings'), ['shift_start' => 'bad'])->assertSessionHasErrors(['shift_start', 'late_grace_minutes', 'standard_hours']);
    }
}
