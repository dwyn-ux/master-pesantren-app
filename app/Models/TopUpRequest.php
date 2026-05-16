<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopUpRequest extends Model
{
    protected $table = 'top_up_requests';

    protected $fillable = [
        'santri_id', 'wali_id', 'nominal', 'status',
        'tripay_ref', 'tripay_channel', 'payment_url', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function wali(): BelongsTo
    {
        return $this->belongsTo(Wali::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    // Alias: sidebar-bendahara reads ::pending()->count() — keep it working
    public function scopePending($query)
    {
        return $query->where('status', 'unpaid');
    }
}
