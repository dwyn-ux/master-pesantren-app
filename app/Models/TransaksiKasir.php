<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiKasir extends Model
{
    protected $table = 'transaksi_kasir';

    // Immutable — hanya created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'santri_id', 'outlet_id', 'total', 'fingerprint_verified',
    ];

    protected function casts(): array
    {
        return [
            'total'                => 'integer',
            'fingerprint_verified' => 'boolean',
            'created_at'           => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiKasirItem::class);
    }
}
