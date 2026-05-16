<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_tagihan', function (Blueprint $table) {
            $table->enum('kelompok', ['bulanan', 'semesteran', 'tahunan', 'kegiatan', 'lainnya'])
                  ->default('lainnya')
                  ->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_tagihan', function (Blueprint $table) {
            $table->dropColumn('kelompok');
        });
    }
};
