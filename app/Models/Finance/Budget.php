<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $table = 'finance_budgets';

    protected $fillable = [
        'tahun', 'bulan', 'kategori_id',
        'nominal_anggaran', 'nominal_realisasi', 'catatan', 'created_by',
    ];

    protected $casts = [
        'nominal_anggaran'  => 'decimal:2',
        'nominal_realisasi' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function persenRealisasi(): float
    {
        if ((float) $this->nominal_anggaran <= 0) return 0;
        return ((float) $this->nominal_realisasi / (float) $this->nominal_anggaran) * 100;
    }
}
