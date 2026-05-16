<?php

namespace Database\Seeders;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use Illuminate\Database\Seeder;

class KasBankSeeder extends Seeder
{
    public function run(): void
    {
        $kasAccount = Account::where('kode', '111')->first();
        $bcaAccount = Account::where('kode', '112')->first();
        $bsiAccount = Account::where('kode', '113')->first();

        $data = [
            [
                'kode'            => 'KAS001',
                'nama'            => 'Kas Tunai Bendahara',
                'tipe'            => 'kas',
                'account_id'      => $kasAccount?->id,
                'saldo_awal'      => 0,
                'saldo_berjalan'  => 0,
                'keterangan'      => 'Kas tunai utama bendahara pesantren',
            ],
            [
                'kode'            => 'BNK001',
                'nama'            => 'Rekening BCA',
                'tipe'            => 'bank',
                'nama_bank'       => 'Bank BCA',
                'no_rekening'     => '1234567890',
                'atas_nama'       => 'Yayasan Pesantren',
                'account_id'      => $bcaAccount?->id,
                'saldo_awal'      => 0,
                'saldo_berjalan'  => 0,
                'keterangan'      => 'Rekening operasional utama',
            ],
            [
                'kode'            => 'BNK002',
                'nama'            => 'Rekening BSI',
                'tipe'            => 'bank',
                'nama_bank'       => 'Bank Syariah Indonesia',
                'no_rekening'     => '0987654321',
                'atas_nama'       => 'Yayasan Pesantren',
                'account_id'      => $bsiAccount?->id,
                'saldo_awal'      => 0,
                'saldo_berjalan'  => 0,
                'keterangan'      => 'Rekening syariah untuk donasi/zakat',
            ],
        ];

        foreach ($data as $item) {
            KasBank::firstOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
