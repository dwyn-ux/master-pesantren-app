<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KantinSyncLog extends Model
{
    protected $table = 'kantin_sync_logs';

    protected $fillable = [
        'kantin_device_id', 'arah', 'jenis', 'jumlah_record',
        'detail', 'status', 'error_message',
    ];

    protected $casts = [
        'detail' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(KantinDevice::class, 'kantin_device_id');
    }
}
