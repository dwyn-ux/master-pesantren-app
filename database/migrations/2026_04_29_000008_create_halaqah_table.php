<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halaqah', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->foreignId('ustadz_id')->constrained('ustadz')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halaqah');
    }
};
