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
        Schema::table('santri', function (Blueprint $table) {
            $table->string('nik', 20)->unique()->nullable()->after('nis');
            $table->text('alamat')->nullable()->after('foto');
            $table->date('tanggal_lahir')->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->dropColumn(['nik', 'alamat', 'tanggal_lahir']);
        });
    }
};
