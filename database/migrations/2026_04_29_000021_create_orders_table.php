<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wali_id')->constrained('wali')->restrictOnDelete();
            $table->foreignId('tujuan_santri_id')->nullable()->constrained('santri')->nullOnDelete();
            $table->decimal('total', 12, 0);
            $table->enum('opsi_terima', ['kirim_ke_santri', 'ambil_sendiri']);
            $table->enum('status', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');
            $table->string('tripay_ref', 100)->nullable()->unique();
            $table->timestamps();

            $table->index(['wali_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
