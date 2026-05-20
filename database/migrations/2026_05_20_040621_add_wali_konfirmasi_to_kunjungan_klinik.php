<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            // Status konfirmasi wali: null = belum, 'otw' = sedang perjalanan, 'selesai' = sudah ditangani
            $table->string('wali_konfirmasi')->nullable()->after('notified_at');
            $table->timestamp('wali_konfirmasi_at')->nullable()->after('wali_konfirmasi');
            $table->text('wali_konfirmasi_pesan')->nullable()->after('wali_konfirmasi_at');
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->dropColumn(['wali_konfirmasi', 'wali_konfirmasi_at', 'wali_konfirmasi_pesan']);
        });
    }
};
