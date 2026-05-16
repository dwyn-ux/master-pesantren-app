<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voice_notes', function (Blueprint $table) {
            $table->foreignId('pengirim_ustadz_id')
                ->nullable()
                ->after('pengirim_wali_id')
                ->constrained('ustadz')
                ->nullOnDelete();
            $table->string('konteks', 30)->nullable()->after('is_read')->comment('umum|kesehatan|akademik');
            $table->foreignId('kunjungan_klinik_id')
                ->nullable()
                ->after('konteks')
                ->constrained('kunjungan_klinik')
                ->nullOnDelete();

            $table->index(['penerima_wali_id', 'konteks']);
        });
    }

    public function down(): void
    {
        Schema::table('voice_notes', function (Blueprint $table) {
            $table->dropForeign(['pengirim_ustadz_id']);
            $table->dropForeign(['kunjungan_klinik_id']);
            $table->dropIndex(['penerima_wali_id', 'konteks']);
            $table->dropColumn(['pengirim_ustadz_id', 'konteks', 'kunjungan_klinik_id']);
        });
    }
};
