<?php

use App\Http\Controllers\Admin\FingerprintController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Api\FcmTokenController;
use Illuminate\Support\Facades\Route;

Route::post('/fingerprint/sync', [FingerprintController::class, 'apiSync'])->name('api.fingerprint.sync');
Route::post('/rfid/sync', [\App\Http\Controllers\Admin\RfidController::class, 'apiSync'])->name('api.rfid.sync');
Route::post('/tripay-callback', [PembayaranController::class, 'tripayCallback'])->name('api.tripay-callback');
Route::post('/midtrans-callback', [PembayaranController::class, 'midtransCallback'])->name('api.midtrans-callback');

use App\Http\Controllers\PrayerController;
Route::get('/prayer-times', [PrayerController::class, 'apiPrayerTimes']);
Route::get('/qibla', [PrayerController::class, 'apiQibla']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/fcm-token', [FcmTokenController::class, 'update']);
});

// ── Kantin Offline API (Electron desktop client) ──────────────────────
Route::prefix('kantin')->name('api.kantin.')->group(function () {
    // Verifikasi token (boleh diakses tanpa middleware kantin.device)
    Route::get('auth/verify', [\App\Http\Controllers\Api\Kantin\AuthController::class, 'verify'])->name('auth.verify');

    // Endpoint yang membutuhkan device token
    Route::middleware('kantin.device')->group(function () {
        Route::get('auth/me', [\App\Http\Controllers\Api\Kantin\AuthController::class, 'me'])->name('auth.me');

        // Sync (pull data master, push transaksi)
        Route::get('sync/pull', [\App\Http\Controllers\Api\Kantin\SyncController::class, 'pull'])->name('sync.pull');
        Route::get('sync/saldo', [\App\Http\Controllers\Api\Kantin\SyncController::class, 'saldoSnapshot'])->name('sync.saldo');
        Route::post('sync/push', [\App\Http\Controllers\Api\Kantin\TransaksiController::class, 'push'])->name('sync.push');

        // Lookup santri real-time
        Route::get('santri/lookup', [\App\Http\Controllers\Api\Kantin\SantriController::class, 'lookup'])->name('santri.lookup');
    });
});
