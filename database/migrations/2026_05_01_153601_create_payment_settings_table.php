<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('active_gateway', ['tripay', 'midtrans', 'xendit'])->default('tripay');
            $table->string('tripay_api_key')->nullable();
            $table->string('tripay_private_key')->nullable();
            $table->string('tripay_merchant_code')->nullable();
            $table->enum('tripay_mode', ['sandbox', 'production'])->default('sandbox');
            
            $table->string('midtrans_client_key')->nullable();
            $table->string('midtrans_server_key')->nullable();
            $table->boolean('midtrans_is_production')->default(false);
            
            $table->string('xendit_secret_key')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
