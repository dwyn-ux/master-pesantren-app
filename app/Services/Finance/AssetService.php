<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\Asset;
use App\Models\Finance\AssetDepreciation;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(protected JournalService $journalService) {}

    /**
     * Hitung & post penyusutan bulanan untuk semua aset aktif.
     * Idempoten: tidak akan double untuk periode yang sama.
     */
    public function postDepreciation(int $tahun, int $bulan): array
    {
        $tanggal = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $bebanPenyusutan = Account::where('kode', '528')->first();
        $akumulasiAcc    = Account::where('kode', '1290')->first();

        if (!$bebanPenyusutan || !$akumulasiAcc) {
            throw new \RuntimeException('Akun Beban Penyusutan (528) atau Akumulasi Penyusutan (1290) tidak ada di COA.');
        }

        $assets = Asset::where('status', 'aktif')->get();
        $processed = 0;
        $skipped   = 0;

        foreach ($assets as $asset) {
            $exists = AssetDepreciation::where(['asset_id' => $asset->id, 'tahun' => $tahun, 'bulan' => $bulan])->exists();
            if ($exists) { $skipped++; continue; }

            $nominal = $asset->penyusutanBulanan();
            if ($nominal <= 0) continue;

            $newAkumulasi = (float) $asset->akumulasi_penyusutan + $nominal;
            $newNilaiBuku = (float) $asset->harga_perolehan - $newAkumulasi;
            if ($newNilaiBuku < (float) $asset->nilai_residu) {
                $nominal = (float) $asset->harga_perolehan - $newAkumulasi - $asset->nilai_residu < 0
                    ? max(0, (float) $asset->harga_perolehan - (float) $asset->akumulasi_penyusutan - (float) $asset->nilai_residu)
                    : $nominal;
                $newAkumulasi = (float) $asset->akumulasi_penyusutan + $nominal;
                $newNilaiBuku = (float) $asset->harga_perolehan - $newAkumulasi;
            }

            DB::transaction(function () use ($asset, $tahun, $bulan, $nominal, $newAkumulasi, $newNilaiBuku, $tanggal, $bebanPenyusutan, $akumulasiAcc) {
                $journal = $this->journalService->post([
                    'tanggal'     => $tanggal,
                    'tipe'        => 'auto',
                    'referensi'   => 'DEPR-' . $asset->kode . '-' . sprintf('%04d%02d', $tahun, $bulan),
                    'source_type' => Asset::class,
                    'source_id'   => $asset->id,
                    'keterangan'  => "Penyusutan {$asset->nama} periode " . sprintf('%02d/%d', $bulan, $tahun),
                ], [
                    ['account_id' => $bebanPenyusutan->id, 'debit' => $nominal, 'kredit' => 0, 'keterangan' => "Penyusutan {$asset->nama}"],
                    ['account_id' => $akumulasiAcc->id, 'debit' => 0, 'kredit' => $nominal, 'keterangan' => "Akum. penyusutan {$asset->nama}"],
                ]);

                AssetDepreciation::create([
                    'asset_id'   => $asset->id,
                    'tahun'      => $tahun,
                    'bulan'      => $bulan,
                    'nominal'    => $nominal,
                    'akumulasi'  => $newAkumulasi,
                    'nilai_buku' => $newNilaiBuku,
                    'journal_id' => $journal->id,
                ]);

                $asset->update([
                    'akumulasi_penyusutan' => $newAkumulasi,
                    'nilai_buku'           => $newNilaiBuku,
                ]);
            });

            $processed++;
        }

        return ['processed' => $processed, 'skipped' => $skipped];
    }
}
