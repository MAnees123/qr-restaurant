<?php

use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\CustomerController;
use App\Http\Controllers\Staff\PayrollController;
use App\Http\Controllers\Staff\StaffRoleController;
use Illuminate\Support\Facades\Route;

// role_permission
Route::middleware(['auth', 'role:admin', 'feature:role_permission'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('staff-roles', StaffRoleController::class)->except('show');
    Route::post('staff-roles/assign/{user}', [StaffRoleController::class, 'assign'])->name('staff-roles.assign');
});

// attendance (admin)
Route::middleware(['auth', 'role:admin', 'feature:attendance'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('attendance/mark-absent', [AttendanceController::class, 'markAbsent'])->name('attendance.mark-absent');
    Route::put('attendance/settings', [AttendanceController::class, 'settings'])->name('attendance.settings');
    Route::delete('attendance/{record}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
});

// attendance (staff self-service clock in/out)
Route::middleware(['auth', 'role:admin,staff,kitchen', 'feature:attendance'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('attendance', [AttendanceController::class, 'my'])->name('attendance.my');
    Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
});

// payroll
Route::middleware(['auth', 'role:admin', 'feature:payroll'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/profiles', [PayrollController::class, 'profiles'])->name('payroll.profiles');
    Route::post('payroll/profiles', [PayrollController::class, 'saveProfile'])->name('payroll.profiles.save');
    Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('payroll/runs/{run}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::delete('payroll/runs/{run}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    Route::get('payroll/payslips/{payslip}', [PayrollController::class, 'payslip'])->name('payroll.payslip');
    Route::post('payroll/payslips/{payslip}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
});

// customer_mgmt
Route::middleware(['auth', 'role:admin', 'feature:customer_mgmt'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/link-order', [CustomerController::class, 'linkOrder'])->name('customers.link-order');
});
