<?php

use App\Http\Controllers\Admin\FingerprintController;
use App\Http\Controllers\Admin\HalaqahController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\JenisTagihanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MarketplaceOrderController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\UstadzController;
use App\Http\Controllers\Admin\WaliController;
use App\Http\Controllers\Admin\Akademik\KenaikanKelasController;
use App\Http\Controllers\Admin\Akademik\RaportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\LimitUangSakuController as BendaharaLimitUangSakuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kesantrian\PerizinanController as KesantrianPerizinanController;
use App\Http\Controllers\Outlet\KasirKantinController;
use App\Http\Controllers\Outlet\LaundryController;
use App\Http\Controllers\Outlet\MarketplaceOrderController as OutletMarketplaceOrderController;
use App\Http\Controllers\Outlet\ProdukController as OutletProdukController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\Wali\LimitUangSakuController;
use App\Http\Controllers\Wali\MarketplaceController;
use App\Http\Controllers\Wali\VoiceNoteController;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────────────────────
use App\Http\Controllers\PublicController;

Route::get("/", function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match (true) {
            $user->hasRole("superadmin") => redirect()->route("superadmin.dashboard"),
            $user->hasRole("admin") => redirect()->route("admin.dashboard"),
            $user->hasRole("bendahara") => redirect()->route("bendahara.dashboard"),
            $user->hasRole("kepala_pondok") => redirect()->route("kepala-pondok.dashboard"),
            $user->hasRole("kesantrian") => redirect()->route("kesantrian.dashboard"),
            $user->hasRole("ustadz") => redirect()->route("ustadz.dashboard"),
            $user->hasRole("wali") => redirect()->route("wali.dashboard"),
            $user->hasRole("outlet") => redirect()->route("outlet.dashboard"),
            default => redirect()->route("login"),
        };
    }
    return app(PublicController::class)->landingPage();
})->name("public.landing");

Route::get("/simulator", function () {
    return view("simulator");
})->name("simulator");

// Public Quran & Prayer Routes (no auth required)
Route::get("/quran-public", [PublicController::class, "quranIndex"])->name(
    "public.quran.index",
);
Route::get("/quran-public/surah/{nomor}", [
    PublicController::class,
    "quranSurah",
])->name("public.quran.surah");
Route::get("/prayer-public", [PublicController::class, "prayerIndex"])->name(
    "public.prayer.index",
);
Route::post("/prayer-public/next-prayer", [
    PublicController::class,
    "apiNextPrayer",
])->name("public.prayer.next-prayer");

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware("guest")->group(function () {
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"])->name("auth.login");
});

Route::middleware("auth")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name(
        "auth.logout",
    );
    Route::get("/ganti-password", [
        ChangePasswordController::class,
        "show",
    ])->name("auth.change-password");
    Route::post("/ganti-password", [
        ChangePasswordController::class,
        "update",
    ])->name("auth.change-password.update");
});

// ── Superadmin ───────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:superadmin"])
    ->prefix("superadmin")
    ->name("superadmin.")
    ->group(function () {
        Route::get("/dashboard", [
            \App\Http\Controllers\Superadmin\DashboardController::class,
            "index",
        ])->name("dashboard");

        // Staff Password Management
        Route::get("/staff-password", [\App\Http\Controllers\Superadmin\StaffPasswordController::class, "index"])->name("staff-password.index");
        Route::put("/staff-password/{user}", [\App\Http\Controllers\Superadmin\StaffPasswordController::class, "update"])->name("staff-password.update");
        Route::patch("/staff-password/{user}/reset", [\App\Http\Controllers\Superadmin\StaffPasswordController::class, "resetToDefault"])->name("staff-password.reset");

        Route::get("/features", [
            \App\Http\Controllers\Superadmin\FeatureController::class,
            "index",
        ])->name("features.index");
        Route::put("/features", [
            \App\Http\Controllers\Superadmin\FeatureController::class,
            "update",
        ])->name("features.update");
        Route::post("/features/{feature}/toggle", [
            \App\Http\Controllers\Superadmin\FeatureController::class,
            "toggle",
        ])->name("features.toggle");
        Route::post("/features/apply-package/{package}", [
            \App\Http\Controllers\Superadmin\FeatureController::class,
            "applyPackage",
        ])->name("features.apply-package");
    });

// ── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "admin"])->name(
            "dashboard",
        );

        Route::resource("santri", SantriController::class)->except(["show"]);
        Route::get("santri-naik-kelas", [SantriController::class, "naikKelas"])->name("santri.naik-kelas");
        Route::post("santri-naik-kelas", [SantriController::class, "doNaikKelas"])->name("santri.do-naik-kelas");
        Route::post("santri/{santri}/toggle-status", [SantriController::class, "toggleStatus"])->name("santri.toggle-status");
        Route::delete("santri/{santri}/force", [SantriController::class, "forceDestroy"])->name("santri.force-destroy");

        Route::resource("wali", WaliController::class)->except(["show"]);
        Route::post("wali/{wali}/reset-password", [
            WaliController::class,
            "resetPassword",
        ])->name("wali.reset-password");
        Route::get("wali/{wali}/download-credential", [
            WaliController::class,
            "downloadCredential",
        ])->name("wali.download-credential");
        Route::get("wali/download-all-credentials", [
            WaliController::class,
            "downloadAllCredentials",
        ])->name("wali.download-all-credentials");
        Route::post("wali/{wali}/toggle-status", [WaliController::class, "toggleStatus"])->name("wali.toggle-status");
        Route::delete("wali/{wali}/force", [WaliController::class, "forceDestroy"])->name("wali.force-destroy");

        Route::resource("ustadz", UstadzController::class)->except(["show"]);
        Route::post("ustadz/{ustadz}/toggle-status", [
            UstadzController::class,
            "toggleStatus",
        ])->name("ustadz.toggle-status");
        Route::post("ustadz/{ustadz}/reset-password", [
            UstadzController::class,
            "resetPassword",
        ])->name("ustadz.reset-password");
        Route::get("ustadz/{ustadz}/download-credential", [
            UstadzController::class,
            "downloadCredential",
        ])->name("ustadz.download-credential");
        Route::get("ustadz/download-all-credentials", [
            UstadzController::class,
            "downloadAllCredentials",
        ])->name("ustadz.download-all-credentials");

        Route::resource("halaqah", HalaqahController::class)->except(["show"])->middleware("feature:halaqah");
        Route::resource("jenis-tagihan", JenisTagihanController::class)->except(
            ["show"],
        )->middleware("feature:tagihan");
        Route::resource("tagihan", TagihanController::class)->except(["show"])->middleware("feature:tagihan");
        Route::middleware("feature:marketplace")->group(function () {
            Route::get("marketplace/orders", [
                MarketplaceOrderController::class,
                "index",
            ])->name("marketplace.orders.index");
            Route::get("marketplace/orders/{order}", [
                MarketplaceOrderController::class,
                "show",
            ])->name("marketplace.orders.show");
            Route::patch("marketplace/orders/{order}/status", [
                MarketplaceOrderController::class,
                "updateStatus",
            ])->name("marketplace.orders.update-status");
        });
        Route::middleware("feature:tagihan")->group(function () {
            Route::resource("pembayaran", PembayaranController::class)->only([
                "index",
                "create",
                "store",
                "show",
            ]);
            Route::post("pembayaran/{pembayaran}/check-status", [
                PembayaranController::class,
                "checkStatus",
            ])->name("pembayaran.check-status");
            Route::post("pembayaran/{pembayaran}/cancel", [
                PembayaranController::class,
                "cancel",
            ])->name("pembayaran.cancel");

            Route::get("payment-settings", [
                \App\Http\Controllers\Admin\PaymentSettingController::class,
                "index",
            ])->name("payment-settings.index");
            Route::put("payment-settings", [
                \App\Http\Controllers\Admin\PaymentSettingController::class,
                "update",
            ])->name("payment-settings.update");
            Route::post("payment-settings/test", [
                \App\Http\Controllers\Admin\PaymentSettingController::class,
                "testConnection",
            ])->name("payment-settings.test");
        });

        Route::middleware("feature:import_excel")->group(function () {
            Route::post("import/download-template", [
                ImportController::class,
                "downloadTemplate",
            ])->name("import.download-template");
            Route::get("import", [ImportController::class, "index"])->name(
                "import.index",
            );
            Route::post("import", [ImportController::class, "store"])->name(
                "import.store",
            );
        });

        Route::get("laporan/keuangan", [
            LaporanController::class,
            "keuangan",
        ])->name("laporan.keuangan");

        Route::get("laporan/terpadu", [
            LaporanController::class,
            "terpadu",
        ])->name("laporan.terpadu");

        // Laporan PDF (Admin + Bendahara juga — lihat group di bawah)
        Route::middleware("feature:laporan_pdf")->group(function () {
            Route::get("laporan/pdf/keuangan", [\App\Http\Controllers\Admin\LaporanPdfController::class, "keuangan"])->name("laporan.pdf.keuangan");
            Route::get("laporan/pdf/tahfidz",  [\App\Http\Controllers\Admin\LaporanPdfController::class, "tahfidz"])->name("laporan.pdf.tahfidz");
            Route::get("laporan/pdf/kantin",   [\App\Http\Controllers\Admin\LaporanPdfController::class, "kantin"])->name("laporan.pdf.kantin");
            Route::get("laporan/pdf/laundry",  [\App\Http\Controllers\Admin\LaporanPdfController::class, "laundry"])->name("laporan.pdf.laundry");
        });

        // Laporan Excel
        Route::get("laporan/excel/halaqah",  [LaporanController::class, "excelHalaqah"])->name("laporan.excel.halaqah")->middleware("feature:halaqah");
        Route::get("laporan/excel/kantin",   [LaporanController::class, "excelKantin"])->name("laporan.excel.kantin")->middleware("feature:kantin");
        Route::get("laporan/excel/laundry",  [LaporanController::class, "excelLaundry"])->name("laporan.excel.laundry")->middleware("feature:laundry");
        Route::get("laporan/excel/keuangan", [LaporanController::class, "excelKeuangan"])->name("laporan.excel.keuangan")->middleware("feature:tagihan");

        // Report Settings (pengaturan auto notifikasi mingguan & bulanan)
        Route::middleware("feature:laporan_otomatis")->group(function () {
            Route::get("report-settings", [\App\Http\Controllers\Admin\ReportSettingController::class, "index"])->name("report-settings.index");
            Route::put("report-settings", [\App\Http\Controllers\Admin\ReportSettingController::class, "update"])->name("report-settings.update");
        });

        Route::resource("fingerprint", FingerprintController::class)
            ->only(["index", "edit", "update", "destroy"])
            ->parameters(["fingerprint" => "santri"])
            ->middleware("feature:fingerprint");

        Route::resource("rfid", \App\Http\Controllers\Admin\RfidController::class)
            ->only(["index", "edit", "update", "destroy"])
            ->parameters(["rfid" => "santri"])
            ->middleware("feature:rfid");

        // Device Kantin Offline (untuk Electron app)
        Route::middleware("feature:kantin")->group(function () {
            Route::get("kantin-device", [\App\Http\Controllers\Admin\KantinDeviceController::class, "index"])->name("kantin-device.index");
            Route::post("kantin-device", [\App\Http\Controllers\Admin\KantinDeviceController::class, "store"])->name("kantin-device.store");
            Route::post("kantin-device/{device}/regenerate", [\App\Http\Controllers\Admin\KantinDeviceController::class, "regenerate"])->name("kantin-device.regenerate");
            Route::post("kantin-device/{device}/toggle-status", [\App\Http\Controllers\Admin\KantinDeviceController::class, "toggleStatus"])->name("kantin-device.toggle-status");
            Route::delete("kantin-device/{device}", [\App\Http\Controllers\Admin\KantinDeviceController::class, "destroy"])->name("kantin-device.destroy");
            Route::get("kantin-device/{device}/logs", [\App\Http\Controllers\Admin\KantinDeviceController::class, "logs"])->name("kantin-device.logs");
        });

        // Perizinan & Kepulangan
        Route::middleware("feature:perizinan")->group(function () {
            Route::resource("perizinan", \App\Http\Controllers\Admin\PerizinanController::class);
            Route::patch("perizinan/{perizinan}/status", [\App\Http\Controllers\Admin\PerizinanController::class, "updateStatus"])->name("perizinan.update-status");

            Route::resource("sesi-kepulangan", \App\Http\Controllers\Admin\SesiKepulanganController::class);
        });

        // ── Akademik Diniyah ─────────────────────────────────────
        Route::prefix("akademik")->name("akademik.")->group(function () {
            // Master data
            Route::middleware("feature:akademik_master")->group(function () {
                Route::resource("tahun-ajaran", \App\Http\Controllers\Admin\Akademik\TahunAjaranController::class)
                    ->except(["show"])
                    ->parameters(["tahun-ajaran" => "tahunAjaran"]);

                Route::resource("tingkat", \App\Http\Controllers\Admin\Akademik\TingkatController::class)
                    ->except(["show"]);

                Route::resource("kelas", \App\Http\Controllers\Admin\Akademik\KelasController::class)
                    ->except(["show"])
                    ->parameters(["kelas" => "kelas"]);

                Route::resource("mata-pelajaran", \App\Http\Controllers\Admin\Akademik\MataPelajaranController::class)
                    ->except(["show"])
                    ->parameters(["mata-pelajaran" => "mataPelajaran"]);

                Route::get("mata-pelajaran/{mataPelajaran}/kitab", [\App\Http\Controllers\Admin\Akademik\MataPelajaranController::class, "kitab"])
                    ->name("mata-pelajaran.kitab");
                Route::post("mata-pelajaran/{mataPelajaran}/kitab", [\App\Http\Controllers\Admin\Akademik\MataPelajaranController::class, "storeKitab"])
                    ->name("mata-pelajaran.kitab.store");
                Route::delete("mata-pelajaran/{mataPelajaran}/kitab/{kitab}", [\App\Http\Controllers\Admin\Akademik\MataPelajaranController::class, "destroyKitab"])
                    ->name("mata-pelajaran.kitab.destroy");

                Route::resource("jadwal-pelajaran", \App\Http\Controllers\Admin\Akademik\JadwalPelajaranController::class)
                    ->except(["show"])
                    ->parameters(["jadwal-pelajaran" => "jadwalPelajaran"]);
            });

            // Komponen Nilai & KKM
            Route::middleware("feature:akademik_penilaian")->group(function () {
                Route::get("komponen-nilai", [\App\Http\Controllers\Admin\Akademik\KomponenNilaiController::class, "index"])->name("komponen-nilai.index");
                Route::post("komponen-nilai", [\App\Http\Controllers\Admin\Akademik\KomponenNilaiController::class, "store"])->name("komponen-nilai.store");
                Route::put("komponen-nilai/{komponenNilai}", [\App\Http\Controllers\Admin\Akademik\KomponenNilaiController::class, "update"])->name("komponen-nilai.update");
                Route::delete("komponen-nilai/{komponenNilai}", [\App\Http\Controllers\Admin\Akademik\KomponenNilaiController::class, "destroy"])->name("komponen-nilai.destroy");

                Route::get("kkm", [\App\Http\Controllers\Admin\Akademik\KkmController::class, "index"])->name("kkm.index");
                Route::post("kkm", [\App\Http\Controllers\Admin\Akademik\KkmController::class, "store"])->name("kkm.store");
            });

            // Kenaikan Kelas
            Route::middleware("feature:akademik_kenaikan")->group(function () {
                Route::get("/kenaikan", [KenaikanKelasController::class, "index"])->name("kenaikan.index");
                Route::post("/kenaikan/process", [KenaikanKelasController::class, "process"])->name("kenaikan.process");
                Route::post("/kenaikan/execute", [KenaikanKelasController::class, "execute"])->name("kenaikan.execute");
            });

            // Raport
            Route::middleware("feature:akademik_raport")->group(function () {
                Route::get("/raport", [RaportController::class, "index"])->name("raport.index");
                Route::get("/raport/{santri}/preview/{tahunAjaran}", [RaportController::class, "preview"])->name("raport.preview");
                Route::get("/raport/{santri}/download/{tahunAjaran}", [RaportController::class, "download"])->name("raport.download");

                // Pengaturan Raport (KOP, pejabat, bilingual)
                Route::get("/raport-setting", [\App\Http\Controllers\Admin\Akademik\RaportSettingController::class, "index"])->name("raport-setting.index");
                Route::put("/raport-setting", [\App\Http\Controllers\Admin\Akademik\RaportSettingController::class, "update"])->name("raport-setting.update");
            });
        });
    });

