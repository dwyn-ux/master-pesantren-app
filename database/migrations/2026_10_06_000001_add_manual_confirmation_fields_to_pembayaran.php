<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('manual_type', 20)->nullable()->after('metode');
            $table->string('proof_path')->nullable()->after('payment_url');
            $table->string('proof_original_name')->nullable()->after('proof_path');
            $table->timestamp('submitted_at')->nullable()->after('proof_original_name');
            $table->foreignId('confirmed_by')->nullable()->after('submitted_at')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            $table->text('manual_note')->nullable()->after('confirmed_at');
            $table->text('rejection_note')->nullable()->after('manual_note');

            $table->index(['metode', 'manual_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropIndex(['metode', 'manual_type', 'status']);
            $table->dropColumn([
                'manual_type',
                'proof_path',
                'proof_original_name',
                'submitted_at',
                'confirmed_by',
                'confirmed_at',
                'manual_note',
                'rejection_note',
            ]);
        });
    }
};
