<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->restrictOnDelete();
            $table->foreignId('penerima_id')->constrained('ustadz')->restrictOnDelete();
            $table->enum('tipe_halaqah', ['utama', 'umum']);
            $table->enum('jenis', ['ziyadah', 'muraja_ah']);
            $table->unsignedTinyInteger('surah_awal');
            $table->unsignedSmallInteger('ayat_awal');
            $table->unsignedTinyInteger('surah_akhir');
            $table->unsignedSmallInteger('ayat_akhir');
            $table->decimal('jumlah_halaman', 4, 1);
            $table->enum('status', ['maqbul', 'perbaikan', 'ulang', 'lancar', 'perlu_latihan', 'banyak_salah']);
            $table->text('catatan')->nullable();
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('surah_awal')->references('id')->on('surah')->restrictOnDelete();
            $table->foreign('surah_akhir')->references('id')->on('surah')->restrictOnDelete();

            $table->index(['santri_id', 'jenis', 'tanggal']);
            $table->index(['santri_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran');
    }
};
