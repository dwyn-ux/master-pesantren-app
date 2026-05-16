<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_journals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->string('referensi')->nullable();
            $table->enum('tipe', ['umum', 'kas_masuk', 'kas_keluar', 'transfer', 'penyesuaian', 'pembalik', 'auto'])->default('umum');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_kredit', 18, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index('tanggal');
            $table->index('status');
        });

        Schema::create('finance_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('finance_journals')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('finance_accounts');
            $table->foreignId('kas_bank_id')->nullable()->constrained('finance_kas_banks');
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('account_id');
            $table->index('kas_bank_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_journal_lines');
        Schema::dropIfExists('finance_journals');
    }
};
