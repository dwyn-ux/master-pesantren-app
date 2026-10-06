<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE setoran MODIFY COLUMN tipe_halaqah ENUM('utama','umum','halaqah') NOT NULL");
            DB::statement("ALTER TABLE setoran MODIFY COLUMN jenis ENUM('ziyadah','murojaah') NOT NULL");
            DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('maqbul','perbaikan','ulang','lancar','perlu_latihan','banyak_salah','dhaif','kurang') NOT NULL");

            return;
        }

        if ($driver === 'pgsql') {
            DB::table('setoran')->where('jenis', 'muraja_ah')->update(['jenis' => 'murojaah']);
            DB::statement('ALTER TABLE setoran DROP CONSTRAINT IF EXISTS setoran_jenis_check');
            DB::statement("ALTER TABLE setoran ADD CONSTRAINT setoran_jenis_check CHECK (jenis IN ('ziyadah', 'murojaah'))");

            return;
        }

        if ($driver !== 'sqlite') {
            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            Schema::create('setoran_rebuilt', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->restrictOnDelete();
                $table->foreignId('penerima_id')->constrained('ustadz')->restrictOnDelete();
                $table->string('tipe_halaqah', 20);
                $table->string('jenis', 20);
                $table->unsignedTinyInteger('surah_awal');
                $table->unsignedSmallInteger('ayat_awal');
                $table->unsignedTinyInteger('surah_akhir');
                $table->unsignedSmallInteger('ayat_akhir');
                $table->decimal('jumlah_halaman', 4, 1);
                $table->string('status', 30);
                $table->text('catatan')->nullable();
                $table->date('tanggal');
                $table->timestamps();

                $table->foreign('surah_awal')->references('id')->on('surah')->restrictOnDelete();
                $table->foreign('surah_akhir')->references('id')->on('surah')->restrictOnDelete();
                $table->index(['santri_id', 'jenis', 'tanggal']);
                $table->index(['santri_id', 'tanggal']);
            });

            DB::statement(<<<'SQL'
                INSERT INTO setoran_rebuilt (
                    id, santri_id, penerima_id, tipe_halaqah, jenis,
                    surah_awal, ayat_awal, surah_akhir, ayat_akhir,
                    jumlah_halaman, status, catatan, tanggal, created_at, updated_at
                )
                SELECT
                    id, santri_id, penerima_id, tipe_halaqah,
                    CASE WHEN jenis = 'muraja_ah' THEN 'murojaah' ELSE jenis END,
                    surah_awal, ayat_awal, surah_akhir, ayat_akhir,
                    jumlah_halaman, status, catatan, tanggal, created_at, updated_at
                FROM setoran
            SQL);

            Schema::drop('setoran');
            Schema::rename('setoran_rebuilt', 'setoran');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // The widened values are intentionally retained to avoid destroying valid data.
    }
};
