<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('nama', 50);
            $table->integer('bobot')->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['mata_pelajaran_id', 'tahun_ajaran_id']);
        });

        Schema::create('kkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tingkat_id')->constrained('tingkat')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->integer('nilai_kkm')->default(70);
            $table->timestamps();

            $table->unique(['mata_pelajaran_id', 'tingkat_id', 'tahun_ajaran_id'], 'kkm_unique');
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['santri_id', 'mata_pelajaran_id', 'komponen_nilai_id', 'tahun_ajaran_id'], 'nilai_unique');
            $table->index('santri_id');
        });

        Schema::create('nilai_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->enum('predikat', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'mata_pelajaran_id', 'tahun_ajaran_id'], 'nilai_akhir_unique');
            $table->index('santri_id');
        });

        Schema::create('nilai_sikap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('aspek', 100);
            $table->enum('predikat', ['A', 'B', 'C', 'D'])->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tahun_ajaran_id']);
        });

        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'tahun_ajaran_id'], 'catatan_wali_kelas_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
        Schema::dropIfExists('nilai_sikap');
        Schema::dropIfExists('nilai_akhir');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('kkm');
        Schema::dropIfExists('komponen_nilai');
    }
};
