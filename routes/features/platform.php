<?php

use App\Http\Controllers\Platform\AiController;
use App\Http\Controllers\Platform\Api\ApiV1Controller;
use App\Http\Controllers\Platform\ApiTokenController;
use App\Http\Controllers\Platform\BackupController;
use App\Http\Controllers\Platform\DataTransferController;
use App\Http\Controllers\Platform\PwaController;
use App\Http\Controllers\Platform\SecurityController;
use App\Http\Controllers\Platform\TwoFactorChallengeController;
use App\Http\Middleware\ApiTokenAuth;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

// ── api_access: token authenticated, stateless JSON API ─────────────────────
Route::prefix('api/v1')->name('api.v1.')
    ->withoutMiddleware([PreventRequestForgery::class])
    ->group(function () {
        Route::middleware(ApiTokenAuth::class . ':read')->group(function () {
            Route::get('/categories', [ApiV1Controller::class, 'categories'])->name('categories');
            Route::get('/items', [ApiV1Controller::class, 'items'])->name('items');
            Route::get('/tables', [ApiV1Controller::class, 'tables'])->name('tables');
            Route::get('/orders', [ApiV1Controller::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [ApiV1Controller::class, 'order'])->name('orders.show');
        });
        Route::patch('/orders/{id}/status', [ApiV1Controller::class, 'updateStatus'])
            ->middleware(ApiTokenAuth::class . ':write')->name('orders.status');
    });

// ── mobile_app: public PWA assets (404 unless the restaurant has the feature) ─
Route::prefix('pwa/{restaurant}')->name('pwa.')->whereNumber('restaurant')->group(function () {
    Route::get('/manifest.webmanifest', [PwaController::class, 'manifest'])->name('manifest');
    Route::get('/sw.js', [PwaController::class, 'serviceWorker'])->name('sw');
    Route::get('/offline', [PwaController::class, 'offline'])->name('offline');
    Route::get('/icon.svg', [PwaController::class, 'icon'])->name('icon');
    Route::get('/install', [PwaController::class, 'install'])->name('install');
});

// ── advanced_security: 2FA challenge during login (no auth yet) ─────────────
Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store'])->middleware('throttle:20,1')->name('two-factor.challenge.store');

// ── Admin screens ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin', 'feature:api_access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::get('/api-docs', fn () => view('platform.api.docs'))->name('api-tokens.docs');
    Route::delete('/api-tokens/{apiToken}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
});

Route::middleware(['auth', 'role:admin', 'feature:mobile_app'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/mobile-app', [PwaController::class, 'admin'])->name('pwa.index');
    Route::put('/mobile-app', [PwaController::class, 'update'])->name('pwa.update');
});

Route::middleware(['auth', 'role:admin', 'feature:backup'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{name}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{name}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{name}', [BackupController::class, 'destroy'])->name('backups.destroy');
});

Route::middleware(['auth', 'role:admin', 'feature:export_import'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/data', [DataTransferController::class, 'index'])->name('data.index');
    Route::get('/data/{entity}/export', [DataTransferController::class, 'export'])->name('data.export');
    Route::get('/data/{entity}/template', [DataTransferController::class, 'template'])->name('data.template');
    Route::post('/data/{entity}/preview', [DataTransferController::class, 'preview'])->name('data.preview');
    Route::post('/data/{entity}/commit', [DataTransferController::class, 'commit'])->name('data.commit');
});

Route::middleware(['auth', 'role:admin', 'feature:advanced_security'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/security', [SecurityController::class, 'index'])->name('security.index');
    Route::post('/security/2fa/start', [SecurityController::class, 'start'])->name('security.2fa.start');
    Route::post('/security/2fa/confirm', [SecurityController::class, 'confirm'])->name('security.2fa.confirm');
    Route::post('/security/2fa/recovery', [SecurityController::class, 'regenerate'])->name('security.2fa.recovery');
    Route::delete('/security/2fa', [SecurityController::class, 'disable'])->name('security.2fa.disable');
    Route::delete('/security/sessions', [SecurityController::class, 'logoutOthers'])->name('security.sessions.others');
    Route::put('/security/settings', [SecurityController::class, 'updateSettings'])->name('security.settings');
});

Route::middleware(['auth', 'role:admin', 'feature:ai_features'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/smart-tools', [AiController::class, 'index'])->name('ai.index');
    Route::post('/smart-tools/describe', [AiController::class, 'describe'])->name('ai.describe');
    Route::post('/smart-tools/price', [AiController::class, 'price'])->name('ai.price');
    Route::get('/smart-tools/recommendations.json', [AiController::class, 'recommendations'])->name('ai.recommendations');
    Route::put('/smart-tools/settings', [AiController::class, 'settings'])->name('ai.settings');
});
