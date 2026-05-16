<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreignId('tagihan_id')->nullable()->change();
            if (!Schema::hasColumn('pembayaran', 'tagihan_ids')) {
                $table->json('tagihan_ids')->nullable()->after('tagihan_id');
            }
            if (!Schema::hasColumn('pembayaran', 'topup_items')) {
                $table->json('topup_items')->nullable()->after('tagihan_ids');
            }
            if (!Schema::hasColumn('pembayaran', 'snap_token')) {
                $table->string('snap_token', 255)->nullable()->after('tripay_channel');
            }
            if (!Schema::hasColumn('pembayaran', 'payment_url')) {
                $table->string('payment_url', 500)->nullable()->after('snap_token');
            }
        });

        // PostgreSQL tidak mendukung MODIFY COLUMN ENUM secara langsung via Laravel/DB::statement MySQL style.
        // Cara paling aman di Laravel adalah membiarkan kolom tersebut string atau me-recreate-nya jika perlu.
        // Namun untuk fix cepat, kita pastikan kolom tersebut bisa menerima 'midtrans_snap'.
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE pembayaran ALTER COLUMN metode TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE pembayaran DROP CONSTRAINT IF EXISTS pembayaran_metode_check");
        } else {
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN metode ENUM('va_bca','va_mandiri','qris','gopay','ovo','manual','midtrans_snap')");
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['tagihan_ids', 'topup_items', 'snap_token', 'payment_url']);
        });

        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN metode ENUM('va_bca','va_mandiri','qris','gopay','ovo','manual')");
        }

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreignId('tagihan_id')->nullable(false)->change();
        });
    }
};
