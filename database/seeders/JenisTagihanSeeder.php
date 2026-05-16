<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisTagihanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'            => 'SPP',
                'nominal'         => 150000,
                'is_nominal_tetap' => true,
                'is_aktif'        => true,
            ],
            [
                'nama'            => 'Infak',
                'nominal'         => 50000,
                'is_nominal_tetap' => true,
                'is_aktif'        => true,
            ],
            [
                'nama'            => 'Daftar Ulang',
                'nominal'         => 300000,
                'is_nominal_tetap' => true,
                'is_aktif'        => true,
            ],
            [
                'nama'            => 'Uang Saku',
                'nominal'         => 0,
                'is_nominal_tetap' => false, // wali isi sendiri
                'is_aktif'        => true,
            ],
            [
                'nama'            => 'Donasi',
                'nominal'         => 0,
                'is_nominal_tetap' => false, // wali isi sendiri
                'is_aktif'        => true,
            ],
        ];

        DB::table('jenis_tagihan')->insert($data);
    }
}
