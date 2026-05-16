<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('nominal', 12, 0)->default(0);
            $table->boolean('is_nominal_tetap');
            $table->boolean('is_aktif')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_tagihan');
    }
};
