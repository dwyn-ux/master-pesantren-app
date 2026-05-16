<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('jenis_tagihan_id')->constrained('jenis_tagihan')->restrictOnDelete();
            $table->decimal('nominal', 12, 0);
            $table->string('periode', 7); // format: 2026-04
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->date('due_date');
            $table->timestamps();

            $table->index(['santri_id', 'periode']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
