<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan')->nullable();
            $table->foreignId('kategori_id')->constrained('finance_kategoris');
            $table->decimal('nominal_anggaran', 18, 2);
            $table->decimal('nominal_realisasi', 18, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
        });

        Schema::create('finance_petty_cash', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->string('penanggung_jawab');
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_id')->constrained('finance_petty_cash')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe', ['pengisian', 'pengeluaran']);
            $table->foreignId('kategori_id')->nullable()->constrained('finance_kategoris');
            $table->decimal('nominal', 18, 2);
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('finance_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type');
            $table->unsignedBigInteger('approvable_id');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->integer('level')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
        });

        Schema::create('finance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_audit_logs');
        Schema::dropIfExists('finance_approvals');
        Schema::dropIfExists('finance_petty_cash_transactions');
        Schema::dropIfExists('finance_petty_cash');
        Schema::dropIfExists('finance_budgets');
    }
};
