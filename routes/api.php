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
