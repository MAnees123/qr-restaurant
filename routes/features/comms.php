<?php

use App\Http\Controllers\Admin\Comms\BarcodeController;
use App\Http\Controllers\Admin\Comms\BrandingController;
use App\Http\Controllers\Admin\Comms\DarkModeController;
use App\Http\Controllers\Admin\Comms\NotificationController;
use App\Http\Controllers\Admin\Comms\PrinterController;
use App\Http\Controllers\Admin\Comms\QrLogoController;
use Illuminate\Support\Facades\Route;

$feat = fn (string $f) => ['auth', 'role:admin', 'feature:' . $f];

// SMS / WhatsApp / Email notifications
foreach (['sms' => 'sms_notif', 'whatsapp' => 'whatsapp_notif', 'email' => 'email_notif'] as $channel => $feature) {
    Route::middleware($feat($feature))->prefix('admin/comms/' . $channel)->name("admin.comms.$channel.")->group(function () use ($channel) {
        Route::get('/', [NotificationController::class, 'index'])->defaults('channel', $channel)->name('index');
        Route::put('/', [NotificationController::class, 'update'])->defaults('channel', $channel)->name('update');
        Route::post('/test', [NotificationController::class, 'test'])->defaults('channel', $channel)->name('test');
    });
}

Route::middleware($feat('custom_branding'))->prefix('admin')->name('admin.')->group(function () {
    Route::get('/branding', [BrandingController::class, 'edit'])->name('branding.edit');
    Route::put('/branding', [BrandingController::class, 'update'])->name('branding.update');
});

Route::middleware(['auth', 'role:admin,kitchen', 'feature:dark_mode'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/dark-mode', [DarkModeController::class, 'toggle'])->name('dark-mode.toggle');
});

Route::middleware($feat('qr_logo'))->prefix('admin')->name('admin.')->group(function () {
    Route::get('/qr-logo', [QrLogoController::class, 'edit'])->name('qr-logo.edit');
    Route::put('/qr-logo', [QrLogoController::class, 'update'])->name('qr-logo.update');
    Route::get('/qr-logo/tables/{table}/svg', [QrLogoController::class, 'svg'])->name('qr-logo.svg');
});

Route::middleware($feat('printer'))->prefix('admin')->name('admin.')->group(function () {
    Route::get('/printer', [PrinterController::class, 'index'])->name('printer.index');
    Route::put('/printer', [PrinterController::class, 'update'])->name('printer.update');
    Route::get('/printer/test', [PrinterController::class, 'testPrint'])->name('printer.test');
    Route::post('/printer/network-test', [PrinterController::class, 'networkTest'])->name('printer.network-test');
    Route::get('/printer/poll', [PrinterController::class, 'poll'])->name('printer.poll');
    Route::get('/printer/orders/{order}/{type}', [PrinterController::class, 'receipt'])->name('printer.receipt');
});

Route::middleware($feat('barcode'))->prefix('admin')->name('admin.')->group(function () {
    Route::get('/barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');
    Route::get('/barcodes/labels', [BarcodeController::class, 'labels'])->name('barcodes.labels');
    Route::get('/barcodes/scan', [BarcodeController::class, 'scan'])->name('barcodes.scan');
    Route::get('/barcodes/lookup', [BarcodeController::class, 'lookup'])->name('barcodes.lookup');
    Route::put('/barcodes/{item}', [BarcodeController::class, 'update'])->name('barcodes.update');
    Route::post('/barcodes/{item}/generate', [BarcodeController::class, 'generate'])->name('barcodes.generate');
    Route::get('/barcodes/{item}/svg', [BarcodeController::class, 'svg'])->name('barcodes.svg');
});
