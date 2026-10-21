<?php

use App\Http\Controllers\Finance\AccountController;
use App\Http\Controllers\Finance\AccountingReportController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\JournalController;
use App\Http\Controllers\Finance\PurchaseOrderController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\Finance\SupplierController;
use App\Http\Controllers\Finance\TaxRateController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::middleware('feature:supplier_mgmt')->group(function () {
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    Route::middleware('feature:purchase_mgmt')->group(function () {
        Route::resource('purchases', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->parameters(['purchases' => 'purchase']);
        Route::post('purchases/{purchase}/receive', [PurchaseOrderController::class, 'receive'])->name('purchases.receive');
        Route::post('purchases/{purchase}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchases.cancel');
        Route::post('purchases/{purchase}/pay', [PurchaseOrderController::class, 'pay'])->name('purchases.pay');
    });

    Route::middleware('feature:expense_tracking')->group(function () {
        Route::resource('expenses', ExpenseController::class)->except('show');
        Route::post('expense-categories', [ExpenseController::class, 'storeCategory'])->name('expense-categories.store');
        Route::delete('expense-categories/{category}', [ExpenseController::class, 'destroyCategory'])->name('expense-categories.destroy');
    });

    Route::middleware('feature:billing')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });

    Route::middleware('feature:tax_mgmt')->group(function () {
        Route::get('taxes/summary', [TaxRateController::class, 'summary'])->name('taxes.summary');
        Route::resource('taxes', TaxRateController::class)->except('show')->parameters(['taxes' => 'tax']);
    });

    Route::middleware('feature:accounting')->group(function () {
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::post('accounts/defaults', [AccountController::class, 'seedDefaults'])->name('accounts.defaults');
        Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::resource('journals', JournalController::class)->only(['index', 'create', 'store', 'destroy'])->parameters(['journals' => 'journal']);
        Route::get('accounting/trial-balance', [AccountingReportController::class, 'trialBalance'])->name('accounting.trial-balance');
        Route::get('accounting/profit-loss', [AccountingReportController::class, 'profitLoss'])->name('accounting.profit-loss');
    });

    Route::middleware('feature:reports')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });
});
