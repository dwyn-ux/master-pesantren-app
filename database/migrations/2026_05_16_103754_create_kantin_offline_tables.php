<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantin_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('device_code', 50)->unique()->comment('kode unik device, misal KASIR-01');
            $table->string('token_hash', 64)->unique()->comment('hash dari API token');
            $table->string('last_ip', 45)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['outlet_id', 'is_active']);
        });

        Schema::create('kantin_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kantin_device_id')->constrained('kantin_devices')->cascadeOnDelete();
            $table->enum('arah', ['pull', 'push']);
            $table->enum('jenis', ['santri', 'produk', 'tarif', 'transaksi', 'full']);
            $table->integer('jumlah_record')->default(0);
            $table->json('detail')->nullable();
            $table->enum('status', ['success', 'partial', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['kantin_device_id', 'created_at']);
        });

        Schema::create('kantin_offline_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kantin_device_id')->constrained('kantin_devices')->cascadeOnDelete();
            $table->string('client_uuid', 36)->unique()->comment('UUID generated di device, untuk idempotency');
            $table->foreignId('transaksi_kasir_id')->nullable()->constrained('transaksi_kasir')->nullOnDelete()
                ->comment('id transaksi setelah berhasil di-sync ke transaksi_kasir');
            $table->foreignId('santri_id')->nullable()->constrained('santri')->nullOnDelete();
            $table->decimal('total', 12, 0);
            $table->json('items')->comment('snapshot item: produk_id, nama, qty, harga');
            $table->enum('metode_bayar', ['saldo', 'tunai'])->default('saldo');
            $table->timestamp('transaksi_at')->comment('waktu transaksi sebenarnya di kasir');
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['sync_status', 'created_at']);
            $table->index('santri_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantin_offline_transaksi');
        Schema::dropIfExists('kantin_sync_logs');
        Schema::dropIfExists('kantin_devices');
    }
};
