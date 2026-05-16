<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->enum('tipe', ['kantin', 'laundry']);
            $table->boolean('is_aktif')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
