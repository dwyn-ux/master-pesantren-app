<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Driver MySQL pakai enum literal, jadi harus ALTER
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // Tambahkan 'murojaah' ke enum dulu (sementara keduanya valid)
            DB::statement("ALTER TABLE setoran MODIFY COLUMN jenis ENUM('ziyadah','muraja_ah','murojaah') NOT NULL");
            // Migrasi data lama
            DB::table('setoran')->where('jenis', 'muraja_ah')->update(['jenis' => 'murojaah']);
            // Hapus varian lama dari enum
            DB::statement("ALTER TABLE setoran MODIFY COLUMN jenis ENUM('ziyadah','murojaah') NOT NULL");
        } else {
            // SQLite / Pgsql: tidak pakai enum ketat, cukup update data lama
            DB::table('setoran')->where('jenis', 'muraja_ah')->update(['jenis' => 'murojaah']);
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE setoran MODIFY COLUMN jenis ENUM('ziyadah','muraja_ah','murojaah') NOT NULL");
            DB::table('setoran')->where('jenis', 'murojaah')->update(['jenis' => 'muraja_ah']);
            DB::statement("ALTER TABLE setoran MODIFY COLUMN jenis ENUM('ziyadah','muraja_ah') NOT NULL");
        } else {
            DB::table('setoran')->where('jenis', 'murojaah')->update(['jenis' => 'muraja_ah']);
        }
    }
};
