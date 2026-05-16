<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    protected $table = 'wallet_transactions';

    // Hanya created_at — record immutable
    const UPDATED_AT = null;

    protected $fillable = [
        'santri_id', 'tipe', 'referensi_id', 'referensi_tipe',
        'nominal', 'jenis', 'saldo_sebelum', 'saldo_sesudah', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal'        => 'integer',
            'saldo_sebelum'  => 'integer',
            'saldo_sesudah'  => 'integer',
            'created_at'     => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