// ── Bendahara ─────────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:bendahara|admin"])
    ->prefix("bendahara")
    ->name("bendahara.")
    ->group(function () {
        Route::get("/dashboard", [
            BendaharaDashboardController::class,
            "index",
        ])->name("dashboard");
        Route::get("pembayaran", function () {
            return redirect()->route("admin.pembayaran.index");
        })->name("pembayaran");
        Route::get("tagihan", function () {
            return redirect()->route("admin.tagihan.index");
        })->name("tagihan");

        // Kepulangan (Surat Kesanggupan)
        Route::middleware("feature:perizinan")->group(function () {
            Route::get("kepulangan", [\App\Http\Controllers\Bendahara\KepulanganController::class, "index"])->name("kepulangan.index");
            Route::post("kepulangan/{kepulangan}/acc", [\App\Http\Controllers\Bendahara\KepulanganController::class, "acc"])->name("kepulangan.acc");
        });

        // Limit Uang Saku
        Route::middleware("feature:wallet")->group(function () {
            Route::get("limit-uang-saku", [BendaharaLimitUangSakuController::class, "index"])->name("limit-uang-saku.index");
            Route::patch("limit-uang-saku/{santri}", [BendaharaLimitUangSakuController::class, "update"])->name("limit-uang-saku.update");

            // Top Up History (auto-processed by Tripay callback)
            Route::get("topup", [\App\Http\Controllers\Bendahara\TopUpController::class, "index"])->name("topup.index");
        });
    });

