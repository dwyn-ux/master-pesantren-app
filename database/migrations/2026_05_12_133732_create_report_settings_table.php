<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->json('value');
            $table->timestamps();
        });

        // Default: kirim laporan mingguan setiap hari Minggu (0=Min, 1=Sen, .. 6=Sab)
        // dan jam 08:00 server time
        \Illuminate\Support\Facades\DB::table('report_settings')->insert([
            [
                'key' => 'weekly_report',
                'value' => json_encode([
                    'enabled' => true,
                    'day_of_week' => 0,
                    'hour' => 8,
                    'minute' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'monthly_report',
                'value' => json_encode([
                    'enabled' => true,
                    'day_of_month' => 1,
                    'hour' => 8,
                    'minute' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('report_settings');
    }
};
