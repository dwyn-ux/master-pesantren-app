<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nama', 100);
            $table->date('tanggal_masuk');
            $table->string('kamar', 50)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('fingerprint_id', 100)->nullable()->unique();
            $table->decimal('saldo', 12, 0)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri');
    }
};
