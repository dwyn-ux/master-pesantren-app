<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tagihan:buat-bulanan')->monthlyOn(1, '06:00');

// Laporan otomatis — hari & jam sesungguhnya di-gate di dalam command (pakai ReportSetting),
// scheduler dijalankan tiap jam supaya flexibility setting admin langsung berlaku.
Schedule::command('reports:weekly')->hourly();
Schedule::command('reports:monthly')->hourly();

// Finance reminders: hutang jatuh tempo, approval pending, budget alert
Schedule::command('finance:reminders')->dailyAt('07:00');

// Pengingat baca Al-Quran setelah Subuh (05:30) dan setelah Maghrib (18:15)
// Waktu WIB (UTC+7) — server UTC, jadi dikurangi 7 jam
Schedule::command('quran:reminder subuh')->dailyAt('22:30');   // 05:30 WIB
Schedule::command('quran:reminder maghrib')->dailyAt('11:15'); // 18:15 WIB
