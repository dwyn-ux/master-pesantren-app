<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\LaundryOrder;
use App\Models\Outlet;
use App\Models\Pembayaran;
use App\Models\Setoran;
use App\Models\Tagihan;
use App\Models\TransaksiKasir;
use App\Services\LaporanAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPdfController extends Controller
{
    public function __construct(protected LaporanAnalysisService $analyzer) {}

    public function keuangan(Request $request)
    {
        $dari = $request->dari ? Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay() : now()->endOfDay();

        $tagihanQuery = Tagihan::whereBetween('created_at', [$dari, $sampai]);
        $totalTagihan = (clone $tagihanQuery)->sum('nominal');
        $belumBayar = (clone $tagihanQuery)->where('status', 'belum_bayar')->sum('nominal');
        $jumlahTagihan = (clone $tagihanQuery)->count();

        $totalPembayaran = Pembayaran::paid()
            ->whereBetween('created_at', [$dari, $sampai])
            ->sum('nominal');
        $jumlahPembayaran = Pembayaran::paid()
            ->whereBetween('created_at', [$dari, $sampai])
            ->count();

        $recentPayments = Pembayaran::with(['wali', 'tagihan.santri'])
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest('created_at')
            ->limit(50)
            ->get();

        $tagihanByJenis = Tagihan::selectRaw('jenis_tagihan_id, SUM(nominal) as total, COUNT(*) as jumlah')
            ->whereBetween('created_at', [$dari, $sampai])
            ->with('jenisTagihan')
            ->groupBy('jenis_tagihan_id')
            ->get();

        $analysis = $this->analyzer->analyzeKeuangan($totalTagihan, $belumBayar, $totalPembayaran);

        $pdf = Pdf::loadView('pdf.laporan.keuangan', [
            'title' => 'Laporan Keuangan',
            'subtitle' => 'Ringkasan tagihan & pembayaran pondok',
            'periode' => $dari->translatedFormat('d M Y') . ' — ' . $sampai->translatedFormat('d M Y'),
            'dari' => $dari,
            'sampai' => $sampai,
            'totalTagihan' => $totalTagihan,
            'belumBayar' => $belumBayar,
            'jumlahTagihan' => $jumlahTagihan,
            'totalPembayaran' => $totalPembayaran,
            'jumlahPembayaran' => $jumlahPembayaran,
            'recentPayments' => $recentPayments,
            'tagihanByJenis' => $tagihanByJenis,
            'analysis' => $analysis,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $dari->format('Ymd') . '-' . $sampai->format('Ymd') . '.pdf');
    }

    public function tahfidz(Request $request)
    {
        $dari = $request->dari ? Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay() : now()->endOfDay();

        $base = Setoran::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()]);

        $totalSetoran = (clone $base)->count();
        $totalHalamanZiyadah = (clone $base)->where('jenis', 'ziyadah')->where('status', 'maqbul')->sum('jumlah_halaman');
        $totalHalamanMurojaah = (clone $base)->where('jenis', 'murojaah')->sum('jumlah_halaman');
        $santriUnik = (clone $base)->distinct('santri_id')->count('santri_id');

        $topHafalan = Setoran::with('santri')
            ->selectRaw('santri_id, SUM(jumlah_halaman) as total_halaman, COUNT(*) as total_setoran')
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->groupBy('santri_id')
            ->orderByDesc('total_halaman')
            ->limit(20)
            ->get();

        // Detail setoran lengkap dengan posisi surah & ayat (max 200 baris)
        $detailSetoran = Setoran::with(['santri', 'surahAwal', 'surahAkhir'])
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('santri_id')
            ->limit(200)
            ->get();

        $perHalaqah = Halaqah::with('ustadz')
            ->get()
            ->map(function ($h) use ($dari, $sampai) {
                $santriIds = $h->santri()->pluck('santri.id');
                $halaman = Setoran::whereIn('santri_id', $santriIds)
                    ->where('jenis', 'ziyadah')
                    ->where('status', 'maqbul')
                    ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
                    ->sum('jumlah_halaman');
                $jumlahSetoran = Setoran::whereIn('santri_id', $santriIds)
                    ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
                    ->count();
                return (object) [
                    'nama' => $h->nama,
                    'ustadz' => $h->ustadz?->nama ?? '-',
                    'jumlah_santri' => $santriIds->count(),
                    'total_halaman' => $halaman,
                    'jumlah_setoran' => $jumlahSetoran,
                ];
            })
            ->sortByDesc('total_halaman');

        $analysis = $this->analyzer->analyzeTahfidz($totalSetoran, (float) $totalHalamanZiyadah, $santriUnik);

        $pdf = Pdf::loadView('pdf.laporan.tahfidz', [
            'title' => 'Laporan Tahfidz',
            'subtitle' => 'Capaian hafalan santri per halaqah',
            'periode' => $dari->translatedFormat('d M Y') . ' — ' . $sampai->translatedFormat('d M Y'),
            'totalSetoran' => $totalSetoran,
            'totalHalamanZiyadah' => $totalHalamanZiyadah,
            'totalHalamanMurojaah' => $totalHalamanMurojaah,
            'santriUnik' => $santriUnik,
            'topHafalan' => $topHafalan,
            'perHalaqah' => $perHalaqah,
            'detailSetoran' => $detailSetoran,
            'analysis' => $analysis,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-tahfidz-' . $dari->format('Ymd') . '-' . $sampai->format('Ymd') . '.pdf');
    }

    public function kantin(Request $request)
    {
        $dari = $request->dari ? Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay() : now()->endOfDay();
        $outletId = $request->outlet_id;

        $base = TransaksiKasir::with('santri', 'outlet')->whereBetween('created_at', [$dari, $sampai]);
        if ($outletId) $base->where('outlet_id', $outletId);

        $totalTransaksi = (clone $base)->count();
        $totalRevenue = (clone $base)->sum('total');
        $santriUnik = (clone $base)->distinct('santri_id')->count('santri_id');
        $avgTransaksi = $totalTransaksi > 0 ? (int) ($totalRevenue / $totalTransaksi) : 0;

        $days = [];
        for ($d = $dari->copy(); $d->lte($sampai); $d->addDay()) {
            $days[] = $d->format('Y-m-d');
        }
        $harian = (clone $base)->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')->orderBy('tgl')->pluck('total', 'tgl');
        $revenueChart = array_map(fn($d) => (int) ($harian[$d] ?? 0), $days);

        $topPembeli = (clone $base)
            ->selectRaw('santri_id, COUNT(*) as jumlah, SUM(total) as total_belanja')
            ->groupBy('santri_id')
            ->orderByDesc('total_belanja')
            ->limit(10)
            ->get();

        $perOutlet = Outlet::where('tipe', 'kantin')
            ->get()
            ->map(function ($o) use ($dari, $sampai) {
                $revenue = TransaksiKasir::where('outlet_id', $o->id)
                    ->whereBetween('created_at', [$dari, $sampai])
                    ->sum('total');
                $trx = TransaksiKasir::where('outlet_id', $o->id)
                    ->whereBetween('created_at', [$dari, $sampai])
                    ->count();
                return (object) [
                    'nama' => $o->nama,
                    'revenue' => $revenue,
                    'transaksi' => $trx,
                ];
            });

        $transaksi = (clone $base)->latest()->limit(100)->get();

        $analysis = $this->analyzer->analyzeRevenue($revenueChart, $totalTransaksi, $totalRevenue, $santriUnik, 'kantin');

        $pdf = Pdf::loadView('pdf.laporan.kantin', [
            'title' => 'Laporan Kantin',
            'subtitle' => 'Ringkasan transaksi kantin pondok',
            'periode' => $dari->translatedFormat('d M Y') . ' — ' . $sampai->translatedFormat('d M Y'),
            'totalTransaksi' => $totalTransaksi,
            'totalRevenue' => $totalRevenue,
            'santriUnik' => $santriUnik,
            'avgTransaksi' => $avgTransaksi,
            'topPembeli' => $topPembeli,
            'perOutlet' => $perOutlet,
            'transaksi' => $transaksi,
            'analysis' => $analysis,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kantin-' . $dari->format('Ymd') . '-' . $sampai->format('Ymd') . '.pdf');
    }

    public function laundry(Request $request)
    {
        $dari = $request->dari ? Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay() : now()->endOfDay();

        $base = LaundryOrder::with('santri')->whereBetween('created_at', [$dari, $sampai]);

        $totalOrder = (clone $base)->count();
        $totalRevenue = (clone $base)->sum('total');
        $totalKg = (float) (clone $base)->sum('berat_kg');
        $avgKg = $totalOrder > 0 ? round($totalKg / $totalOrder, 2) : 0;
        $santriUnik = (clone $base)->distinct('santri_id')->count('santri_id');

        $days = [];
        for ($d = $dari->copy(); $d->lte($sampai); $d->addDay()) {
            $days[] = $d->format('Y-m-d');
        }
        $harian = (clone $base)->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')->orderBy('tgl')->pluck('total', 'tgl');
        $revenueChart = array_map(fn($d) => (int) ($harian[$d] ?? 0), $days);

        $statusBreakdown = (clone $base)
            ->selectRaw('status, COUNT(*) as jumlah, SUM(total) as total')
            ->groupBy('status')
            ->get();

        $topPelanggan = (clone $base)
            ->selectRaw('santri_id, COUNT(*) as jumlah, SUM(total) as total_belanja, SUM(berat_kg) as total_kg')
            ->groupBy('santri_id')
            ->orderByDesc('total_belanja')
            ->limit(10)
            ->get();

        $orders = (clone $base)->latest()->limit(100)->get();

        $analysis = $this->analyzer->analyzeRevenue($revenueChart, $totalOrder, $totalRevenue, $santriUnik, 'laundry');

        $pdf = Pdf::loadView('pdf.laporan.laundry', [
            'title' => 'Laporan Laundry',
            'subtitle' => 'Ringkasan order laundry pondok',
            'periode' => $dari->translatedFormat('d M Y') . ' — ' . $sampai->translatedFormat('d M Y'),
            'totalOrder' => $totalOrder,
            'totalRevenue' => $totalRevenue,
            'totalKg' => $totalKg,
            'avgKg' => $avgKg,
            'santriUnik' => $santriUnik,
            'statusBreakdown' => $statusBreakdown,
            'topPelanggan' => $topPelanggan,
            'orders' => $orders,
            'analysis' => $analysis,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-laundry-' . $dari->format('Ymd') . '-' . $sampai->format('Ymd') . '.pdf');
    }
}