// ── Kesantrian ────────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:kesantrian|admin"])
    ->prefix("kesantrian")
    ->name("kesantrian.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "kesantrian"])->name("dashboard");

        Route::middleware("feature:perizinan")->group(function () {
            Route::get("/perizinan", [KesantrianPerizinanController::class, "index"])->name("perizinan.index");
            Route::patch("/perizinan/{perizinan}/status", [KesantrianPerizinanController::class, "updateStatus"])->name("perizinan.update-status");
        });
    });

// ── Kepala Pondok ─────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:kepala_pondok|admin"])
    ->prefix("kepala-pondok")
    ->name("kepala-pondok.")
    ->group(function () {
        Route::get("/dashboard", [
            DashboardController::class,
            "kepalapondok",
        ])->name("dashboard");
    });

// ── Ustadz ────────────────────────────────────────────────────────────────────
use App\Http\Controllers\Ustadz\HalaqahController as UstadzHalaqahController;
use App\Http\Controllers\Ustadz\VoiceNoteController as UstadzVoiceNoteController;

Route::middleware(["auth", "must.change.pw", "role:ustadz|admin"])
    ->prefix("ustadz")
    ->name("ustadz.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "ustadz"])->name(
            "dashboard",
        );

        // Halaqah & Hafalan
        Route::middleware("feature:halaqah")->group(function () {
            Route::get("/halaqah", [UstadzHalaqahController::class, "index"])->name("halaqah.index");
            Route::get("/halaqah/{halaqah}", [UstadzHalaqahController::class, "show"])->name("halaqah.show");

            // Santri di halaqah
            Route::get("/santri", [UstadzHalaqahController::class, "santri"])->name("santri.index");

            // Setoran / Hafalan (santri halaqah sendiri)
            Route::get("/setoran", [UstadzHalaqahController::class, "setoran"])->name("setoran.index");
            Route::post("/setoran", [UstadzHalaqahController::class, "storeSetoran"])->name("setoran.store");

            // Setoran Umum (semua santri aktif)
            Route::get("/setoran-umum", [UstadzHalaqahController::class, "setoranUmum"])->name("setoran-umum.index");
            Route::post("/setoran-umum", [UstadzHalaqahController::class, "storeSetoranUmum"])->name("setoran-umum.store");

            // API: posisi terakhir setoran santri
            Route::get("/api/last-position", [UstadzHalaqahController::class, "lastPosition"])->name("api.last-position");
        });

        // Klinik (Rekam Medis)
        Route::middleware("feature:klinik")->group(function () {
            Route::get("/klinik", [\App\Http\Controllers\Ustadz\KlinikController::class, "index"])->name("klinik.index");
            Route::post("/klinik", [\App\Http\Controllers\Ustadz\KlinikController::class, "store"])->name("klinik.store");
            Route::get("/klinik/api/search", [\App\Http\Controllers\Ustadz\KlinikController::class, "searchPatient"])->name("klinik.api.search");
            Route::get("/klinik/api/history", [\App\Http\Controllers\Ustadz\KlinikController::class, "history"])->name("klinik.api.history");
        });

        // Voice Note (gratis, bisa attach ke kunjungan klinik)
        Route::middleware("feature:voice_note")->group(function () {
            Route::get("/voice-note", [UstadzVoiceNoteController::class, "index"])->name("voice-note.index");
            Route::get("/voice-note/create", [UstadzVoiceNoteController::class, "create"])->name("voice-note.create");
            Route::post("/voice-note", [UstadzVoiceNoteController::class, "store"])->name("voice-note.store");
            Route::get("/voice-note/{voiceNote}", [UstadzVoiceNoteController::class, "show"])->name("voice-note.show");
        });

        // Perizinan Halaqah
        Route::middleware("feature:perizinan")->group(function () {
            Route::resource("perizinan", \App\Http\Controllers\Admin\PerizinanController::class);
            Route::patch("perizinan/{perizinan}/status", [\App\Http\Controllers\Admin\PerizinanController::class, "updateStatus"])->name("perizinan.update-status");

            Route::resource("sesi-kepulangan", \App\Http\Controllers\Admin\SesiKepulanganController::class);
        });

        // ── Akademik Diniyah (Ustadz) ────────────────────────────
        Route::prefix("akademik")->name("akademik.")->group(function () {
            // Absensi & Jurnal
            Route::middleware("feature:akademik_absensi")->group(function () {
                Route::get("/absensi", [\App\Http\Controllers\Ustadz\Akademik\AbsensiController::class, "index"])->name("absensi.index");
                Route::post("/absensi", [\App\Http\Controllers\Ustadz\Akademik\AbsensiController::class, "store"])->name("absensi.store");
                Route::get("/jurnal", [\App\Http\Controllers\Ustadz\Akademik\AbsensiController::class, "jurnal"])->name("jurnal.index");
                Route::post("/jurnal", [\App\Http\Controllers\Ustadz\Akademik\AbsensiController::class, "storeJurnal"])->name("absensi.store-jurnal");
            });

            // Penilaian
            Route::middleware("feature:akademik_penilaian")->group(function () {
                Route::get("/nilai", [\App\Http\Controllers\Ustadz\Akademik\NilaiController::class, "index"])->name("nilai.index");
                Route::post("/nilai", [\App\Http\Controllers\Ustadz\Akademik\NilaiController::class, "store"])->name("nilai.store");
            });

            // Halaqah Diniyah
            Route::middleware("feature:halaqah_diniyah")->group(function () {
                Route::get("/halaqah-diniyah", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "index"])->name("halaqah-diniyah.index");
                Route::get("/halaqah-diniyah/{halaqah}/santri", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "santri"])->name("halaqah-diniyah.santri");
                Route::get("/halaqah-diniyah/{halaqah}/setoran", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "setoran"])->name("halaqah-diniyah.setoran");
                Route::post("/halaqah-diniyah/{halaqah}/setoran", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "storeSetoran"])->name("halaqah-diniyah.store-setoran");
                Route::get("/halaqah-diniyah/{halaqah}/khataman", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "khataman"])->name("halaqah-diniyah.khataman");
                Route::post("/halaqah-diniyah/{halaqah}/khataman", [\App\Http\Controllers\Ustadz\Akademik\HalaqahDiniyahController::class, "storeKhataman"])->name("halaqah-diniyah.store-khataman");
            });

            // Wali Kelas (penilaian sikap & catatan)
            Route::middleware("feature:akademik_raport")->group(function () {
                Route::get("/wali-kelas", [\App\Http\Controllers\Ustadz\Akademik\WaliKelasController::class, "index"])->name("wali-kelas.index");
                Route::get("/wali-kelas/{kelas}", [\App\Http\Controllers\Ustadz\Akademik\WaliKelasController::class, "show"])->name("wali-kelas.show");
                Route::get("/wali-kelas/{kelas}/santri/{santri}/sikap", [\App\Http\Controllers\Ustadz\Akademik\WaliKelasController::class, "sikap"])->name("wali-kelas.sikap");
                Route::post("/wali-kelas/{kelas}/santri/{santri}/sikap", [\App\Http\Controllers\Ustadz\Akademik\WaliKelasController::class, "storeSikap"])->name("wali-kelas.store-sikap");
            });
        });
    });

