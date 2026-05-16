<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->restrictOnDelete();
            $table->foreignId('wali_id')->constrained('wali')->restrictOnDelete();
            $table->decimal('nominal', 12, 0);
            $table->enum('metode', ['va_bca', 'va_mandiri', 'qris', 'gopay', 'ovo', 'manual']);
            $table->string('tripay_ref', 100)->nullable()->unique();
            $table->string('tripay_channel', 50)->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
