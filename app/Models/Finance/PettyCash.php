<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PettyCash extends Model
{
    protected $table = 'finance_petty_cash';

    protected $fillable = [
        'nomor', 'tanggal', 'penanggung_jawab', 'nama',
        'saldo_awal', 'saldo_berjalan', 'is_active', 'keterangan',
    ];

    protected $casts = [
        'tanggal'        => 'date',
        'is_active'      => 'boolean',
        'saldo_awal'     => 'decimal:2',
        'saldo_berjalan' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PettyCashTransaction::class, 'petty_cash_id');
    }
}
