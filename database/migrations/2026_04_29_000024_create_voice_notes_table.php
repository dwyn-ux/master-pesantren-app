<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_santri_id')->nullable()->constrained('santri')->nullOnDelete();
            $table->foreignId('pengirim_wali_id')->nullable()->constrained('wali')->nullOnDelete();
            $table->foreignId('penerima_santri_id')->nullable()->constrained('santri')->nullOnDelete();
            $table->foreignId('penerima_wali_id')->nullable()->constrained('wali')->nullOnDelete();
            $table->string('audio_url', 255);
            $table->integer('durasi_detik');
            $table->decimal('biaya', 10, 0);
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent(); // immutable — no updated_at

            $table->index(['penerima_santri_id', 'is_read']);
            $table->index(['penerima_wali_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_notes');
    }
};
