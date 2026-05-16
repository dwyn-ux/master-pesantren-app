<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('finance_petty_cash', function (Blueprint $table) {
            if (!Schema::hasColumn('finance_petty_cash', 'nama')) {
                $table->string('nama')->nullable()->after('penanggung_jawab');
            }
        });
    }

    public function down(): void
    {
        Schema::table('finance_petty_cash', function (Blueprint $table) {
            if (Schema::hasColumn('finance_petty_cash', 'nama')) {
                $table->dropColumn('nama');
            }
        });
    }
};
