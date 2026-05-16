<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->string('nama_ar', 200)->nullable()->after('nama');
        });

        Schema::table('wali', function (Blueprint $table) {
            $table->string('nama_ar', 200)->nullable()->after('nama');
        });

        Schema::table('ustadz', function (Blueprint $table) {
            $table->string('nama_ar', 200)->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->dropColumn('nama_ar');
        });
        Schema::table('wali', function (Blueprint $table) {
            $table->dropColumn('nama_ar');
        });
        Schema::table('ustadz', function (Blueprint $table) {
            $table->dropColumn('nama_ar');
        });
    }
};
