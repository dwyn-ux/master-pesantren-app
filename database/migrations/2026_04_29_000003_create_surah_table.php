<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surah', function (Blueprint $table) {
            $table->tinyIncrements('id'); // 1–114
            $table->string('nama', 50);
            $table->string('nama_latin', 50);
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->unsignedTinyInteger('juz_awal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surah');
    }
};
