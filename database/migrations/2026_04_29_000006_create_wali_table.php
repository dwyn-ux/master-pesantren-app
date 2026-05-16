<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('no_hp', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali');
    }
};