// ── Wali ──────────────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:wali"])
    ->prefix("wali")
    ->name("wali.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "wali"])->name(
            "dashboard",
        );
        Route::post("/dashboard/test-notification", [DashboardController::class, "testNotification"])->name("dashboard.test-notification");

        // Marketplace
        Route::middleware("feature:marketplace")->group(function () {
            Route::get("/marketplace", [
                MarketplaceController::class,
                "index",
            ])->name("marketplace.index");
            Route::post("/marketplace/order", [
                MarketplaceController::class,
                "createOrder",
            ])->name("marketplace.create-order");
            Route::get("/marketplace/success/{order}", [
                MarketplaceController::class,
                "orderSuccess",
            ])->name("marketplace.success");
            Route::get("/marketplace/orders", [
                MarketplaceController::class,
                "orders",
            ])->name("marketplace.orders");
            Route::get("/marketplace/order/{order}", [
                MarketplaceController::class,
                "orderDetail",
            ])->name("marketplace.detail");
        });

        // Voice Note
        Route::middleware("feature:voice_note")->group(function () {
            Route::get("/voice-note", [VoiceNoteController::class, "index"])->name(
                "voice-note.index",
            );
            Route::get("/voice-note/create", [
                VoiceNoteController::class,
                "create",
            ])->name("voice-note.create");
            Route::post("/voice-note", [VoiceNoteController::class, "store"])->name(
                "voice-note.store",
            );
            Route::get("/voice-note/success/{voiceNote}", [
                VoiceNoteController::class,
                "success",
            ])->name("voice-note.success");
            Route::get("/voice-note/{voiceNote}", [
                VoiceNoteController::class,
                "show",
            ])->name("voice-note.show");
            Route::post("/voice-note/{voiceNote}/mark-read", [
                VoiceNoteController::class,
                "markAsRead",
            ])->name("voice-note.mark-read");
        });

        // Perizinan
        Route::middleware("feature:perizinan")->group(function () {
            Route::get("/perizinan", [\App\Http\Controllers\Wali\PerizinanController::class, "index"])->name("perizinan.index");
            Route::post("/perizinan", [\App\Http\Controllers\Wali\PerizinanController::class, "store"])->name("perizinan.store");
        });

        // Limit Uang Saku & Top Up (Wallet)
        Route::middleware("feature:wallet")->group(function () {
            Route::get("/limit-uang-saku", [LimitUangSakuController::class, "index"])->name("limit-uang-saku.index");
            Route::patch("/limit-uang-saku/{santri}", [LimitUangSakuController::class, "update"])->name("limit-uang-saku.update");

            // Top Up
            Route::get("/topup", [\App\Http\Controllers\Wali\TopUpController::class, "index"])->name("topup.index");
            Route::get("/topup/baru", [\App\Http\Controllers\Wali\TopUpController::class, "create"])->name("topup.create");
            Route::post("/topup", [\App\Http\Controllers\Wali\TopUpController::class, "store"])->name("topup.store");
            Route::get("/topup/{topUp}", [\App\Http\Controllers\Wali\TopUpController::class, "show"])->name("topup.show");
        });

        // Laporan per santri
        Route::get("/laporan/{santri}", [\App\Http\Controllers\Wali\LaporanController::class, "show"])->name("laporan.show");
        Route::get("/laporan/{santri}/bulanan", [\App\Http\Controllers\Wali\LaporanController::class, "bulanan"])->name("laporan.bulanan");
        Route::get("/laporan/{santri}/semester", [\App\Http\Controllers\Wali\LaporanController::class, "semester"])->name("laporan.semester");
        Route::get("/laporan/{santri}/tahunan", [\App\Http\Controllers\Wali\LaporanController::class, "tahunan"])->name("laporan.tahunan");

        // Kesehatan
        Route::middleware("feature:klinik")->group(function () {
            Route::get("/kesehatan", [\App\Http\Controllers\Wali\KesehatanController::class, "index"])->name("kesehatan.index");
            Route::get("/kesehatan/{kunjungan}", [\App\Http\Controllers\Wali\KesehatanController::class, "show"])->name("kesehatan.show");
            Route::post("/kesehatan/{kunjungan}/konfirmasi", [\App\Http\Controllers\Wali\KesehatanController::class, "konfirmasi"])->name("kesehatan.konfirmasi");
        });

        // Tagihan
        Route::middleware("feature:tagihan")->group(function () {
            Route::get("/tagihan", [\App\Http\Controllers\Wali\TagihanController::class, "index"])->name("tagihan.index");
            Route::get("/tagihan/poll", [\App\Http\Controllers\Wali\TagihanController::class, "pollStatus"])->name("tagihan.poll");
            Route::get("/tagihan/{tagihan}/bayar", [\App\Http\Controllers\Wali\TagihanController::class, "pay"])->name("tagihan.pay");
            Route::post("/tagihan/{tagihan}/bayar", [\App\Http\Controllers\Wali\TagihanController::class, "processPayment"])->name("tagihan.pay.process");
            Route::get("/tagihan/{tagihan}/status", [\App\Http\Controllers\Wali\TagihanController::class, "show"])->name("tagihan.show");
            Route::post("/tagihan/checkout", [\App\Http\Controllers\Wali\TagihanController::class, "checkout"])->name("tagihan.checkout");
        });

        // Route Update Token via Web (untuk WebView)
        Route::post('/update-fcm-token', [\App\Http\Controllers\Api\FcmTokenController::class, 'update'])->name('update-fcm-token');
    });

