<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_kepulangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi');
            $table->date('tanggal_pulang');
            $table->date('tanggal_kembali');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_kepulangan');
    }
};
