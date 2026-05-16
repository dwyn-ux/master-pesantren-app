<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->enum('semester', ['ganjil', 'genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('tingkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tingkat_id')->constrained('tingkat')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('ustadz')->nullOnDelete();
            $table->integer('kapasitas')->default(30);
            $table->timestamps();

            $table->index('tingkat_id');
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 20)->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('kitab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->string('nama', 200);
            $table->string('pengarang', 200)->nullable();
            $table->integer('total_halaman')->nullable();
            $table->timestamps();

            $table->index('mata_pelajaran_id');
        });

        Schema::create('kelas_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kelas_id', 'santri_id', 'tahun_ajaran_id'], 'kelas_santri_unique');
            $table->index('santri_id');
            $table->index('tahun_ajaran_id');
        });

        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 50)->nullable();
            $table->timestamps();

            $table->index(['kelas_id', 'tahun_ajaran_id']);
            $table->index('ustadz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
        Schema::dropIfExists('kelas_santri');
        Schema::dropIfExists('kitab');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tingkat');
        Schema::dropIfExists('tahun_ajaran');
    }
};
