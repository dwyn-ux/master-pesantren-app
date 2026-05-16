<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'finance_periode';

    protected $fillable = [
        'tahun', 'bulan', 'status', 'closed_at', 'closed_by',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public static function isOpen(int $tahun, int $bulan): bool
    {
        $p = static::where(['tahun' => $tahun, 'bulan' => $bulan])->first();
        return !$p || $p->status === 'open';
    }
}
