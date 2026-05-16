<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_kas_banks', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['kas', 'bank']);
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->foreignId('account_id')->constrained('finance_accounts');
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_kas_banks');
    }
};
