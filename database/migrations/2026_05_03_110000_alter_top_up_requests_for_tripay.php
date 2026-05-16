<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isSqlite = DB::getDriverName() === 'sqlite';

        if (! $isSqlite) {
            try {
                Schema::table('top_up_requests', function (Blueprint $table) {
                    $table->dropForeign(['approved_by']);
                });
            } catch (\Exception $e) {
                // FK might not exist
            }

            DB::statement("ALTER TABLE top_up_requests MODIFY COLUMN status ENUM('unpaid','paid','expired','failed') NOT NULL DEFAULT 'unpaid'");
        }

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        Schema::table('top_up_requests', function (Blueprint $table) {
            if (Schema::hasColumn('top_up_requests', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('top_up_requests', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('top_up_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            $table->string('tripay_ref')->nullable()->after('nominal');
            $table->string('tripay_channel')->nullable()->after('tripay_ref');
            $table->text('payment_url')->nullable()->after('tripay_channel');
            $table->timestamp('paid_at')->nullable()->after('payment_url');
        });

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        Schema::table('top_up_requests', function (Blueprint $table) {
            $table->dropColumn(['tripay_ref', 'tripay_channel', 'payment_url', 'paid_at']);

            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        DB::statement("ALTER TABLE top_up_requests MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
