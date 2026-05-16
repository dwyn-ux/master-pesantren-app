<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->string('kelas', 10)->nullable()->after('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('kelas');
        });
    }

    public function down(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jenis_kelamin']);
        });
    }
};
