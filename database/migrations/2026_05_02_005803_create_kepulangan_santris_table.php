<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepulangan_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_kepulangan_id')->constrained('sesi_kepulangan')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->enum('status_administrasi', ['lunas', 'belum_lunas', 'acc_bendahara'])->default('belum_lunas');
            $table->text('keterangan_bendahara')->nullable();
            $table->date('tanggal_janji_bayar')->nullable(); // Sesuai surat kesanggupan
            $table->datetime('waktu_keluar')->nullable();
            $table->datetime('waktu_kembali')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepulangan_santri');
    }
};
