<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Guest\MenuController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\GuestOrderController;
use App\Http\Controllers\Guest\GuestReservationController;
use App\Http\Controllers\Guest\TableCallController;
use Illuminate\Support\Facades\Route;

// Guest / Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});
// Guest / Public Routes wrapped with guest tracker middleware
Route::middleware(['guest.tracker'])->group(function() {
    // QR Code Menu Route (Public)
    Route::get('/menu/{code}', [MenuController::class, 'show'])->name('menu.show');

    // Cart Routes (Session based)
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.discount.apply');
    Route::post('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.discount.remove');
    Route::post('/cart/save-notes', function (\Illuminate\Http\Request $request) {
        session()->put('order_notes', $request->input('notes', ''));
        return response()->json(['ok' => true]);
    })->name('cart.save-notes');

    // Order Route (Public)
    Route::get('/menu/{code}/payment', [GuestOrderController::class, 'paymentForm'])->name('payment.show');
    Route::post('/order/place', [GuestOrderController::class, 'place'])->name('order.place');
    Route::get('/order/confirmed/{order_number}', [GuestOrderController::class, 'confirmed'])->name('order.confirmed');
    Route::get('/order/status/{order_number}', [GuestOrderController::class, 'status'])->name('order.status');
    Route::post('/table/call', [TableCallController::class, 'call'])->name('table.call');
    Route::get('/table/call/status', [TableCallController::class, 'status'])->name('table.call.status');
    
    // New route for getting all active guest orders
    Route::get('/guest/active-orders', [GuestOrderController::class, 'activeOrders'])->name('guest.active-orders');
});

// Redirect standard Breeze dashboard route based on role
Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->role === 'kitchen') {
        return redirect()->route('kitchen.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu/download', [RestaurantController::class, 'generateMenuPDF'])->name('menu.download');
    Route::resource('restaurant', RestaurantController::class)->only(['index', 'edit', 'update']);
    Route::resource('menu-categories', MenuCategoryController::class);
    Route::resource('menu-items', MenuItemController::class);
    Route::get('/orders/live-search', [OrderController::class, 'liveSearch'])->name('orders.live-search');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::post('/orders/{order}/payment', [OrderController::class, 'recordPayment'])->name('orders.payment');
    Route::get('/order-notifications', [DashboardController::class, 'orderNotifications'])->name('order-notifications');

    // Theme Customization Feature
    Route::middleware('feature:theme_custom')->group(function () {
        Route::get('/themes', [App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('themes.index');
        Route::post('/themes/apply', [App\Http\Controllers\Admin\ThemeController::class, 'apply'])->name('themes.apply');
    });

    // Analytics Dashboard Feature
    Route::middleware('feature:analytics')->group(function () {
        Route::get('/revenue', [DashboardController::class, 'revenue'])->name('revenue');
        Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/api/sales', [App\Http\Controllers\Admin\AnalyticsController::class, 'salesApi'])->name('analytics.api.sales');
        Route::get('/analytics/api/top-items', [App\Http\Controllers\Admin\AnalyticsController::class, 'topItemsApi'])->name('analytics.api.topitems');
    });

    // Multi-Branch System Feature
    Route::middleware('feature:multi_branch')->group(function () {
        Route::resource('branches', App\Http\Controllers\Admin\BranchController::class)->except(['create', 'show', 'edit']);
    });

    // Inventory System Feature
    Route::middleware('feature:inventory')->group(function () {
        Route::resource('portions', App\Http\Controllers\Admin\PortionController::class)->except(['create', 'show', 'edit']);
    });

    // Coupons & Discounts Feature
    Route::middleware('feature:coupons')->group(function () {
        Route::resource('discounts', DiscountController::class);
    });

    // Hot Deals & Banners Feature
    Route::middleware('feature:banners')->group(function () {
        Route::resource('banners', BannerController::class);
    });

    // Table Reservation Feature
    Route::middleware('feature:table_reservation')->group(function () {
        Route::resource('reservations', ReservationController::class);
    });

    // QR Table Ordering Feature
    Route::middleware('feature:table_ordering')->group(function () {
        Route::resource('tables', TableController::class);
        Route::get('/table-calls', [\App\Http\Controllers\Admin\TableCallController::class, 'index'])->name('table-calls.index');
        Route::post('/table-calls/{call}/accept', [\App\Http\Controllers\Admin\TableCallController::class, 'accept'])->name('table-calls.accept');
        Route::post('/table-calls/{call}/complete', [\App\Http\Controllers\Admin\TableCallController::class, 'complete'])->name('table-calls.complete');
    });
});

// Kitchen Routes
Route::middleware(['auth', 'role:kitchen,admin'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/dashboard', [KitchenController::class, 'index'])->name('dashboard');
    Route::get('/orders', [KitchenController::class, 'getOrders'])->name('orders');
    Route::get('/orders/{status}', [KitchenController::class, 'statusView'])->name('orders.status');
    Route::put('/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('orders.update');
    Route::get('/orders/{order}/details', [KitchenController::class, 'orderDetails'])->name('orders.details');
    Route::get('/orders/{order}/print', [KitchenController::class, 'print'])->name('orders.print');
});

// Guest Reservations
Route::get('/reservations/new', [GuestReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations', [GuestReservationController::class, 'store'])->name('reservations.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// =============================================
// SUPER ADMIN ROUTES — Isolated from restaurant
// =============================================
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Tenant (Restaurant) Management
    Route::get('/tenants',                              [\App\Http\Controllers\SuperAdmin\TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create',                       [\App\Http\Controllers\SuperAdmin\TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants',                             [\App\Http\Controllers\SuperAdmin\TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{restaurant}',                 [\App\Http\Controllers\SuperAdmin\TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{restaurant}/edit',            [\App\Http\Controllers\SuperAdmin\TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{restaurant}',                 [\App\Http\Controllers\SuperAdmin\TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{restaurant}',              [\App\Http\Controllers\SuperAdmin\TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::post('/tenants/{restaurant}/toggle-suspension', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'toggleSuspension'])->name('tenants.toggle-suspension');
    Route::post('/tenants/{restaurant}/features',       [\App\Http\Controllers\SuperAdmin\TenantController::class, 'updateFeatures'])->name('tenants.update-features');
    Route::post('/tenants/{restaurant}/impersonate',     [\App\Http\Controllers\SuperAdmin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('/tenants/{restaurant}/reset-password',   [\App\Http\Controllers\SuperAdmin\TenantController::class, 'resetPassword'])->name('tenants.reset-password');
    Route::post('/users/{user}/reset-password',           [\App\Http\Controllers\SuperAdmin\TenantController::class, 'resetUserPassword'])->name('tenants.reset-user-password');

    // Subscription Plans Management
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class);

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('analytics.index');

    // Billing & Invoices
    Route::get('/billing', [\App\Http\Controllers\SuperAdmin\BillingController::class, 'index'])->name('billing.index');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Impersonation leave route (needs to be outside superadmin middleware group because we are currently logged in as a normal user)
Route::middleware(['auth'])->get('/superadmin/impersonate/leave', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'leaveImpersonate'])->name('superadmin.tenants.leave-impersonate');
