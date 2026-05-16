<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 20)->default('string'); // string|boolean|integer|decimal|json
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_settings');
    }
};
