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

// QR Code Menu Route (Public)
Route::get('/menu/{code}', [MenuController::class, 'show'])->name('menu.show');

// Cart Routes (Session based)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.discount.apply');
Route::post('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.discount.remove');

// Order Route (Public)
Route::post('/order/place', [GuestOrderController::class, 'place'])->name('order.place');
Route::get('/order/confirmed/{order_number}', [GuestOrderController::class, 'confirmed'])->name('order.confirmed');
Route::get('/order/status/{order_number}', [GuestOrderController::class, 'status'])->name('order.status');
Route::post('/table/call', [TableCallController::class, 'call'])->name('table.call');
Route::get('/table/call/status', [TableCallController::class, 'status'])->name('table.call.status');

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
    Route::get('/revenue', [DashboardController::class, 'revenue'])->name('revenue');
    Route::get('/menu/download', [RestaurantController::class, 'generateMenuPDF'])->name('menu.download');

    Route::resource('restaurant', RestaurantController::class)->only(['index', 'edit', 'update']);
    Route::resource('tables', TableController::class);
    Route::resource('menu-categories', MenuCategoryController::class);
    Route::resource('menu-items', MenuItemController::class);
    Route::resource('discounts', DiscountController::class);
    Route::get('/orders/live-search', [OrderController::class, 'liveSearch'])->name('orders.live-search');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::post('/orders/{order}/payment', [OrderController::class, 'recordPayment'])->name('orders.payment');
    Route::resource('reservations', ReservationController::class)->only(['index', 'update']);
    Route::get('/table-calls', [\App\Http\Controllers\Admin\TableCallController::class, 'index'])->name('table-calls.index');
    Route::get('/order-notifications', [DashboardController::class, 'orderNotifications'])->name('order-notifications');
    Route::post('/table-calls/{call}/accept', [\App\Http\Controllers\Admin\TableCallController::class, 'accept'])->name('table-calls.accept');
    Route::post('/table-calls/{call}/complete', [\App\Http\Controllers\Admin\TableCallController::class, 'complete'])->name('table-calls.complete');
});

// Kitchen Routes
Route::middleware(['auth', 'role:kitchen,admin'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/dashboard', [KitchenController::class, 'index'])->name('dashboard');
    Route::get('/orders', [KitchenController::class, 'getOrders'])->name('orders');
    Route::get('/orders/{status}', [KitchenController::class, 'statusView'])->name('orders.status');
    Route::put('/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('orders.update');
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
