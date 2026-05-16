<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    protected $table = 'outlets';

    public $timestamps = false;

    protected $fillable = ['user_id', 'nama', 'tipe', 'is_aktif'];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    public function transaksiKasir(): HasMany
    {
        return $this->hasMany(TransaksiKasir::class);
    }

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }
}
