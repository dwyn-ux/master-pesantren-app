<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\Transaksi;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $bulan = (int) request('bulan', now()->month);
        $tahun = (int) request('tahun', now()->year);
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $totalKas = (float) KasBank::aktif()->sum('saldo_berjalan');

        $masuk = (float) Transaksi::where('tipe', 'masuk')
            ->where('status', 'posted')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('nominal');

        $keluar = (float) Transaksi::where('tipe', 'keluar')
            ->where('status', 'posted')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('nominal');

        $kasBanks = KasBank::aktif()->get();

        $recentTransaksi = Transaksi::with(['kategori', 'kasBank'])
            ->where('status', 'posted')
            ->latest('tanggal')->latest('id')
            ->limit(10)->get();

        $masukPerKategori = \DB::table('finance_transaksi as t')
            ->join('finance_kategoris as k', 'k.id', '=', 't.kategori_id')
            ->where('t.tipe', 'masuk')->where('t.status', 'posted')
            ->whereBetween('t.tanggal', [$start, $end])
            ->groupBy('k.id', 'k.nama')
            ->orderByDesc(\DB::raw('SUM(t.nominal)'))
            ->select('k.nama', \DB::raw('SUM(t.nominal) as total'))
            ->limit(5)->get();

        $keluarPerKategori = \DB::table('finance_transaksi as t')
            ->join('finance_kategoris as k', 'k.id', '=', 't.kategori_id')
            ->where('t.tipe', 'keluar')->where('t.status', 'posted')
            ->whereBetween('t.tanggal', [$start, $end])
            ->groupBy('k.id', 'k.nama')
            ->orderByDesc(\DB::raw('SUM(t.nominal)'))
            ->select('k.nama', \DB::raw('SUM(t.nominal) as total'))
            ->limit(5)->get();

        $trendBulanan = collect(range(0, 5))->map(function ($i) {
            $d = now()->subMonths(5 - $i);
            $s = $d->copy()->startOfMonth();
            $e = $d->copy()->endOfMonth();
            return [
                'label'  => $d->translatedFormat('M Y'),
                'masuk'  => (float) Transaksi::where('tipe', 'masuk')->where('status', 'posted')->whereBetween('tanggal', [$s, $e])->sum('nominal'),
                'keluar' => (float) Transaksi::where('tipe', 'keluar')->where('status', 'posted')->whereBetween('tanggal', [$s, $e])->sum('nominal'),
            ];
        });

        return view('finance.dashboard', compact(
            'totalKas', 'masuk', 'keluar', 'kasBanks',
            'recentTransaksi', 'masukPerKategori', 'keluarPerKategori',
            'trendBulanan', 'bulan', 'tahun'
        ));
    }
}