// ── Outlet ────────────────────────────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:outlet|admin"])
    ->prefix("outlet")
    ->name("outlet.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "outlet"])->name(
            "dashboard",
        );

        Route::middleware("feature:kantin")->group(function () {
            Route::resource("produk", OutletProdukController::class)->except([
                "show",
            ]);
            Route::get("kasir/kantin", [
                KasirKantinController::class,
                "index",
            ])->name("kasir.kantin.index");
            Route::post("kasir/kantin", [
                KasirKantinController::class,
                "store",
            ])->name("kasir.kantin.store");
            Route::get("kasir/kantin/history", [
                KasirKantinController::class,
                "history",
            ])->name("kasir.kantin.history");
            Route::get("kasir/kantin/{transaksi}", [
                KasirKantinController::class,
                "show",
            ])->name("kasir.kantin.show");
        });

        Route::middleware("feature:laundry")->group(function () {
            Route::get("laundry", [LaundryController::class, "index"])->name(
                "laundry.index",
            );
            Route::get("laundry/create", [
                LaundryController::class,
                "create",
            ])->name("laundry.create");
            Route::post("laundry", [LaundryController::class, "store"])->name(
                "laundry.store",
            );
            Route::get("laundry/{laundry}", [
                LaundryController::class,
                "show",
            ])->name("laundry.show");
            Route::patch("laundry/{laundry}/status", [
                LaundryController::class,
                "updateStatus",
            ])->name("laundry.update-status");
        });

        Route::middleware("feature:marketplace")->group(function () {
            Route::get("marketplace/orders", [
                OutletMarketplaceOrderController::class,
                "index",
            ])->name("marketplace.orders.index");
            Route::get("marketplace/orders/{order}", [
                OutletMarketplaceOrderController::class,
                "show",
            ])->name("marketplace.orders.show");
            Route::patch("marketplace/orders/{order}/status", [
                OutletMarketplaceOrderController::class,
                "updateStatus",
            ])->name("marketplace.orders.update-status");
        });
    });

