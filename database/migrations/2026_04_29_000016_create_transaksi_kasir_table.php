<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kasir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->restrictOnDelete();
            $table->foreignId('outlet_id')->constrained('outlets')->restrictOnDelete();
            $table->decimal('total', 12, 0);
            $table->boolean('fingerprint_verified')->default(false);
            $table->timestamp('created_at')->useCurrent(); // immutable — no updated_at

            $table->index(['santri_id', 'created_at']);
            $table->index(['outlet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kasir');
    }
};
