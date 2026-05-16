<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->restrictOnDelete();
            $table->string('nama_produk', 100); // snapshot
            $table->decimal('harga', 10, 0); // snapshot
            $table->integer('qty');
            $table->decimal('subtotal', 12, 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
