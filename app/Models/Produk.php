<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'outlet_id', 'nama', 'barcode', 'harga', 'stok', 'foto', 'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga'    => 'integer',
            'stok'     => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function transaksiKasirItems(): HasMany
    {
        return $this->hasMany(TransaksiKasirItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
