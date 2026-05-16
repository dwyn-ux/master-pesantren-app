<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->enum('tipe', ['wali', 'santri']);
            $table->integer('total_baris');
            $table->integer('berhasil');
            $table->integer('gagal');
            $table->string('file_asli', 255);
            $table->timestamp('created_at')->useCurrent(); // immutable — no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
