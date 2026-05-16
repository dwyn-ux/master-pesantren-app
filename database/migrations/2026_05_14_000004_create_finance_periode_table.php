<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_periode', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_periode');
    }
};
