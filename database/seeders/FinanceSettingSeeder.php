<?php

namespace Database\Seeders;

use App\Models\Finance\Setting;
use Illuminate\Database\Seeder;

class FinanceSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Identitas Yayasan (untuk PDF kop laporan)
            ['key' => 'org.nama',     'value' => 'Yayasan Pesantren', 'group' => 'organisasi', 'label' => 'Nama Yayasan',  'type' => 'string'],
            ['key' => 'org.alamat',   'value' => '',                  'group' => 'organisasi', 'label' => 'Alamat',        'type' => 'string'],
            ['key' => 'org.telepon',  'value' => '',                  'group' => 'organisasi', 'label' => 'Telepon',       'type' => 'string'],
            ['key' => 'org.email',    'value' => '',                  'group' => 'organisasi', 'label' => 'Email',         'type' => 'string'],
            ['key' => 'org.npwp',     'value' => '',                  'group' => 'organisasi', 'label' => 'NPWP',          'type' => 'string'],

            // Toggle Fitur Opsional
            ['key' => 'feature.pajak',       'value' => '0', 'group' => 'fitur', 'label' => 'Aktifkan Pajak (PPh 21)', 'type' => 'boolean', 'description' => 'Hitung otomatis PPh 21 di payroll & laporan pajak bulanan.'],
            ['key' => 'feature.approval',    'value' => '0', 'group' => 'fitur', 'label' => 'Aktifkan Approval Multi-level', 'type' => 'boolean', 'description' => 'Pengeluaran besar wajib disetujui kepala pondok / yayasan.'],
            ['key' => 'feature.budgeting',   'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Budgeting', 'type' => 'boolean', 'description' => 'Buat & tracking anggaran per kategori.'],
            ['key' => 'feature.petty_cash',  'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Petty Cash (Kas Kecil)', 'type' => 'boolean'],
            ['key' => 'feature.aset',        'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Modul Aset Tetap', 'type' => 'boolean'],
            ['key' => 'feature.payable',     'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Hutang Vendor', 'type' => 'boolean'],
            ['key' => 'feature.payroll',     'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Payroll Ustadz', 'type' => 'boolean'],
            ['key' => 'feature.audit_log',   'value' => '1', 'group' => 'fitur', 'label' => 'Aktifkan Audit Trail', 'type' => 'boolean'],
            ['key' => 'feature.bank_recon',  'value' => '0', 'group' => 'fitur', 'label' => 'Aktifkan Bank Reconciliation', 'type' => 'boolean'],

            // Konfigurasi Approval
            ['key' => 'approval.threshold_kepala_pondok', 'value' => '5000000', 'group' => 'approval', 'label' => 'Threshold Approval Kepala Pondok (Rp)', 'type' => 'decimal', 'description' => 'Pengeluaran ≥ nominal ini wajib disetujui Kepala Pondok.'],
            ['key' => 'approval.threshold_yayasan',       'value' => '50000000', 'group' => 'approval', 'label' => 'Threshold Approval Yayasan (Rp)', 'type' => 'decimal'],

            // Konfigurasi Pajak
            ['key' => 'pajak.ptkp_setahun',      'value' => '54000000', 'group' => 'pajak', 'label' => 'PTKP Setahun (TK/0)', 'type' => 'decimal'],
            ['key' => 'pajak.lapis1_max',        'value' => '60000000', 'group' => 'pajak', 'label' => 'Lapis 1 Max', 'type' => 'decimal'],
            ['key' => 'pajak.lapis1_persen',     'value' => '5',        'group' => 'pajak', 'label' => 'Lapis 1 (%)', 'type' => 'decimal'],
            ['key' => 'pajak.lapis2_max',        'value' => '250000000','group' => 'pajak', 'label' => 'Lapis 2 Max', 'type' => 'decimal'],
            ['key' => 'pajak.lapis2_persen',     'value' => '15',       'group' => 'pajak', 'label' => 'Lapis 2 (%)', 'type' => 'decimal'],

            // Reminder
            ['key' => 'reminder.hutang_h_minus', 'value' => '7', 'group' => 'reminder', 'label' => 'Reminder Hutang H- (hari)', 'type' => 'integer'],
            ['key' => 'reminder.budget_warning', 'value' => '80', 'group' => 'reminder', 'label' => 'Alert Budget (% terpakai)', 'type' => 'integer'],
        ];

        foreach ($defaults as $d) {
            \App\Models\Finance\Setting::firstOrCreate(['key' => $d['key']], $d);
        }
    }
}
