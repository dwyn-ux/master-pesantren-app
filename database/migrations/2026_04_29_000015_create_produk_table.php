<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('barcode', 50)->nullable()->unique();
            $table->decimal('harga', 10, 0);
            $table->integer('stok')->default(0);
            $table->string('foto', 255)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['outlet_id', 'is_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