// ── Laporan (Admin + Bendahara) ───────────────────────────────────────────────
Route::middleware(["auth", "must.change.pw", "role:admin|bendahara"])
    ->prefix("laporan")
    ->name("laporan.")
    ->group(function () {
        Route::get("halaqah", [LaporanController::class, "halaqah"])->name("halaqah")->middleware("feature:halaqah");
        Route::get("kantin", [LaporanController::class, "kantin"])->name("kantin")->middleware("feature:kantin");
        Route::get("laundry", [LaporanController::class, "laundry"])->name("laundry")->middleware("feature:laundry");

        // PDF
        Route::middleware("feature:laporan_pdf")->group(function () {
            Route::get("pdf/keuangan", [\App\Http\Controllers\Admin\LaporanPdfController::class, "keuangan"])->name("pdf.keuangan");
            Route::get("pdf/tahfidz",  [\App\Http\Controllers\Admin\LaporanPdfController::class, "tahfidz"])->name("pdf.tahfidz");
            Route::get("pdf/kantin",   [\App\Http\Controllers\Admin\LaporanPdfController::class, "kantin"])->name("pdf.kantin");
            Route::get("pdf/laundry",  [\App\Http\Controllers\Admin\LaporanPdfController::class, "laundry"])->name("pdf.laundry");
        });

        // Excel
        Route::get("excel/halaqah",  [LaporanController::class, "excelHalaqah"])->name("excel.halaqah")->middleware("feature:halaqah");
        Route::get("excel/kantin",   [LaporanController::class, "excelKantin"])->name("excel.kantin")->middleware("feature:kantin");
        Route::get("excel/laundry",  [LaporanController::class, "excelLaundry"])->name("excel.laundry")->middleware("feature:laundry");
        Route::get("excel/keuangan", [LaporanController::class, "excelKeuangan"])->name("excel.keuangan")->middleware("feature:tagihan");
    });

