<?php

namespace Database\Seeders;

use App\Models\Finance\Account;
use App\Models\Finance\Kategori;
use Illuminate\Database\Seeder;

class KategoriKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['kode' => 'PMS-SPP', 'nama' => 'Pembayaran SPP', 'tipe' => 'pemasukan', 'account_kode' => '411'],
            ['kode' => 'PMS-DU', 'nama' => 'Daftar Ulang', 'tipe' => 'pemasukan', 'account_kode' => '412'],
            ['kode' => 'PMS-SRG', 'nama' => 'Seragam', 'tipe' => 'pemasukan', 'account_kode' => '413'],
            ['kode' => 'PMS-KGT', 'nama' => 'Kegiatan', 'tipe' => 'pemasukan', 'account_kode' => '414'],
            ['kode' => 'PMS-KNT', 'nama' => 'Pendapatan Kantin', 'tipe' => 'pemasukan', 'account_kode' => '421'],
            ['kode' => 'PMS-LND', 'nama' => 'Pendapatan Laundry', 'tipe' => 'pemasukan', 'account_kode' => '422'],
            ['kode' => 'PMS-INF', 'nama' => 'Donasi / Infaq', 'tipe' => 'pemasukan', 'account_kode' => '431'],
            ['kode' => 'PMS-WKF', 'nama' => 'Wakaf', 'tipe' => 'pemasukan', 'account_kode' => '432'],
            ['kode' => 'PMS-ZKT', 'nama' => 'Zakat', 'tipe' => 'pemasukan', 'account_kode' => '433'],
            ['kode' => 'PMS-LIN', 'nama' => 'Pemasukan Lainnya', 'tipe' => 'pemasukan', 'account_kode' => '439'],

            ['kode' => 'PNG-GAJI', 'nama' => 'Gaji Ustadz & Karyawan', 'tipe' => 'pengeluaran', 'account_kode' => '511'],
            ['kode' => 'PNG-TNJ', 'nama' => 'Tunjangan', 'tipe' => 'pengeluaran', 'account_kode' => '513'],
            ['kode' => 'PNG-THR', 'nama' => 'THR', 'tipe' => 'pengeluaran', 'account_kode' => '514'],
            ['kode' => 'PNG-LST', 'nama' => 'Listrik', 'tipe' => 'pengeluaran', 'account_kode' => '521'],
            ['kode' => 'PNG-AIR', 'nama' => 'Air', 'tipe' => 'pengeluaran', 'account_kode' => '522'],
            ['kode' => 'PNG-TLP', 'nama' => 'Telepon & Internet', 'tipe' => 'pengeluaran', 'account_kode' => '523'],
            ['kode' => 'PNG-KSM', 'nama' => 'Konsumsi', 'tipe' => 'pengeluaran', 'account_kode' => '524'],
            ['kode' => 'PNG-ATK', 'nama' => 'ATK', 'tipe' => 'pengeluaran', 'account_kode' => '525'],
            ['kode' => 'PNG-TRN', 'nama' => 'Transport', 'tipe' => 'pengeluaran', 'account_kode' => '526'],
            ['kode' => 'PNG-MNT', 'nama' => 'Maintenance', 'tipe' => 'pengeluaran', 'account_kode' => '527'],
            ['kode' => 'PNG-KGT', 'nama' => 'Kegiatan Santri', 'tipe' => 'pengeluaran', 'account_kode' => '531'],
            ['kode' => 'PNG-HPP', 'nama' => 'HPP Kantin', 'tipe' => 'pengeluaran', 'account_kode' => '541'],
            ['kode' => 'PNG-ADM', 'nama' => 'Administrasi Bank', 'tipe' => 'pengeluaran', 'account_kode' => '591'],
            ['kode' => 'PNG-LIN', 'nama' => 'Pengeluaran Lainnya', 'tipe' => 'pengeluaran', 'account_kode' => '599'],
        ];

        foreach ($categories as $cat) {
            $account = Account::where('kode', $cat['account_kode'])->first();
            Kategori::firstOrCreate(
                ['kode' => $cat['kode']],
                [
                    'nama'       => $cat['nama'],
                    'tipe'       => $cat['tipe'],
                    'account_id' => $account?->id,
                ]
            );
        }
    }
}
