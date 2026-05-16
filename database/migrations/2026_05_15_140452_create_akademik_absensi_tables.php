<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_pelajaran_id', 'santri_id', 'tanggal'], 'absensi_pelajaran_unique');
            $table->index('santri_id');
            $table->index('tanggal');
        });

        Schema::create('jurnal_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('materi');
            $table->string('halaman_kitab', 50)->nullable();
            $table->text('pr')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['jadwal_pelajaran_id', 'tanggal']);
            $table->index('ustadz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajar');
        Schema::dropIfExists('absensi_pelajaran');
    }
};
