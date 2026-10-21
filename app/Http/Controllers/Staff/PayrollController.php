<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\AttendanceRecord;
use App\Models\Staff\AttendanceSetting;
use App\Models\Staff\PayrollRun;
use App\Models\Staff\Payslip;
use App\Models\Staff\SalaryProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    public function index()
    {
        $runs = PayrollRun::where('restaurant_id', $this->rid())->withCount('payslips')
            ->withSum('payslips', 'net_pay')->orderByDesc('month')->get();

        return view('staff.payroll.index', compact('runs'));
    }

    public function profiles()
    {
        $staff = User::where('restaurant_id', $this->rid())->where('role', '!=', 'customer')->orderBy('name')->get();
        $profiles = SalaryProfile::where('restaurant_id', $this->rid())->get()->keyBy('user_id');

        return view('staff.payroll.profiles', compact('staff', 'profiles'));
    }

    public function saveProfile(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('restaurant_id', $this->rid())],
            'base_salary' => 'required|numeric|min:0|max:99999999',
            'allowances' => 'nullable|numeric|min:0|max:99999999',
            'deductions' => 'nullable|numeric|min:0|max:99999999',
            'overtime_rate' => 'nullable|numeric|min:0|max:999999',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        SalaryProfile::updateOrCreate(
            ['user_id' => $data['user_id']],
            [
                'restaurant_id' => $this->rid(),
                'base_salary' => $data['base_salary'],
                'allowances' => $data['allowances'] ?? 0,
                'deductions' => $data['deductions'] ?? 0,
                'overtime_rate' => $data['overtime_rate'] ?? 0,
                'working_days' => $data['working_days'],
            ]
        );

        return redirect()->route('admin.payroll.profiles')->with('success', 'Salary profile saved.');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'month' => ['required', 'date_format:Y-m', Rule::unique('payroll_runs', 'month')->where('restaurant_id', $this->rid())],
        ], ['month.unique' => 'Payroll for this month has already been generated.']);

        $profiles = SalaryProfile::where('restaurant_id', $this->rid())->get();
        if ($profiles->isEmpty()) {
            return back()->withErrors(['month' => 'Create at least one salary profile first.'])->withInput();
        }

        $start = Carbon::createFromFormat('Y-m-d', $data['month'] . '-01')->startOfDay();
        $end = $start->copy()->endOfMonth();
        $standard = (float) AttendanceSetting::forRestaurant($this->rid())->standard_hours;

        $run = PayrollRun::create(['restaurant_id' => $this->rid(), 'month' => $data['month'], 'generated_by' => auth()->id()]);

        foreach ($profiles as $p) {
            $rows = AttendanceRecord::where('restaurant_id', $this->rid())->where('user_id', $p->user_id)
                ->whereBetween('work_date', [$start, $end])->get();

            // present, late and approved leave count as paid days; half days count 0.5
            $paidDays = $rows->whereIn('status', ['present', 'late', 'leave'])->count() + 0.5 * $rows->where('status', 'half_day')->count();
            $absent = max(0, $p->working_days - $paidDays);
            $overtime = round($rows->sum(fn ($r) => max(0, $r->workedMinutes() / 60 - $standard)), 2);

            $base = (float) $p->base_salary;
            $absenceDeduction = round($base / $p->working_days * $absent, 2);
            $overtimePay = round($overtime * (float) $p->overtime_rate, 2);
            $net = round($base + $p->allowances + $overtimePay - $p->deductions - $absenceDeduction, 2);

            Payslip::create([
                'restaurant_id' => $this->rid(), 'payroll_run_id' => $run->id, 'user_id' => $p->user_id,
                'days_present' => $paidDays, 'days_absent' => $absent, 'overtime_hours' => $overtime,
                'base_salary' => $base, 'allowances' => $p->allowances, 'deductions' => $p->deductions,
                'overtime_pay' => $overtimePay, 'absence_deduction' => $absenceDeduction, 'net_pay' => $net,
            ]);
        }

        return redirect()->route('admin.payroll.show', $run)->with('success', 'Payroll generated for ' . $data['month'] . '.');
    }

    public function show(PayrollRun $run)
    {
        abort_unless($run->restaurant_id === $this->rid(), 404);
        $payslips = $run->payslips()->with('user')->get();

        return view('staff.payroll.show', compact('run', 'payslips'));
    }

    public function payslip(Payslip $payslip)
    {
        abort_unless($payslip->restaurant_id === $this->rid(), 404);
        $payslip->load('user', 'run');

        return view('staff.payroll.payslip', ['payslip' => $payslip, 'restaurant' => auth()->user()->restaurant]);
    }

    public function pay(Payslip $payslip)
    {
        abort_unless($payslip->restaurant_id === $this->rid(), 404);
        if ($payslip->status !== 'paid') {
            $payslip->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Payslip marked as paid.');
    }

    public function destroy(PayrollRun $run)
    {
        abort_unless($run->restaurant_id === $this->rid(), 404);
        if ($run->payslips()->where('status', 'paid')->exists()) {
            return back()->withErrors(['run' => 'Cannot delete a run that contains paid payslips.']);
        }
        $run->delete();

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll run deleted.');
    }
}
