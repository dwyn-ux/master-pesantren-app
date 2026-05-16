<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->foreignId('account_id')->constrained('finance_accounts');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_kategoris');
    }
};
