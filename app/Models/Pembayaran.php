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
        'manual_type', 'proof_path', 'proof_original_name', 'submitted_at',
        'confirmed_by', 'confirmed_at', 'manual_note', 'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'paid_at'     => 'datetime',
            'nominal'     => 'integer',
            'tagihan_ids' => 'array',
            'topup_items' => 'array',
            'submitted_at' => 'datetime',
            'confirmed_at' => 'datetime',
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

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function isManualTransfer(): bool
    {
        return $this->metode === 'manual' && $this->manual_type === 'transfer';
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
