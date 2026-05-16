<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kunjungan_klinik', function (Blueprint $table) {
            $table->id();
            $table->morphs('patient'); // patient_type, patient_id
            $table->datetime('tanggal_kunjungan');
            $table->text('keluhan');
            $table->string('diagnosa');
            $table->text('tindakan_obat');
            $table->enum('status_pengobatan', ['aktifitas_normal', 'istirahat', 'dirujuk', 'dirawat_orang_tua']);
            $table->integer('lama_istirahat_hari')->nullable();
            $table->foreignId('pemeriksa_id')->constrained('ustadz')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_klinik');
    }
};
