<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKasirItem extends Model
{
    protected $table = 'transaksi_kasir_items';

    public $timestamps = false;

    protected $fillable = [
        'transaksi_kasir_id', 'produk_id',
        'nama_produk', 'harga_satuan', 'qty', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'integer',
            'subtotal'     => 'integer',
        ];
    }

    public function transaksiKasir(): BelongsTo
    {
        return $this->belongsTo(TransaksiKasir::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
