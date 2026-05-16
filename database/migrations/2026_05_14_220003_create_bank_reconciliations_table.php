<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_bank_id')->constrained('finance_kas_banks')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('saldo_buku', 18, 2);
            $table->decimal('saldo_rekening_koran', 18, 2);
            $table->decimal('selisih', 18, 2);
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_bank_reconciliations');
    }
};
