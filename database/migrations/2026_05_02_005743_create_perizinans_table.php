<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->datetime('tanggal_mulai');
            $table->datetime('tanggal_selesai');
            $table->datetime('waktu_keluar_aktual')->nullable();
            $table->datetime('waktu_kembali_aktual')->nullable();
            $table->text('alasan');
            $table->enum('status', ['pending', 'disetujui_ustadz', 'disetujui_kesantrian', 'ditolak', 'sedang_keluar', 'selesai', 'terlambat'])->default('pending');
            $table->string('diajukan_oleh')->default('wali'); // wali atau admin/kesantrian
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};
