<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryOrder extends Model
{
    protected $table = 'laundry_orders';

    protected $fillable = [
        'santri_id', 'outlet_id', 'nomor_tiket', 'berat_kg', 'harga_per_kg',
        'total', 'status', 'fingerprint_verified', 'catatan',
        'tanggal_antar', 'tanggal_selesai', 'tanggal_diambil',
    ];

    protected function casts(): array
    {
        return [
            'berat_kg'             => 'decimal:2',
            'harga_per_kg'         => 'integer',
            'total'                => 'integer',
            'fingerprint_verified' => 'boolean',
            'tanggal_antar'        => 'datetime',
            'tanggal_selesai'      => 'datetime',
            'tanggal_diambil'      => 'datetime',
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

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', ['diambil']);
    }
}
