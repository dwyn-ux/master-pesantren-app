<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'wali_id', 'tujuan_santri_id', 'total',
        'opsi_terima', 'status', 'tripay_ref',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
        ];
    }

    public function wali(): BelongsTo
    {
        return $this->belongsTo(Wali::class);
    }

    public function tujuanSantri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'tujuan_santri_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
