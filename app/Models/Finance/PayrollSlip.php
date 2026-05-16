<?php

namespace App\Models\Finance;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollSlip extends Model
{
    protected $table = 'finance_payroll_slips';

    protected $fillable = [
        'payroll_run_id', 'ustadz_id',
        'gaji_pokok', 'tunjangan_jabatan', 'tunjangan_transport', 'tunjangan_makan', 'tunjangan_lain',
        'bonus', 'potongan_absensi', 'potongan_pph21', 'potongan_lain',
        'hari_hadir', 'hari_kerja', 'gross', 'net', 'catatan',
        'is_paid', 'tanggal_bayar', 'kas_bank_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'is_paid'       => 'boolean',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function kasBank(): BelongsTo
    {
        return $this->belongsTo(KasBank::class, 'kas_bank_id');
    }
}
