<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->restrictOnDelete();
            $table->enum('tipe', ['topup', 'kantin', 'laundry', 'voice_note', 'marketplace', 'refund']);
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('referensi_tipe', 50)->nullable();
            $table->decimal('nominal', 12, 0);
            $table->enum('jenis', ['kredit', 'debit']);
            $table->decimal('saldo_sebelum', 12, 0);
            $table->decimal('saldo_sesudah', 12, 0);
            $table->string('keterangan', 255)->nullable();
            $table->timestamp('created_at')->useCurrent(); // immutable — no updated_at

            $table->index(['santri_id', 'created_at']);
            $table->index(['referensi_tipe', 'referensi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
