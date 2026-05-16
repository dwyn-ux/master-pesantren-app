<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->date('tanggal_perolehan');
            $table->decimal('harga_perolehan', 18, 2);
            $table->decimal('nilai_residu', 18, 2)->default(0);
            $table->integer('umur_ekonomis_bulan')->default(60);
            $table->enum('metode_penyusutan', ['garis_lurus', 'saldo_menurun'])->default('garis_lurus');
            $table->decimal('akumulasi_penyusutan', 18, 2)->default(0);
            $table->decimal('nilai_buku', 18, 2)->default(0);
            $table->enum('status', ['aktif', 'rusak', 'dijual', 'dihapus'])->default('aktif');
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('account_id')->nullable()->constrained('finance_accounts');
            $table->timestamps();
        });

        Schema::create('finance_asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('finance_assets')->cascadeOnDelete();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->decimal('nominal', 18, 2);
            $table->decimal('akumulasi', 18, 2);
            $table->decimal('nilai_buku', 18, 2);
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->timestamps();

            $table->unique(['asset_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_asset_depreciations');
        Schema::dropIfExists('finance_assets');
    }
};
