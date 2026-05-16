<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KantinOfflineTransaksi extends Model
{
    protected $table = 'kantin_offline_transaksi';

    protected $fillable = [
        'kantin_device_id', 'client_uuid', 'transaksi_kasir_id', 'santri_id',
        'total', 'items', 'metode_bayar', 'transaksi_at',
        'sync_status', 'sync_error', 'synced_at',
    ];

    protected $casts = [
        'items'         => 'array',
        'transaksi_at'  => 'datetime',
        'synced_at'     => 'datetime',
        'total'         => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(KantinDevice::class, 'kantin_device_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiKasir::class, 'transaksi_kasir_id');
    }
}
