<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_laundry', function (Blueprint $table) {
            $table->id();
            $table->decimal('harga_per_kg', 10, 0);
            $table->foreignId('diubah_oleh')->constrained('users')->restrictOnDelete();
            $table->date('berlaku_mulai');
            $table->timestamp('created_at')->useCurrent(); // immutable — no updated_at

            $table->index('berlaku_mulai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_laundry');
    }
};
