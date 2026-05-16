<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarifLaundrySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->firstOrFail();

        DB::table('tarif_laundry')->insert([
            'harga_per_kg'  => 5000,
            'diubah_oleh'   => $admin->id,
            'berlaku_mulai' => now()->toDateString(),
            'created_at'    => now(),
        ]);
    }
}
