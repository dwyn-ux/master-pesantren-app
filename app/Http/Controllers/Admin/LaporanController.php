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
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function terpadu(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $awal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth();

        // Summary cards
        $totalSetoran         = Setoran::whereBetween('tanggal', [$awal, $akhir])->count();
        $totalHalamanZiyadah  = Setoran::where('jenis', 'ziyadah')->where('status', 'maqbul')
            ->whereBetween('tanggal', [$awal, $akhir])->sum('jumlah_halaman');
        $revenueKantin  = TransaksiKasir::whereBetween('created_at', [$awal, $akhir])->sum('total');
        $revenueLaundry = LaundryOrder::whereBetween('created_at', [$awal, $akhir])->sum('total');

        // Build daily labels for selected month
        $days = [];
        for ($d = $awal->copy(); $d->lte($akhir); $d->addDay()) {
            $days[] = $d->format('Y-m-d');
        }
        $chartLabels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $days);

        // Daily revenue kantin
        $kantinHarian = TransaksiKasir::selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->whereBetween('created_at', [$awal, $akhir])
            ->groupBy('tgl')->orderBy('tgl')
            ->pluck('total', 'tgl');
        $kantinData = array_map(fn($d) => (int) ($kantinHarian[$d] ?? 0), $days);

        // Daily revenue laundry
        $laundryHarian = LaundryOrder::selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->whereBetween('created_at', [$awal, $akhir])
            ->groupBy('tgl')->orderBy('tgl')
            ->pluck('total', 'tgl');
        $laundryData = array_map(fn($d) => (int) ($laundryHarian[$d] ?? 0), $days);

        // Setoran breakdown by jenis
        $setoranByJenis = Setoran::selectRaw('jenis, COUNT(*) as total')
            ->whereBetween('tanggal', [$awal, $akhir])
            ->groupBy('jenis')->pluck('total', 'jenis');

        // Top 10 santri hafalan bulan ini
        $topHafalan = Setoran::with('santri')
            ->selectRaw('santri_id, SUM(jumlah_halaman) as total_halaman')
            ->where('jenis', 'ziyadah')->where('status', 'maqbul')
            ->whereBetween('tanggal', [$awal, $akhir])
            ->groupBy('santri_id')
            ->orderByDesc('total_halaman')
            ->limit(10)->get();

        // Recent transactions
        $recentKantin  = TransaksiKasir::with('santri')
            ->whereBetween('created_at', [$awal, $akhir])->latest()->limit(5)->get();
        $recentLaundry = LaundryOrder::with('santri')
            ->whereBetween('created_at', [$awal, $akhir])->latest()->limit(5)->get();

        $bulanList = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => Carbon::create(null, $m, 1)->translatedFormat('F'),
        ]);
        $tahunList = range(now()->year - 2, now()->year + 1);

        return view('admin.laporan.terpadu', compact(
            'bulan', 'tahun', 'awal',
            'totalSetoran', 'totalHalamanZiyadah', 'revenueKantin', 'revenueLaundry',
            'chartLabels', 'kantinData', 'laundryData',
            'setoranByJenis', 'topHafalan', 'recentKantin', 'recentLaundry',
            'bulanList', 'tahunList'
        ));
    }

    public function halaqah(Request $request)
    {
        $dari   = $request->dari   ? Carbon::parse($request->dari)->startOfDay()   : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay()   : now()->endOfDay();
        $halaqahId = $request->halaqah_id;

        $halaqahList = Halaqah::with('ustadz')->orderBy('nama')->get();

        $base = Setoran::with(['santri', 'penerima'])->whereBetween('tanggal', [$dari, $sampai]);
        if ($halaqahId) {
            $santriIds = Halaqah::find($halaqahId)?->santri()->pluck('santri.id') ?? collect();
            $base->whereIn('santri_id', $santriIds);
        }

        $totalSetoran        = (clone $base)->count();
        $totalHalamanZiyadah = (clone $base)->where('jenis', 'ziyadah')->where('status', 'maqbul')->sum('jumlah_halaman');
        $totalHalamanMurojaah= (clone $base)->where('jenis', 'murojaah')->sum('jumlah_halaman');
        $santriUnik          = (clone $base)->distinct('santri_id')->count('santri_id');

        $days = $this->buildDays($dari, $sampai);
        $chartLabels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $days);

        $harian = (clone $base)->selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
            ->groupBy('tgl')->orderBy('tgl')->pluck('total', 'tgl');
        $setoranChart = array_map(fn($d) => (int)($harian[$d] ?? 0), $days);

        $topHafalan = Setoran::with('santri')
            ->selectRaw('santri_id, SUM(jumlah_halaman) as total_halaman')
            ->where('jenis', 'ziyadah')->where('status', 'maqbul')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->when($halaqahId, function ($q) use ($halaqahId) {
                $ids = Halaqah::find($halaqahId)?->santri()->pluck('santri.id') ?? collect();
                $q->whereIn('santri_id', $ids);
            })
            ->groupBy('santri_id')->orderByDesc('total_halaman')->limit(10)->get();

        $setoran = (clone $base)->latest('tanggal')->paginate(20)->withQueryString();

        return view('laporan.halaqah', compact(
            'dari', 'sampai', 'halaqahList', 'halaqahId',
            'totalSetoran', 'totalHalamanZiyadah', 'totalHalamanMurojaah', 'santriUnik',
            'chartLabels', 'setoranChart', 'topHafalan', 'setoran'
        ));
    }

    public function kantin(Request $request)
    {
        $dari   = $request->dari   ? Carbon::parse($request->dari)->startOfDay()   : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay()   : now()->endOfDay();

        $outletList = Outlet::where('tipe', 'kantin')->get();
        $outletId   = $request->outlet_id;

        $base = TransaksiKasir::with('santri')->whereBetween('created_at', [$dari, $sampai]);
        if ($outletId) $base->where('outlet_id', $outletId);

        $totalTransaksi = (clone $base)->count();
        $totalRevenue   = (clone $base)->sum('total');
        $santriUnik     = (clone $base)->distinct('santri_id')->count('santri_id');
        $avgTransaksi   = $totalTransaksi > 0 ? (int)($totalRevenue / $totalTransaksi) : 0;

        $days = $this->buildDays($dari, $sampai);
        $chartLabels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $days);
        $harian = (clone $base)->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')->orderBy('tgl')->pluck('total', 'tgl');
        $revenueChart = array_map(fn($d) => (int)($harian[$d] ?? 0), $days);

        $topPembeli = (clone $base)->with('santri')
            ->selectRaw('santri_id, COUNT(*) as jumlah, SUM(total) as total_belanja')
            ->groupBy('santri_id')->orderByDesc('total_belanja')->limit(5)->get();

        $transaksi = (clone $base)->latest()->paginate(20)->withQueryString();

        return view('laporan.kantin', compact(
            'dari', 'sampai', 'outletList', 'outletId',
            'totalTransaksi', 'totalRevenue', 'santriUnik', 'avgTransaksi',
            'chartLabels', 'revenueChart', 'topPembeli', 'transaksi'
        ));
    }

    public function laundry(Request $request)
    {
        $dari   = $request->dari   ? Carbon::parse($request->dari)->startOfDay()   : now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->endOfDay()   : now()->endOfDay();
        $filterStatus = $request->status;

        $base = LaundryOrder::with('santri')->whereBetween('created_at', [$dari, $sampai]);
        if ($filterStatus) $base->where('status', $filterStatus);

        $totalOrder   = (clone $base)->count();
        $totalRevenue = (clone $base)->sum('total');
        $totalKg      = (clone $base)->sum('berat_kg');
        $avgKg        = $totalOrder > 0 ? round($totalKg / $totalOrder, 2) : 0;

        $days = $this->buildDays($dari, $sampai);
        $chartLabels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $days);
        $harian = (clone $base)->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')->orderBy('tgl')->pluck('total', 'tgl');
        $revenueChart = array_map(fn($d) => (int)($harian[$d] ?? 0), $days);

        $statusOptions = ['diterima' => 'Diterima', 'dicuci' => 'Dicuci', 'selesai' => 'Selesai', 'diambil' => 'Diambil'];

        $orders = (clone $base)->latest()->paginate(20)->withQueryString();

        return view('laporan.laundry', compact(
            'dari', 'sampai', 'filterStatus', 'statusOptions',
            'totalOrder', 'totalRevenue', 'totalKg', 'avgKg',
            'chartLabels', 'revenueChart', 'orders'
        ));
    }

    private function buildDays(Carbon $dari, Carbon $sampai): array
    {
        $days = [];
        $limit = $sampai->diffInDays($dari);
        if ($limit > 90) {
            // Group by week label if range > 90 days — return weekly buckets
            for ($d = $dari->copy()->startOfWeek(); $d->lte($sampai); $d->addWeek()) {
                $days[] = $d->format('Y-m-d');
            }
        } else {
            for ($d = $dari->copy(); $d->lte($sampai); $d->addDay()) {
                $days[] = $d->format('Y-m-d');
            }
        }
        return $days;
    }

    public function keuangan()
    {
        $summary = [
            'total_tagihan'      => Tagihan::sum('nominal'),
            'tagihan_belum_bayar'=> Tagihan::where('status', 'belum_bayar')->sum('nominal'),
            'total_pembayaran'   => Pembayaran::paid()->sum('nominal'),
            'jumlah_tagihan'     => Tagihan::count(),
            'jumlah_pembayaran'  => Pembayaran::count(),
        ];

        $recentPayments = Pembayaran::with(['wali', 'tagihan.santri'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.laporan.keuangan', compact('summary', 'recentPayments'));
    }
}
