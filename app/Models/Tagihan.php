<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'santri_id', 'jenis_tagihan_id', 'nominal', 'periode', 'status', 'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'nominal'  => 'integer',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function jenisTagihan(): BelongsTo
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }
}
