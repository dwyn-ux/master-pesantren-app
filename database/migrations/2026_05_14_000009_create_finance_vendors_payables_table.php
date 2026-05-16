<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->string('kontak_person')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('npwp')->nullable();
            $table->string('bank_nama')->nullable();
            $table->string('bank_rekening')->nullable();
            $table->string('bank_atas_nama')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('finance_payables', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->date('jatuh_tempo');
            $table->foreignId('vendor_id')->constrained('finance_vendors');
            $table->decimal('nominal', 18, 2);
            $table->decimal('terbayar', 18, 2)->default(0);
            $table->decimal('sisa', 18, 2);
            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas', 'void'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('finance_payable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payable_id')->constrained('finance_payables')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('nominal', 18, 2);
            $table->foreignId('kas_bank_id')->constrained('finance_kas_banks');
            $table->text('keterangan')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_payable_payments');
        Schema::dropIfExists('finance_payables');
        Schema::dropIfExists('finance_vendors');
    }
};
