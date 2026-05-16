<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id', 'tagihan_ids', 'topup_items', 'wali_id', 'nominal', 'metode',
        'tripay_ref', 'tripay_channel', 'snap_token', 'payment_url', 'status', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at'     => 'datetime',
            'nominal'     => 'integer',
            'tagihan_ids' => 'array',
            'topup_items' => 'array',
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function wali(): BelongsTo
    {
        return $this->belongsTo(Wali::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
