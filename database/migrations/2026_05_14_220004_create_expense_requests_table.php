<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_expense_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->string('judul');
            $table->text('deskripsi');
            $table->decimal('nominal', 18, 2);
            $table->foreignId('kategori_id')->nullable()->constrained('finance_kategoris');
            $table->foreignId('kas_bank_id')->nullable()->constrained('finance_kas_banks');
            $table->foreignId('vendor_id')->nullable()->constrained('finance_vendors');
            $table->string('bukti')->nullable();
            $table->enum('status', ['draft', 'pending_kepala', 'pending_yayasan', 'approved', 'rejected', 'paid', 'void'])->default('draft');
            $table->integer('current_level')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_kepala')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_kepala_at')->nullable();
            $table->foreignId('approved_by_yayasan')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_yayasan_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->foreignId('transaksi_id')->nullable()->constrained('finance_transaksi')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_expense_requests');
    }
};
