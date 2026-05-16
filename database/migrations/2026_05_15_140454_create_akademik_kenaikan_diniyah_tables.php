<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('kelas_asal_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('kelas_tujuan_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->enum('keputusan', ['naik', 'tidak_naik', 'naik_bersyarat'])->default('naik');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'tahun_ajaran_id'], 'kenaikan_kelas_unique');
            $table->index('santri_id');
        });

        Schema::create('halaqah_diniyah', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->foreignId('kitab_id')->constrained('kitab')->cascadeOnDelete();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('ustadz_id');
            $table->index('is_active');
        });

        Schema::create('halaqah_diniyah_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('halaqah_diniyah_id')->constrained('halaqah_diniyah')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->date('tanggal_bergabung');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['halaqah_diniyah_id', 'santri_id'], 'halaqah_diniyah_santri_unique');
            $table->index('santri_id');
        });

        Schema::create('setoran_diniyah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('halaqah_diniyah_id')->constrained('halaqah_diniyah')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('halaman_mulai', 20);
            $table->string('halaman_selesai', 20);
            $table->enum('jenis', ['bandongan', 'sorogan'])->default('bandongan');
            $table->enum('nilai', ['mumtaz', 'jayyid_jiddan', 'jayyid', 'maqbul'])->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tanggal']);
            $table->index('halaqah_diniyah_id');
        });

        Schema::create('khataman_kitab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('kitab_id')->constrained('kitab')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->date('tanggal_khatam');
            $table->string('nomor_sertifikat', 50)->unique()->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('santri_id');
            $table->index('tanggal_khatam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khataman_kitab');
        Schema::dropIfExists('setoran_diniyah');
        Schema::dropIfExists('halaqah_diniyah_santri');
        Schema::dropIfExists('halaqah_diniyah');
        Schema::dropIfExists('kenaikan_kelas');
    }
};
