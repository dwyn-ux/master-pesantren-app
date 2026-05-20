<?php

namespace App\Http\Controllers;

use App\Models\Akademik\AbsensiPelajaran;
use App\Models\Akademik\JadwalPelajaran;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\KelasSantri;
use App\Models\Akademik\TahunAjaran;
use App\Models\Order;
use App\Models\Perizinan;
use App\Models\Produk;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\Ustadz;
use App\Models\Wali;
use App\Services\FeatureManager;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $features = app(FeatureManager::class);
        $taActive = TahunAjaran::active();

        $stats = [
            "santri_aktif" => Santri::where("is_aktif", true)->count(),
            "total_wali"   => Wali::count(),
            "total_ustadz" => Ustadz::count(),
            "tagihan_belum_bayar" => $features->enabled('tagihan')
                ? Tagihan::where("status", "belum_bayar")->count()
                : 0,
        ];

        // Stats akademik (kalau fitur aktif)
        $akademikStats = null;
        if ($features->enabled('akademik_master')) {
            $akademikStats = [
                'total_kelas'  => Kelas::count(),
                'total_jadwal' => JadwalPelajaran::when($taActive, fn ($q) => $q->where('tahun_ajaran_id', $taActive->id))->count(),
                'santri_terdaftar' => $taActive
                    ? KelasSantri::where('tahun_ajaran_id', $taActive->id)->count()
                    : 0,
                'tahun_ajaran' => $taActive,
            ];
        }

        return view("admin.dashboard", compact('stats', 'akademikStats'));
    }

    public function bendahara(): View
    {
        return redirect()->route("bendahara.dashboard");
    }

    public function kesantrian(): View
    {
        $pendingCount    = Perizinan::where('status', 'disetujui_ustadz')->count();
        $approvedToday   = Perizinan::where('status', 'disetujui_kesantrian')
            ->whereDate('updated_at', today())
            ->count();
        $pendingPerizinan = Perizinan::with('santri')
            ->where('status', 'disetujui_ustadz')
            ->latest()
            ->take(10)
            ->get();

        return view('kesantrian.dashboard', compact('pendingCount', 'approvedToday', 'pendingPerizinan'));
    }

    public function kepalapondok(): View
    {
        return view("dashboard.index", ["role" => "Kepala Pondok"]);
    }

    public function ustadz(): View
    {
        $features = app(FeatureManager::class);
        $ustadzId = auth()->user()->ustadz?->id;
        $taActive = TahunAjaran::active();

        $stats = [
            'kelas_diampu'   => 0,
            'jadwal_hari_ini'=> 0,
            'kelas_walikan'  => 0,
            'absensi_minggu_ini' => 0,
        ];

        $jadwalHariIni = collect();
        $kelasWalikan  = collect();

        if ($ustadzId && $features->enabled('akademik_master')) {
            $hariMap = ['Sunday' => 'minggu', 'Monday' => 'senin', 'Tuesday' => 'selasa', 'Wednesday' => 'rabu', 'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu'];
            $hari = $hariMap[now()->format('l')] ?? 'senin';

            $jadwalHariIni = JadwalPelajaran::where('ustadz_id', $ustadzId)
                ->when($taActive, fn ($q) => $q->where('tahun_ajaran_id', $taActive->id))
                ->where('hari', $hari)
                ->with(['kelas.tingkat', 'mataPelajaran'])
                ->orderBy('jam_mulai')
                ->get();

            $stats['jadwal_hari_ini'] = $jadwalHariIni->count();

            $stats['kelas_diampu'] = JadwalPelajaran::where('ustadz_id', $ustadzId)
                ->when($taActive, fn ($q) => $q->where('tahun_ajaran_id', $taActive->id))
                ->distinct('kelas_id')
                ->count('kelas_id');

            $kelasWalikan = Kelas::where('wali_kelas_id', $ustadzId)
                ->with(['tingkat', 'santri'])
                ->get();

            $stats['kelas_walikan'] = $kelasWalikan->count();

            if ($features->enabled('akademik_absensi')) {
                $stats['absensi_minggu_ini'] = AbsensiPelajaran::whereHas('jadwal', fn ($q) => $q->where('ustadz_id', $ustadzId))
                    ->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count();
            }
        }

        return view('ustadz.dashboard', compact('stats', 'jadwalHariIni', 'kelasWalikan', 'taActive'));
    }

    public function wali(): View
    {
        $user = auth()->user();
        $wali = $user->wali;
        $santriIds = $wali?->santri->pluck('id') ?? collect();

        // Data untuk Pop-up Rekap (Cuma ambil data terbaru)
        $rekap = [
            'tagihan' => \App\Models\Tagihan::whereIn('santri_id', $santriIds)
                ->where('status', 'belum_bayar')
                ->with('jenisTagihan', 'santri')
                ->get(),
            'tahfidz' => \App\Models\Setoran::whereIn('santri_id', $santriIds)
                ->with('santri', 'surahAwal', 'surahAkhir')
                ->latest('tanggal')
                ->take(3)
                ->get(),
            // Hanya tampilkan kunjungan yang butuh tindakan wali (rujuk/rawat ortu)
            // DAN belum dikonfirmasi wali — supaya tidak muncul terus setiap buka dashboard
            'kesehatan' => \App\Models\KunjunganKlinik::where('patient_type', \App\Models\Santri::class)
                ->whereIn('patient_id', $santriIds)
                ->where(fn($q) => $q->where('perlu_rujuk', true)->orWhere('perlu_dirawat_ortu', true))
                ->whereNull('wali_konfirmasi')
                ->latest('tanggal_kunjungan')
                ->take(3)
                ->get(),
        ];

        return view("wali.dashboard", compact('rekap'));
    }

    public function testNotification()
    {
        $user = auth()->user();
        
        if (!$user->fcm_token) {
            return back()->with('error', 'Token FCM tidak ditemukan di akun Anda. Silakan buka aplikasi HP dan login kembali.');
        }

        $service = app(\App\Services\FirebaseNotificationService::class);
        $res = $service->sendToUser(
            $user, 
            'Tes Notifikasi Pesantren', 
            'Halo ' . $user->name . ', ini adalah tes notifikasi dari sistem.',
            ['type' => 'test']
        );

        if ($res) {
            return back()->with('success', 'Notifikasi tes berhasil dikirim ke HP Anda.');
        }

        return back()->with('error', 'Gagal mengirim notifikasi. Cek log server untuk detailnya.');
    }

    public function outlet(): View
    {
        $outlet = auth()->user()->outlet;

        if (!$outlet) {
            abort(403, "Profil outlet tidak ditemukan.");
        }

        $stats = [
            "produk_aktif" => 0,
            "stok_menipis" => 0,
            "order_pending" => 0,
            "order_diproses" => 0,
        ];

        if ($outlet->tipe === "kantin") {
            $stats = [
                "produk_aktif" => Produk::where("outlet_id", $outlet->id)
                    ->where("is_aktif", true)
                    ->count(),
                "stok_menipis" => Produk::where("outlet_id", $outlet->id)
                    ->where("is_aktif", true)
                    ->where("stok", "<=", 5)
                    ->count(),
                "order_pending" => Order::where("status", "pending")
                    ->whereHas(
                        "items.produk",
                        fn($query) => $query->where("outlet_id", $outlet->id),
                    )
                    ->count(),
                "order_diproses" => Order::where("status", "diproses")
                    ->whereHas(
                        "items.produk",
                        fn($query) => $query->where("outlet_id", $outlet->id),
                    )
                    ->count(),
            ];
        }

        return view("outlet.dashboard", compact("outlet", "stats"));
    }
}
