<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->restrictOnDelete();
            $table->foreignId('outlet_id')->constrained('outlets')->restrictOnDelete();
            $table->string('nomor_tiket', 30)->unique(); // format: LDR-20260429-001
            $table->decimal('berat_kg', 4, 2);
            $table->decimal('harga_per_kg', 10, 0); // snapshot saat transaksi
            $table->decimal('total', 12, 0);
            $table->enum('status', ['diterima', 'dicuci', 'selesai', 'diambil'])->default('diterima');
            $table->boolean('fingerprint_verified')->default(false);
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_antar');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->dateTime('tanggal_diambil')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
