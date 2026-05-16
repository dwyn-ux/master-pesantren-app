<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_wali', function (Blueprint $table) {
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('wali_id')->constrained('wali')->cascadeOnDelete();
            $table->enum('hubungan', ['ayah', 'ibu', 'wali']);

            $table->primary(['santri_id', 'wali_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_wali');
    }
};
