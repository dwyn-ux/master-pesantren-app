<?php

namespace App\Models\Finance;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UstadzSalary extends Model
{
    protected $table = 'finance_ustadz_salary';

    protected $fillable = [
        'ustadz_id',
        'gaji_pokok', 'tunjangan_jabatan', 'tunjangan_transport',
        'tunjangan_makan', 'tunjangan_lain', 'potongan_tetap',
        'berlaku_sejak', 'is_active',
    ];

    protected $casts = [
        'berlaku_sejak'      => 'date',
        'is_active'          => 'boolean',
        'gaji_pokok'         => 'decimal:2',
        'tunjangan_jabatan'  => 'decimal:2',
        'tunjangan_transport'=> 'decimal:2',
        'tunjangan_makan'    => 'decimal:2',
        'tunjangan_lain'     => 'decimal:2',
        'potongan_tetap'     => 'decimal:2',
    ];

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function totalGross(): float
    {
        return (float) $this->gaji_pokok
             + (float) $this->tunjangan_jabatan
             + (float) $this->tunjangan_transport
             + (float) $this->tunjangan_makan
             + (float) $this->tunjangan_lain;
    }
}
