<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\Setting;
use App\Services\Finance\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LaporanPdfController extends Controller
{
    public function __construct(protected ReportService $report) {}

    protected function org(): array
    {
        return [
            'nama'    => Setting::get('org.nama', 'Yayasan Pesantren'),
            'alamat'  => Setting::get('org.alamat', ''),
            'telepon' => Setting::get('org.telepon', ''),
            'email'   => Setting::get('org.email', ''),
        ];
    }

    public function labaRugi(Request $request): Response
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->labaRugi($dari, $sampai);
        $org = $this->org();

        $pdf = Pdf::loadView('finance.laporan.pdf.laba_rugi', compact('data', 'dari', 'sampai', 'org'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream("laba-rugi-{$dari}-{$sampai}.pdf");
    }

    public function neraca(Request $request): Response
    {
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->neraca($sampai);
        $org = $this->org();

        $pdf = Pdf::loadView('finance.laporan.pdf.neraca', compact('data', 'sampai', 'org'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream("neraca-{$sampai}.pdf");
    }

    public function arusKas(Request $request): Response
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->arusKas($dari, $sampai);
        $org = $this->org();

        $pdf = Pdf::loadView('finance.laporan.pdf.arus_kas', compact('data', 'dari', 'sampai', 'org'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream("arus-kas-{$dari}-{$sampai}.pdf");
    }

    public function bukuBesar(Request $request): Response
    {
        $accountId = $request->integer('account_id');
        abort_unless($accountId, 400, 'account_id required');
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->bukuBesar($accountId, $dari, $sampai);
        $org = $this->org();

        $pdf = Pdf::loadView('finance.laporan.pdf.buku_besar', compact('data', 'dari', 'sampai', 'org'))
            ->setPaper('A4', 'landscape');
        return $pdf->stream("buku-besar-{$accountId}-{$dari}-{$sampai}.pdf");
    }

    public function trialBalance(Request $request): Response
    {
        $sampai = $request->input('sampai', now()->endOfMonth()->toDateString());
        $data = $this->report->trialBalance($sampai);
        $org = $this->org();

        $pdf = Pdf::loadView('finance.laporan.pdf.trial_balance', compact('data', 'sampai', 'org'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream("trial-balance-{$sampai}.pdf");
    }
}
