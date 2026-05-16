<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar', 'transfer']);
            $table->foreignId('kategori_id')->nullable()->constrained('finance_kategoris');
            $table->foreignId('kas_bank_id')->constrained('finance_kas_banks');
            $table->foreignId('kas_bank_tujuan_id')->nullable()->constrained('finance_kas_banks');
            $table->decimal('nominal', 18, 2);
            $table->string('pihak')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transaksi');
    }
};