// ── Notifications (semua user login) ──────────────────────────────────────────
Route::middleware(["auth", "must.change.pw"])
    ->prefix("notifications")
    ->name("notifications.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\NotificationController::class, "index"])->name("index");
        Route::get("/{notification}/read", [\App\Http\Controllers\NotificationController::class, "markAsRead"])->name("read");
        Route::post("/mark-all", [\App\Http\Controllers\NotificationController::class, "markAllAsRead"])->name("mark-all");
        Route::get("/unread-count", [\App\Http\Controllers\NotificationController::class, "unreadCount"])->name("unread-count");
    });

// ── Quran & Prayer (Public with auth) ──────────────────────────────────────────
Route::middleware(["auth", "must.change.pw"])->group(function () {
    // Quran
    Route::middleware("feature:quran")->group(function () {
        Route::get("/quran", [QuranController::class, "index"])->name(
            "quran.index",
        );
        Route::get("/quran/surah/{nomor}", [QuranController::class, "surah"])->name(
            "quran.surah",
        );
        Route::get("/quran/ayat/{surah}/{ayat}", [
            QuranController::class,
            "ayat",
        ])->name("quran.ayat");
        Route::get("/quran/tafsir/{surah}/{ayat}", [
            QuranController::class,
            "tafsir",
        ])->name("quran.tafsir");
        Route::get("/quran/search", [QuranController::class, "search"])->name(
            "quran.search",
        );
    });

    // Prayer Times
    Route::middleware("feature:prayer")->group(function () {
        Route::get("/prayer", [PrayerController::class, "index"])->name(
            "prayer.index",
        );
        Route::get("/prayer/qibla", [PrayerController::class, "qibla"])->name(
            "prayer.qibla",
        );
        Route::get("/prayer/calendar", [PrayerController::class, "calendar"])->name(
            "prayer.calendar",
        );
        Route::post("/prayer/next-prayer", [
            PrayerController::class,
            "nextPrayer",
        ])->name("prayer.next-prayer");
    });
});
