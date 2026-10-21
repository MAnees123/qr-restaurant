<?php

use App\Http\Controllers\Operations\AdminCashierController;
use App\Http\Controllers\Operations\CashierController;
use App\Http\Controllers\Operations\DeliveryController;
use App\Http\Controllers\Operations\LoyaltyController;
use App\Http\Controllers\Operations\OnlineOrderAdminController;
use App\Http\Controllers\Operations\OnlineOrderController;
use App\Http\Controllers\Operations\StaffAccountController;
use App\Http\Controllers\Operations\WaiterController;
use App\Http\Middleware\OperationsStaffRole;
use Illuminate\Support\Facades\Route;

// ── Admin: staff accounts (waiter / cashier users). Allowed when either staff feature is granted (checked in controller).
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('staff-accounts', StaffAccountController::class)->except(['show']);
});

// ── Waiter panel (staff users with staff_role = waiter)
Route::middleware(['auth', 'role:staff', 'feature:waiter_panel', OperationsStaffRole::class . ':waiter'])
    ->prefix('staff/waiter')->name('staff.waiter.')->group(function () {
        Route::get('/', [WaiterController::class, 'index'])->name('index');
        Route::get('/tables/{table}/order', [WaiterController::class, 'create'])->name('orders.create');
        Route::post('/tables/{table}/order', [WaiterController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/edit', [WaiterController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [WaiterController::class, 'update'])->name('orders.update');
        Route::post('/orders/{order}/serve', [WaiterController::class, 'serve'])->name('orders.serve');
        Route::post('/calls/{call}/answer', [WaiterController::class, 'answerCall'])->name('calls.answer');
    });

// ── Cashier screen (staff users with staff_role = cashier)
Route::middleware(['auth', 'role:staff', 'feature:cashier_module', OperationsStaffRole::class . ':cashier'])
    ->prefix('staff/cashier')->name('staff.cashier.')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('index');
        Route::get('/summary', [CashierController::class, 'summary'])->name('summary');
        Route::get('/orders/{order}', [CashierController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/pay', [CashierController::class, 'pay'])->name('orders.pay');
        Route::get('/orders/{order}/receipt', [CashierController::class, 'receipt'])->name('orders.receipt');
    });

// ── Admin: cash summary / receipts
Route::middleware(['auth', 'role:admin', 'feature:cashier_module'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/cashier/summary', [AdminCashierController::class, 'summary'])->name('cashier.summary');
    Route::get('/cashier/orders/{order}/receipt', [AdminCashierController::class, 'receipt'])->name('cashier.receipt');
});

// ── Admin: online orders
Route::middleware(['auth', 'role:admin', 'feature:online_orders'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/online-orders', [OnlineOrderAdminController::class, 'index'])->name('online-orders.index');
    Route::post('/online-orders/settings', [OnlineOrderAdminController::class, 'settings'])->name('online-orders.settings');
    Route::post('/online-orders/{order}/accept', [OnlineOrderAdminController::class, 'accept'])->name('online-orders.accept');
    Route::post('/online-orders/{order}/reject', [OnlineOrderAdminController::class, 'reject'])->name('online-orders.reject');
    Route::post('/online-orders/{order}/status', [OnlineOrderAdminController::class, 'status'])->name('online-orders.status');
});

// ── Admin: delivery
Route::middleware(['auth', 'role:admin', 'feature:delivery'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery/settings', [DeliveryController::class, 'settings'])->name('delivery.settings');
    Route::post('/delivery/{order}/assign', [DeliveryController::class, 'assign'])->name('delivery.assign');
    Route::post('/delivery/{order}/status', [DeliveryController::class, 'status'])->name('delivery.status');
});

// ── Admin: loyalty
Route::middleware(['auth', 'role:admin', 'feature:loyalty'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::post('/loyalty/settings', [LoyaltyController::class, 'settings'])->name('loyalty.settings');
    Route::get('/loyalty/accounts/{account}', [LoyaltyController::class, 'show'])->name('loyalty.show');
    Route::post('/loyalty/accounts/{account}/adjust', [LoyaltyController::class, 'adjust'])->name('loyalty.adjust');
});

// ── Public online ordering (guest). Restaurant must have online_orders (or delivery) granted.
Route::group([], function () {
    Route::get('/order-online/{restaurant}', [OnlineOrderController::class, 'show'])->name('operations.online.show');
    Route::post('/order-online/{restaurant}', [OnlineOrderController::class, 'place'])->middleware('throttle:20,1')->name('operations.online.place');
    Route::get('/order-online/{restaurant}/track/{orderNumber}', [OnlineOrderController::class, 'track'])->name('operations.online.track');
});
