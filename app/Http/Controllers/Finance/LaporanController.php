<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Services\Finance\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(protected ReportService $report) {}

    public function bukuBesar(Request $request): View
    {
        $accountId = $request->integer('account_id');
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $accounts = Account::aktif()->orderBy('kode')->get();

        $data = null;
        if ($accountId) {
            $data = $this->report->bukuBesar($accountId, $dari, $sampai);
        }

        return view('finance.laporan.buku_besar', compact('accounts', 'accountId', 'dari', 'sampai', 'data'));
    }

    public function trialBalance(Request $request): View
    {
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->trialBalance($sampai);
        return view('finance.laporan.trial_balance', compact('data', 'sampai'));
    }

    public function labaRugi(Request $request): View
    {
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->labaRugi($dari, $sampai);
        return view('finance.laporan.laba_rugi', compact('data', 'dari', 'sampai'));
    }

    public function neraca(Request $request): View
    {
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->neraca($sampai);
        return view('finance.laporan.neraca', compact('data', 'sampai'));
    }

    public function arusKas(Request $request): View
    {
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->arusKas($dari, $sampai);
        return view('finance.laporan.arus_kas', compact('data', 'dari', 'sampai'));
    }

    public function jurnalUmum(Request $request): View
    {
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $items = \App\Models\Finance\Journal::with('lines.account')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')->orderBy('id')
            ->paginate(50)->withQueryString();

        return view('finance.laporan.jurnal_umum', compact('items', 'dari', 'sampai'));
    }
}
