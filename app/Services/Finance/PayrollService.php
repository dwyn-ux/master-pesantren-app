<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\PayrollRun;
use App\Models\Finance\PayrollSlip;
use App\Models\Finance\UstadzSalary;
use App\Models\Ustadz;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayrollService
{
    public function __construct(
        protected JournalService $journalService,
        protected Pph21Service $pphService,
    ) {}

    /**
     * Generate payroll run (slip) untuk semua ustadz aktif yang punya UstadzSalary.
     */
    public function generate(int $tahun, int $bulan): PayrollRun
    {
        if (PayrollRun::where(['tahun' => $tahun, 'bulan' => $bulan])->exists()) {
            throw new InvalidArgumentException("Payroll periode {$tahun}-{$bulan} sudah ada.");
        }

        return DB::transaction(function () use ($tahun, $bulan) {
            $run = PayrollRun::create([
                'nomor'      => PayrollRun::generateNomor($tahun, $bulan),
                'tahun'      => $tahun,
                'bulan'      => $bulan,
                'status'     => 'draft',
                'created_by' => auth()->id(),
            ]);

            $salaries = UstadzSalary::with('ustadz')
                ->where('is_active', true)
                ->whereHas('ustadz')
                ->get();

            $totalGross = 0;
            $totalPotongan = 0;

            foreach ($salaries as $salary) {
                $gross = $salary->totalGross();

                $pph = $this->pphService->hitungBulanan($gross);
                $potonganPph = (float) $pph['pph_sebulan'];

                $potongan = (float) $salary->potongan_tetap + $potonganPph;
                $net = $gross - $potongan;

                PayrollSlip::create([
                    'payroll_run_id'      => $run->id,
                    'ustadz_id'           => $salary->ustadz_id,
                    'gaji_pokok'          => $salary->gaji_pokok,
                    'tunjangan_jabatan'   => $salary->tunjangan_jabatan,
                    'tunjangan_transport' => $salary->tunjangan_transport,
                    'tunjangan_makan'     => $salary->tunjangan_makan,
                    'tunjangan_lain'      => $salary->tunjangan_lain,
                    'potongan_pph21'      => round($potonganPph),
                    'potongan_lain'       => $salary->potongan_tetap,
                    'gross'               => $gross,
                    'net'                 => $net,
                    'is_paid'             => false,
                ]);

                $totalGross    += $gross;
                $totalPotongan += $potongan;
            }

            $run->update([
                'total_gross'    => $totalGross,
                'total_potongan' => $totalPotongan,
                'total_net'      => $totalGross - $totalPotongan,
            ]);

            return $run->fresh(['slips.ustadz']);
        });
    }

    public function approve(PayrollRun $run): PayrollRun
    {
        if ($run->status !== 'draft') {
            throw new InvalidArgumentException('Hanya payroll draft yang bisa di-approve.');
        }

        $run->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $run;
    }

    /**
     * Posting payroll & generate jurnal: Debit Beban Gaji, Kredit Kas/Bank.
     */
    public function pay(PayrollRun $run, int $kasBankId, ?string $tanggalBayar = null): PayrollRun
    {
        if (!in_array($run->status, ['approved', 'draft'])) {
            throw new InvalidArgumentException('Status payroll tidak valid untuk dibayar.');
        }

        $kasBank = KasBank::with('account')->findOrFail($kasBankId);
        $tanggal = $tanggalBayar ?? now()->toDateString();

        return DB::transaction(function () use ($run, $kasBank, $tanggal) {
            $bebanGaji = Account::where('kode', '511')->first();
            if (!$bebanGaji) {
                throw new InvalidArgumentException('Akun Beban Gaji Ustadz (511) tidak ditemukan di COA.');
            }

            $journal = $this->journalService->post([
                'tanggal'     => $tanggal,
                'tipe'        => 'auto',
                'referensi'   => $run->nomor,
                'source_type' => PayrollRun::class,
                'source_id'   => $run->id,
                'keterangan'  => "Pembayaran gaji ustadz periode " . sprintf('%02d/%d', $run->bulan, $run->tahun),
            ], [
                ['account_id' => $bebanGaji->id, 'debit' => $run->total_net, 'kredit' => 0, 'keterangan' => 'Beban Gaji'],
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => 0, 'kredit' => $run->total_net, 'keterangan' => 'Pembayaran via ' . $kasBank->nama],
            ]);

            $run->slips()->update([
                'is_paid'       => true,
                'tanggal_bayar' => $tanggal,
                'kas_bank_id'   => $kasBank->id,
            ]);

            $run->update([
                'status'        => 'paid',
                'tanggal_bayar' => $tanggal,
                'journal_id'    => $journal->id,
            ]);

            $kasBank->recalcSaldo();

            return $run->fresh(['slips', 'journal.lines']);
        });
    }

    public function void(PayrollRun $run, ?string $alasan = null): PayrollRun
    {
        return DB::transaction(function () use ($run, $alasan) {
            if ($run->journal) {
                $this->journalService->reverse($run->journal);
            }
            $run->update([
                'status'     => 'void',
                'keterangan' => trim(($run->keterangan ?? '') . " | VOID: " . ($alasan ?? '-')),
            ]);
            return $run;
        });
    }
}
