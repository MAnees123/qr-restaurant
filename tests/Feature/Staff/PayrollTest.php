<?php

namespace Tests\Feature\Staff;

use App\Models\Staff\AttendanceRecord;
use App\Models\Staff\AttendanceSetting;
use App\Models\Staff\PayrollRun;
use App\Models\Staff\Payslip;
use App\Models\Staff\SalaryProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    public function test_profile_generate_pay_and_payslip(): void
    {
        $admin = $this->adminWith(['payroll']);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        AttendanceSetting::forRestaurant($admin->restaurant_id)->update(['standard_hours' => 8]);

        $this->actingAs($admin)->get(route('admin.payroll.profiles'))->assertOk();
        $this->actingAs($admin)->post(route('admin.payroll.profiles.save'), [
            'user_id' => $staff->id, 'base_salary' => 2600, 'allowances' => 100, 'deductions' => 50, 'overtime_rate' => 20, 'working_days' => 26,
        ])->assertRedirect();
        $this->assertSame(1, SalaryProfile::where('user_id', $staff->id)->count());

        // 24 present days, one with 10h worked (2h overtime), 1 half day, 1 absent => paid 24.5 of 26
        foreach (range(1, 24) as $day) {
            AttendanceRecord::create([
                'restaurant_id' => $admin->restaurant_id, 'user_id' => $staff->id, 'work_date' => sprintf('2026-03-%02d', $day), 'status' => 'present',
                'clock_in' => $day === 1 ? '2026-03-01 08:00:00' : null, 'clock_out' => $day === 1 ? '2026-03-01 18:00:00' : null,
            ]);
        }
        AttendanceRecord::create(['restaurant_id' => $admin->restaurant_id, 'user_id' => $staff->id, 'work_date' => '2026-03-25', 'status' => 'half_day']);
        AttendanceRecord::create(['restaurant_id' => $admin->restaurant_id, 'user_id' => $staff->id, 'work_date' => '2026-03-26', 'status' => 'absent']);

        $this->actingAs($admin)->post(route('admin.payroll.generate'), ['month' => '2026-03'])->assertRedirect();
        $slip = Payslip::where('user_id', $staff->id)->firstOrFail();

        $this->assertEquals(24.5, $slip->days_present);
        $this->assertEquals(1.5, $slip->days_absent);
        $this->assertEquals(2, $slip->overtime_hours);
        $this->assertEquals(150.0, $slip->absence_deduction); // 2600/26*1.5
        $this->assertEquals(40.0, $slip->overtime_pay);
        $this->assertEquals(2600 + 100 + 40 - 50 - 150, $slip->net_pay);

        $this->actingAs($admin)->get(route('admin.payroll.show', $slip->run))->assertOk()->assertSee($staff->name);
        $this->actingAs($admin)->get(route('admin.payroll.payslip', $slip))->assertOk()->assertSee('2,540.00');

        $this->actingAs($admin)->post(route('admin.payroll.pay', $slip))->assertRedirect();
        $this->assertSame('paid', $slip->fresh()->status);
        $this->assertNotNull($slip->fresh()->paid_at);

        $this->actingAs($admin)->delete(route('admin.payroll.destroy', $slip->run))->assertSessionHasErrors('run');
        $this->assertDatabaseHas('payroll_runs', ['id' => $slip->payroll_run_id]);
    }

    public function test_duplicate_month_and_delete_unpaid_run(): void
    {
        $admin = $this->adminWith(['payroll']);
        $staff = $this->makeUser($admin->restaurant, 'staff');
        SalaryProfile::create(['restaurant_id' => $admin->restaurant_id, 'user_id' => $staff->id, 'base_salary' => 1000, 'working_days' => 26]);

        $this->actingAs($admin)->post(route('admin.payroll.generate'), ['month' => '2026-04'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.payroll.generate'), ['month' => '2026-04'])->assertSessionHasErrors('month');
        $this->assertSame(1, PayrollRun::count());
        // no attendance at all => fully absent
        $this->assertEquals(0, Payslip::first()->net_pay);

        $this->actingAs($admin)->get(route('admin.payroll.index'))->assertOk()->assertSee('2026-04');
        $this->actingAs($admin)->delete(route('admin.payroll.destroy', PayrollRun::first()))->assertRedirect();
        $this->assertSame(0, PayrollRun::count());
    }

    public function test_feature_not_granted_is_403(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.payroll.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.payroll.generate'), ['month' => '2026-04'])->assertForbidden();
        $this->assertDatabaseCount('payroll_runs', 0);
    }

    public function test_cross_tenant_isolation(): void
    {
        $a = $this->adminWith(['payroll']);
        $b = $this->adminWith(['payroll']);
        $staffB = $this->makeUser($b->restaurant, 'staff');
        SalaryProfile::create(['restaurant_id' => $b->restaurant_id, 'user_id' => $staffB->id, 'base_salary' => 500, 'working_days' => 26]);
        $run = PayrollRun::create(['restaurant_id' => $b->restaurant_id, 'month' => '2026-01']);
        $slip = Payslip::create(['restaurant_id' => $b->restaurant_id, 'payroll_run_id' => $run->id, 'user_id' => $staffB->id, 'net_pay' => 500]);

        $this->actingAs($a)->get(route('admin.payroll.show', $run))->assertNotFound();
        $this->actingAs($a)->get(route('admin.payroll.payslip', $slip))->assertNotFound();
        $this->actingAs($a)->post(route('admin.payroll.pay', $slip))->assertNotFound();
        $this->actingAs($a)->delete(route('admin.payroll.destroy', $run))->assertNotFound();
        $this->actingAs($a)->post(route('admin.payroll.profiles.save'), ['user_id' => $staffB->id, 'base_salary' => 1, 'working_days' => 26])->assertSessionHasErrors('user_id');
        $this->assertSame('pending', $slip->fresh()->status);
        $this->assertEquals(500, SalaryProfile::where('user_id', $staffB->id)->first()->base_salary);
        $this->actingAs($a)->get(route('admin.payroll.index'))->assertDontSee('2026-01');
        // generating in A does not touch B and fails without A profiles
        $this->actingAs($a)->post(route('admin.payroll.generate'), ['month' => '2026-02'])->assertSessionHasErrors('month');
    }

    public function test_validation(): void
    {
        $admin = $this->adminWith(['payroll']);
        $this->actingAs($admin)->post(route('admin.payroll.profiles.save'), [])->assertSessionHasErrors(['user_id', 'base_salary', 'working_days']);
        $this->actingAs($admin)->post(route('admin.payroll.generate'), ['month' => 'nope'])->assertSessionHasErrors('month');
        $staff = $this->makeUser($admin->restaurant, 'staff');
        $this->actingAs($admin)->post(route('admin.payroll.profiles.save'), ['user_id' => $staff->id, 'base_salary' => -5, 'working_days' => 40])->assertSessionHasErrors(['base_salary', 'working_days']);
    }
}
