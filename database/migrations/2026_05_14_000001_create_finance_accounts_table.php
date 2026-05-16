<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('saldo_normal', ['debit', 'kredit']);
            $table->foreignId('parent_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->boolean('is_kas_bank')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('tipe');
            $table->index('is_kas_bank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_accounts');
    }
};
