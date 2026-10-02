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

        if ($isSqlite) {
            $this->rebuildSqliteTable();

            return;
        }

        try {
            Schema::table('top_up_requests', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
            });
        } catch (\Exception $e) {
            // FK might not exist
        }

        DB::statement("ALTER TABLE top_up_requests MODIFY COLUMN status ENUM('unpaid','paid','expired','failed') NOT NULL DEFAULT 'unpaid'");

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

    private function rebuildSqliteTable(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::dropIfExists('top_up_requests_new');

        Schema::create('top_up_requests_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('wali_id')->constrained('wali')->cascadeOnDelete();
            $table->unsignedInteger('nominal');
            $table->string('status')->default('unpaid');
            $table->string('tripay_ref')->nullable();
            $table->string('tripay_channel')->nullable();
            $table->text('payment_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO top_up_requests_new (
                id, santri_id, wali_id, nominal, status, created_at, updated_at
            )
            SELECT
                id,
                santri_id,
                wali_id,
                nominal,
                CASE
                    WHEN status = 'approved' THEN 'paid'
                    WHEN status = 'rejected' THEN 'failed'
                    ELSE 'unpaid'
                END,
                created_at,
                updated_at
            FROM top_up_requests
        ");

        Schema::drop('top_up_requests');
        Schema::rename('top_up_requests_new', 'top_up_requests');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
