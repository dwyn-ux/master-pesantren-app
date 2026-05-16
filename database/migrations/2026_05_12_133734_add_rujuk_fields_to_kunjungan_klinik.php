<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->boolean('perlu_rujuk')->default(false)->after('lama_istirahat_hari');
            $table->boolean('perlu_dirawat_ortu')->default(false)->after('perlu_rujuk');
            $table->text('catatan_ortu')->nullable()->after('perlu_dirawat_ortu');
            $table->timestamp('notified_at')->nullable()->after('catatan_ortu');
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->dropColumn(['perlu_rujuk', 'perlu_dirawat_ortu', 'catatan_ortu', 'notified_at']);
        });
    }
};
