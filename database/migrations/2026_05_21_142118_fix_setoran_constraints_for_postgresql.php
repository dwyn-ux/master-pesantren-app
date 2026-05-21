<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // Drop constraint lama dan buat ulang dengan nilai yang benar

            // 1. tipe_halaqah: tambah 'halaqah' ke allowed values
            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_tipe_halaqah_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_tipe_halaqah_check CHECK (tipe_halaqah IN ('utama', 'umum', 'halaqah'))");

            // 2. jenis: tambah 'murojaah' ke allowed values
            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_jenis_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_jenis_check CHECK (jenis IN ('ziyadah', 'muraja_ah', 'murojaah'))");

            // 3. status: tambah nilai baru yang dipakai form
            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_status_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_status_check CHECK (status IN ('maqbul', 'perbaikan', 'ulang', 'lancar', 'perlu_latihan', 'banyak_salah', 'dhaif', 'kurang'))");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_tipe_halaqah_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_tipe_halaqah_check CHECK (tipe_halaqah IN ('utama', 'umum'))");

            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_jenis_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_jenis_check CHECK (jenis IN ('ziyadah', 'muraja_ah'))");

            DB::statement("ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_status_check");
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_status_check CHECK (status IN ('maqbul', 'perbaikan', 'ulang', 'lancar', 'perlu_latihan', 'banyak_salah'))");
        }
    }
};
