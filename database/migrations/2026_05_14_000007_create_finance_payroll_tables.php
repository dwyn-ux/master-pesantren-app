<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_payroll_components', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['tunjangan', 'potongan']);
            $table->enum('hitungan', ['tetap', 'persentase', 'manual'])->default('tetap');
            $table->decimal('default_nominal', 18, 2)->default(0);
            $table->decimal('default_persen', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('finance_ustadz_salary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ustadz_id')->constrained('ustadz')->cascadeOnDelete();
            $table->decimal('gaji_pokok', 18, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 18, 2)->default(0);
            $table->decimal('tunjangan_transport', 18, 2)->default(0);
            $table->decimal('tunjangan_makan', 18, 2)->default(0);
            $table->decimal('tunjangan_lain', 18, 2)->default(0);
            $table->decimal('potongan_tetap', 18, 2)->default(0);
            $table->date('berlaku_sejak')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('finance_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('total_gross', 18, 2)->default(0);
            $table->decimal('total_potongan', 18, 2)->default(0);
            $table->decimal('total_net', 18, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid', 'void'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('finance_journals')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
        });

        Schema::create('finance_payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('finance_payroll_runs')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadz');
            $table->decimal('gaji_pokok', 18, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 18, 2)->default(0);
            $table->decimal('tunjangan_transport', 18, 2)->default(0);
            $table->decimal('tunjangan_makan', 18, 2)->default(0);
            $table->decimal('tunjangan_lain', 18, 2)->default(0);
            $table->decimal('bonus', 18, 2)->default(0);
            $table->decimal('potongan_absensi', 18, 2)->default(0);
            $table->decimal('potongan_pph21', 18, 2)->default(0);
            $table->decimal('potongan_lain', 18, 2)->default(0);
            $table->integer('hari_hadir')->default(0);
            $table->integer('hari_kerja')->default(0);
            $table->decimal('gross', 18, 2)->default(0);
            $table->decimal('net', 18, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('tanggal_bayar')->nullable();
            $table->foreignId('kas_bank_id')->nullable()->constrained('finance_kas_banks');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_payroll_slips');
        Schema::dropIfExists('finance_payroll_runs');
        Schema::dropIfExists('finance_ustadz_salary');
        Schema::dropIfExists('finance_payroll_components');
    }
};
