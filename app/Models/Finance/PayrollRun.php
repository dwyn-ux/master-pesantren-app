<?php

namespace App\Models\Finance;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $table = 'finance_payroll_runs';

    protected $fillable = [
        'nomor', 'tahun', 'bulan', 'tanggal_bayar',
        'total_gross', 'total_potongan', 'total_net',
        'status', 'keterangan',
        'created_by', 'approved_by', 'approved_at', 'journal_id',
    ];

    protected $casts = [
        'tanggal_bayar'  => 'date',
        'approved_at'    => 'datetime',
        'total_gross'    => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'total_net'      => 'decimal:2',
    ];

    public function slips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'payroll_run_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public static function generateNomor(int $tahun, int $bulan): string
    {
        return 'PR/' . $tahun . '/' . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
    }
}
