<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\Journal;
use App\Models\Finance\JournalLine;
use App\Models\Finance\Periode;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class JournalService
{
    /**
     * Posting jurnal double-entry.
     *
     * @param  array{tanggal: string, tipe?: string, keterangan?: string, referensi?: string, source_type?: string, source_id?: int}  $header
     * @param  array<int, array{account_id?: int, kode?: string, kas_bank_id?: int, debit?: float, kredit?: float, keterangan?: string}>  $lines
     */
    public function post(array $header, array $lines): Journal
    {
        if (count($lines) < 2) {
            throw new InvalidArgumentException('Jurnal minimal 2 baris (debit & kredit).');
        }

        $tanggal = \Carbon\Carbon::parse($header['tanggal']);
        if (!Periode::isOpen($tanggal->year, $tanggal->month)) {
            throw new InvalidArgumentException("Periode {$tanggal->format('m/Y')} sudah ditutup.");
        }

        $totalDebit  = 0;
        $totalKredit = 0;
        $resolved    = [];

        foreach ($lines as $line) {
            $accountId = $line['account_id'] ?? null;
            if (!$accountId && !empty($line['kode'])) {
                $accountId = Account::where('kode', $line['kode'])->value('id');
            }
            if (!$accountId) {
                throw new InvalidArgumentException('Account tidak ditemukan untuk salah satu baris jurnal.');
            }

            $debit  = (float) ($line['debit'] ?? 0);
            $kredit = (float) ($line['kredit'] ?? 0);

            if ($debit > 0 && $kredit > 0) {
                throw new InvalidArgumentException('Satu baris hanya boleh debit ATAU kredit.');
            }
            if ($debit <= 0 && $kredit <= 0) {
                throw new InvalidArgumentException('Nominal debit/kredit wajib > 0.');
            }

            $totalDebit  += $debit;
            $totalKredit += $kredit;

            $resolved[] = [
                'account_id'  => $accountId,
                'kas_bank_id' => $line['kas_bank_id'] ?? null,
                'debit'       => $debit,
                'kredit'      => $kredit,
                'keterangan'  => $line['keterangan'] ?? null,
            ];
        }

        if (abs($totalDebit - $totalKredit) > 0.01) {
            throw new InvalidArgumentException(
                "Jurnal tidak balance: debit Rp " . number_format($totalDebit, 2) . " vs kredit Rp " . number_format($totalKredit, 2)
            );
        }

        return DB::transaction(function () use ($header, $resolved, $totalDebit, $totalKredit) {
            $tipe    = $header['tipe'] ?? 'umum';
            $journal = Journal::create([
                'nomor'        => Journal::generateNomor(match ($tipe) {
                    'kas_masuk'  => 'JKM',
                    'kas_keluar' => 'JKK',
                    'transfer'   => 'JT',
                    'penyesuaian'=> 'JP',
                    'pembalik'   => 'JB',
                    'auto'       => 'JA',
                    default      => 'JU',
                }),
                'tanggal'      => $header['tanggal'],
                'referensi'    => $header['referensi'] ?? null,
                'tipe'         => $tipe,
                'source_type'  => $header['source_type'] ?? null,
                'source_id'    => $header['source_id'] ?? null,
                'keterangan'   => $header['keterangan'] ?? null,
                'total_debit'  => $totalDebit,
                'total_kredit' => $totalKredit,
                'status'       => 'posted',
                'created_by'   => auth()->id(),
                'posted_by'    => auth()->id(),
                'posted_at'    => now(),
            ]);

            foreach ($resolved as $line) {
                JournalLine::create(array_merge($line, ['journal_id' => $journal->id]));
            }

            return $journal->load('lines');
        });
    }

    public function void(Journal $journal, ?string $alasan = null): Journal
    {
        return DB::transaction(function () use ($journal, $alasan) {
            $journal->update([
                'status'     => 'void',
                'keterangan' => trim(($journal->keterangan ?? '') . " | VOID: " . ($alasan ?? '-')),
            ]);
            return $journal;
        });
    }

    public function reverse(Journal $journal, ?string $tanggal = null): Journal
    {
        $reversedLines = $journal->lines->map(fn ($l) => [
            'account_id'  => $l->account_id,
            'kas_bank_id' => $l->kas_bank_id,
            'debit'       => (float) $l->kredit,
            'kredit'      => (float) $l->debit,
            'keterangan'  => 'Reversal: ' . ($l->keterangan ?? ''),
        ])->all();

        return $this->post([
            'tanggal'    => $tanggal ?? now()->toDateString(),
            'tipe'       => 'pembalik',
            'referensi'  => $journal->nomor,
            'keterangan' => 'Pembalik untuk ' . $journal->nomor,
        ], $reversedLines);
    }
}
