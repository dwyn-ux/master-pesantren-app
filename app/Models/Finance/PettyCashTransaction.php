<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashTransaction extends Model
{
    protected $table = 'finance_petty_cash_transactions';

    protected $fillable = [
        'petty_cash_id', 'tanggal', 'tipe', 'kategori_id',
        'nominal', 'keterangan', 'bukti', 'journal_id', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function pettyCash(): BelongsTo
    {
        return $this->belongsTo(PettyCash::class, 'petty_cash_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
