<?php

namespace Database\Seeders;

use App\Models\Finance\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['kode' => '1', 'nama' => 'AKTIVA', 'tipe' => 'asset', 'saldo_normal' => 'debit'],
            ['kode' => '11', 'nama' => 'Aktiva Lancar', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '1'],
            ['kode' => '110', 'nama' => 'Kas & Bank', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '11', 'is_kas_bank' => true],
            ['kode' => '111', 'nama' => 'Kas Tunai', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '110', 'is_kas_bank' => true],
            ['kode' => '112', 'nama' => 'Bank BCA', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '110', 'is_kas_bank' => true],
            ['kode' => '113', 'nama' => 'Bank BSI', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '110', 'is_kas_bank' => true],
            ['kode' => '120', 'nama' => 'Piutang Usaha', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '11'],
            ['kode' => '121', 'nama' => 'Piutang SPP Santri', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '120'],
            ['kode' => '122', 'nama' => 'Piutang Lain-lain', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '120'],
            ['kode' => '130', 'nama' => 'Persediaan', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '11'],
            ['kode' => '131', 'nama' => 'Persediaan Kantin', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '130'],
            ['kode' => '140', 'nama' => 'Uang Muka', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '11'],
            ['kode' => '150', 'nama' => 'Kas Kecil', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '11'],
            
            ['kode' => '12', 'nama' => 'Aktiva Tetap', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '1'],
            ['kode' => '1210', 'nama' => 'Tanah', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '12'],
            ['kode' => '1220', 'nama' => 'Bangunan', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '12'],
            ['kode' => '1230', 'nama' => 'Kendaraan', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '12'],
            ['kode' => '1240', 'nama' => 'Peralatan Kantor', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '12'],
            ['kode' => '1250', 'nama' => 'Perlengkapan', 'tipe' => 'asset', 'saldo_normal' => 'debit', 'parent_kode' => '12'],
            ['kode' => '1290', 'nama' => 'Akumulasi Penyusutan', 'tipe' => 'asset', 'saldo_normal' => 'kredit', 'parent_kode' => '12'],
            
            ['kode' => '2', 'nama' => 'KEWAJIBAN', 'tipe' => 'liability', 'saldo_normal' => 'kredit'],
            ['kode' => '21', 'nama' => 'Kewajiban Lancar', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '2'],
            ['kode' => '210', 'nama' => 'Hutang Usaha', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '21'],
            ['kode' => '211', 'nama' => 'Hutang Vendor', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '210'],
            ['kode' => '220', 'nama' => 'Hutang Gaji', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '21'],
            ['kode' => '230', 'nama' => 'Hutang Pajak', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '21'],
            ['kode' => '231', 'nama' => 'Hutang PPh 21', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '230'],
            ['kode' => '240', 'nama' => 'Pendapatan Diterima Dimuka', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '21'],
            ['kode' => '241', 'nama' => 'Saldo Wallet Santri', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '240'],
            
            ['kode' => '22', 'nama' => 'Kewajiban Jangka Panjang', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '2'],
            ['kode' => '221', 'nama' => 'Hutang Bank', 'tipe' => 'liability', 'saldo_normal' => 'kredit', 'parent_kode' => '22'],
            
            ['kode' => '3', 'nama' => 'EKUITAS', 'tipe' => 'equity', 'saldo_normal' => 'kredit'],
            ['kode' => '31', 'nama' => 'Modal Yayasan', 'tipe' => 'equity', 'saldo_normal' => 'kredit', 'parent_kode' => '3'],
            ['kode' => '32', 'nama' => 'Laba Ditahan', 'tipe' => 'equity', 'saldo_normal' => 'kredit', 'parent_kode' => '3'],
            ['kode' => '33', 'nama' => 'Laba Tahun Berjalan', 'tipe' => 'equity', 'saldo_normal' => 'kredit', 'parent_kode' => '3'],
            
            ['kode' => '4', 'nama' => 'PENDAPATAN', 'tipe' => 'revenue', 'saldo_normal' => 'kredit'],
            ['kode' => '41', 'nama' => 'Pendapatan Pendidikan', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '4'],
            ['kode' => '411', 'nama' => 'Pendapatan SPP', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '41'],
            ['kode' => '412', 'nama' => 'Pendapatan Daftar Ulang', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '41'],
            ['kode' => '413', 'nama' => 'Pendapatan Seragam', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '41'],
            ['kode' => '414', 'nama' => 'Pendapatan Kegiatan', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '41'],
            
            ['kode' => '42', 'nama' => 'Pendapatan Usaha', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '4'],
            ['kode' => '421', 'nama' => 'Pendapatan Kantin', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '42'],
            ['kode' => '422', 'nama' => 'Pendapatan Laundry', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '42'],
            ['kode' => '423', 'nama' => 'Pendapatan Penjualan', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '42'],
            
            ['kode' => '43', 'nama' => 'Pendapatan Lain-lain', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '4'],
            ['kode' => '431', 'nama' => 'Pendapatan Donasi/Infaq', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '43'],
            ['kode' => '432', 'nama' => 'Pendapatan Wakaf', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '43'],
            ['kode' => '433', 'nama' => 'Pendapatan Zakat', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '43'],
            ['kode' => '439', 'nama' => 'Pendapatan Lainnya', 'tipe' => 'revenue', 'saldo_normal' => 'kredit', 'parent_kode' => '43'],
            
            ['kode' => '5', 'nama' => 'BEBAN', 'tipe' => 'expense', 'saldo_normal' => 'debit'],
            ['kode' => '51', 'nama' => 'Beban Gaji & Tunjangan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '5'],
            ['kode' => '511', 'nama' => 'Beban Gaji Ustadz', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '51'],
            ['kode' => '512', 'nama' => 'Beban Gaji Karyawan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '51'],
            ['kode' => '513', 'nama' => 'Beban Tunjangan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '51'],
            ['kode' => '514', 'nama' => 'Beban THR', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '51'],
            
            ['kode' => '52', 'nama' => 'Beban Operasional', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '5'],
            ['kode' => '521', 'nama' => 'Beban Listrik', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '522', 'nama' => 'Beban Air', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '523', 'nama' => 'Beban Telepon & Internet', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '524', 'nama' => 'Beban Konsumsi', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '525', 'nama' => 'Beban ATK', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '526', 'nama' => 'Beban Transport', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '527', 'nama' => 'Beban Maintenance', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            ['kode' => '528', 'nama' => 'Beban Penyusutan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '52'],
            
            ['kode' => '53', 'nama' => 'Beban Pendidikan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '5'],
            ['kode' => '531', 'nama' => 'Beban Kegiatan Santri', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '53'],
            ['kode' => '532', 'nama' => 'Beban Perlombaan', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '53'],
            ['kode' => '533', 'nama' => 'Beban Pelatihan Ustadz', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '53'],
            
            ['kode' => '54', 'nama' => 'Beban HPP', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '5'],
            ['kode' => '541', 'nama' => 'HPP Kantin', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '54'],
            ['kode' => '542', 'nama' => 'HPP Laundry', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '54'],
            
            ['kode' => '59', 'nama' => 'Beban Lain-lain', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '5'],
            ['kode' => '591', 'nama' => 'Beban Administrasi Bank', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '59'],
            ['kode' => '592', 'nama' => 'Beban Pajak', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '59'],
            ['kode' => '599', 'nama' => 'Beban Lainnya', 'tipe' => 'expense', 'saldo_normal' => 'debit', 'parent_kode' => '59'],
        ];

        foreach ($accounts as $acc) {
            $data = [
                'kode'          => $acc['kode'],
                'nama'          => $acc['nama'],
                'tipe'          => $acc['tipe'],
                'saldo_normal'  => $acc['saldo_normal'],
                'is_kas_bank'   => $acc['is_kas_bank'] ?? false,
            ];

            if (!empty($acc['parent_kode'])) {
                $parent = Account::where('kode', $acc['parent_kode'])->first();
                if ($parent) {
                    $data['parent_id'] = $parent->id;
                }
            }

            Account::firstOrCreate(['kode' => $acc['kode']], $data);
        }
    }
}
