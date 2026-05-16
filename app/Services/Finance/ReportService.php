<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function bukuBesar(int $accountId, string $dari, string $sampai): array
    {
        $account = Account::findOrFail($accountId);

        $saldoAwal = (float) DB::table('finance_journal_lines as jl')
            ->join('finance_journals as j', 'j.id', '=', 'jl.journal_id')
            ->where('jl.account_id', $accountId)
            ->where('j.status', 'posted')
            ->where('j.tanggal', '<', $dari)
            ->selectRaw($account->saldo_normal === 'debit'
                ? 'COALESCE(SUM(jl.debit) - SUM(jl.kredit), 0) as saldo'
                : 'COALESCE(SUM(jl.kredit) - SUM(jl.debit), 0) as saldo')
            ->value('saldo');

        $rows = DB::table('finance_journal_lines as jl')
            ->join('finance_journals as j', 'j.id', '=', 'jl.journal_id')
            ->where('jl.account_id', $accountId)
            ->where('j.status', 'posted')
            ->whereBetween('j.tanggal', [$dari, $sampai])
            ->orderBy('j.tanggal')
            ->orderBy('j.id')
            ->select('j.tanggal', 'j.nomor', 'j.keterangan as journal_ket', 'jl.keterangan', 'jl.debit', 'jl.kredit')
            ->get();

        $running = $saldoAwal;
        $items   = [];
        foreach ($rows as $row) {
            $running += $account->saldo_normal === 'debit'
                ? ((float) $row->debit - (float) $row->kredit)
                : ((float) $row->kredit - (float) $row->debit);
            $items[] = [
                'tanggal'    => $row->tanggal,
                'nomor'      => $row->nomor,
                'keterangan' => $row->keterangan ?: $row->journal_ket,
                'debit'      => (float) $row->debit,
                'kredit'     => (float) $row->kredit,
                'saldo'      => $running,
            ];
        }

        return [
            'account'    => $account,
            'saldo_awal' => $saldoAwal,
            'items'      => $items,
            'saldo_akhir'=> $running,
        ];
    }

    public function trialBalance(string $sampai): array
    {
        $accounts = Account::aktif()->orderBy('kode')->get();
        $rows = [];
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($accounts as $acc) {
            $debit = (float) DB::table('finance_journal_lines as jl')
                ->join('finance_journals as j', 'j.id', '=', 'jl.journal_id')
                ->where('jl.account_id', $acc->id)
                ->where('j.status', 'posted')
                ->where('j.tanggal', '<=', $sampai)
                ->sum('jl.debit');
            $kredit = (float) DB::table('finance_journal_lines as jl')
                ->join('finance_journals as j', 'j.id', '=', 'jl.journal_id')
                ->where('jl.account_id', $acc->id)
                ->where('j.status', 'posted')
                ->where('j.tanggal', '<=', $sampai)
                ->sum('jl.kredit');

            if ($debit == 0 && $kredit == 0) continue;

            $saldo = $acc->saldo_normal === 'debit' ? $debit - $kredit : $kredit - $debit;
            $rows[] = [
                'account'      => $acc,
                'debit'        => $debit,
                'kredit'       => $kredit,
                'saldo'        => $saldo,
                'saldo_normal' => $acc->saldo_normal,
            ];
            $totalDebit  += $debit;
            $totalKredit += $kredit;
        }

        return [
            'rows'         => $rows,
            'total_debit'  => $totalDebit,
            'total_kredit' => $totalKredit,
        ];
    }

    public function labaRugi(string $dari, string $sampai): array
    {
        $pendapatan = DB::table('finance_accounts as a')
            ->leftJoin('finance_journal_lines as jl', 'jl.account_id', '=', 'a.id')
            ->leftJoin('finance_journals as j', 'j.id', '=', 'jl.journal_id')
            ->where('a.tipe', 'revenue')
            ->where(function ($q) use ($dari, $sampai) {
                $q->whereBetween('j.tanggal', [$dari, $sampai])->where('j.status', 'posted');
            })
            ->groupBy('a.id', 'a.kode', 'a.nama')
            ->orderBy('a.kode')
            ->select('a.id', 'a.kode', 'a.nama', DB::raw('COALESCE(SUM(jl.kredit) - SUM(jl.debit), 0) as saldo'))
            ->get();

        $beban = DB::table('finance_accounts as a')
            ->leftJoin('finance_journal_lines as jl', 'jl.account_id', '=', 'a.id')
            ->leftJoin('finance_journals as j', 'j.id', '=', 'jl.journal_id')
            ->where('a.tipe', 'expense')
            ->where(function ($q) use ($dari, $sampai) {
                $q->whereBetween('j.tanggal', [$dari, $sampai])->where('j.status', 'posted');
            })
            ->groupBy('a.id', 'a.kode', 'a.nama')
            ->orderBy('a.kode')
            ->select('a.id', 'a.kode', 'a.nama', DB::raw('COALESCE(SUM(jl.debit) - SUM(jl.kredit), 0) as saldo'))
            ->get();

        $totalPendapatan = (float) $pendapatan->sum('saldo');
        $totalBeban      = (float) $beban->sum('saldo');

        return [
            'dari'             => $dari,
            'sampai'           => $sampai,
            'pendapatan'       => $pendapatan,
            'beban'            => $beban,
            'total_pendapatan' => $totalPendapatan,
            'total_beban'      => $totalBeban,
            'laba_bersih'      => $totalPendapatan - $totalBeban,
        ];
    }

    public function neraca(string $sampai): array
    {
        $rows = function (string $tipe) use ($sampai) {
            return DB::table('finance_accounts as a')
                ->leftJoin('finance_journal_lines as jl', 'jl.account_id', '=', 'a.id')
                ->leftJoin('finance_journals as j', 'j.id', '=', 'jl.journal_id')
                ->where('a.tipe', $tipe)
                ->where(function ($q) use ($sampai) {
                    $q->where('j.tanggal', '<=', $sampai)->where('j.status', 'posted')
                      ->orWhereNull('j.id');
                })
                ->groupBy('a.id', 'a.kode', 'a.nama', 'a.saldo_normal')
                ->orderBy('a.kode')
                ->select('a.id', 'a.kode', 'a.nama', 'a.saldo_normal',
                    DB::raw('COALESCE(SUM(jl.debit), 0) as total_debit'),
                    DB::raw('COALESCE(SUM(jl.kredit), 0) as total_kredit'))
                ->get()
                ->map(function ($r) {
                    $r->saldo = $r->saldo_normal === 'debit'
                        ? (float) $r->total_debit - (float) $r->total_kredit
                        : (float) $r->total_kredit - (float) $r->total_debit;
                    return $r;
                })
                ->filter(fn($r) => abs($r->saldo) > 0.01)
                ->values();
        };

        $aset      = $rows('asset');
        $liabilitas= $rows('liability');
        $ekuitas   = $rows('equity');

        $lr        = $this->labaRugi('1900-01-01', $sampai);

        return [
            'sampai'           => $sampai,
            'aset'             => $aset,
            'liabilitas'       => $liabilitas,
            'ekuitas'          => $ekuitas,
            'total_aset'       => (float) $aset->sum('saldo'),
            'total_liabilitas' => (float) $liabilitas->sum('saldo'),
            'total_ekuitas'    => (float) $ekuitas->sum('saldo'),
            'laba_berjalan'    => $lr['laba_bersih'],
        ];
    }

    public function arusKas(string $dari, string $sampai): array
    {
        $kasBanks = KasBank::with('account')->aktif()->get();

        $saldoAwalTotal = 0;
        $saldoAkhirTotal = 0;
        $perKas = [];

        foreach ($kasBanks as $kb) {
            $awal = $this->saldoKasBankSampai($kb, Carbon::parse($dari)->subDay()->toDateString());
            $akhir = $this->saldoKasBankSampai($kb, $sampai);
            $saldoAwalTotal += $awal;
            $saldoAkhirTotal += $akhir;
            $perKas[] = [
                'kas_bank'   => $kb,
                'saldo_awal' => $awal,
                'saldo_akhir'=> $akhir,
                'mutasi'     => $akhir - $awal,
            ];
        }

        $masuk = DB::table('finance_transaksi as t')
            ->join('finance_kategoris as k', 'k.id', '=', 't.kategori_id')
            ->where('t.tipe', 'masuk')->where('t.status', 'posted')
            ->whereBetween('t.tanggal', [$dari, $sampai])
            ->groupBy('k.id', 'k.nama')
            ->orderByDesc(DB::raw('SUM(t.nominal)'))
            ->select('k.nama', DB::raw('SUM(t.nominal) as total'))
            ->get();

        $keluar = DB::table('finance_transaksi as t')
            ->join('finance_kategoris as k', 'k.id', '=', 't.kategori_id')
            ->where('t.tipe', 'keluar')->where('t.status', 'posted')
            ->whereBetween('t.tanggal', [$dari, $sampai])
            ->groupBy('k.id', 'k.nama')
            ->orderByDesc(DB::raw('SUM(t.nominal)'))
            ->select('k.nama', DB::raw('SUM(t.nominal) as total'))
            ->get();

        return [
            'dari'             => $dari,
            'sampai'           => $sampai,
            'per_kas'          => $perKas,
            'saldo_awal_total' => $saldoAwalTotal,
            'saldo_akhir_total'=> $saldoAkhirTotal,
            'masuk_per_kategori'  => $masuk,
            'keluar_per_kategori' => $keluar,
            'total_masuk'      => (float) $masuk->sum('total'),
            'total_keluar'     => (float) $keluar->sum('total'),
            'kas_bersih'       => $saldoAkhirTotal - $saldoAwalTotal,
        ];
    }

    protected function saldoKasBankSampai(KasBank $kb, string $tanggal): float
    {
        $masuk  = (float) DB::table('finance_transaksi')
            ->where('kas_bank_id', $kb->id)->where('tipe', 'masuk')->where('status', 'posted')
            ->where('tanggal', '<=', $tanggal)->sum('nominal');
        $keluar = (float) DB::table('finance_transaksi')
            ->where('kas_bank_id', $kb->id)->where('tipe', 'keluar')->where('status', 'posted')
            ->where('tanggal', '<=', $tanggal)->sum('nominal');
        $trIn   = (float) DB::table('finance_transaksi')
            ->where('kas_bank_tujuan_id', $kb->id)->where('tipe', 'transfer')->where('status', 'posted')
            ->where('tanggal', '<=', $tanggal)->sum('nominal');
        $trOut  = (float) DB::table('finance_transaksi')
            ->where('kas_bank_id', $kb->id)->where('tipe', 'transfer')->where('status', 'posted')
            ->where('tanggal', '<=', $tanggal)->sum('nominal');

        return (float) $kb->saldo_awal + $masuk - $keluar + $trIn - $trOut;
    }
}
