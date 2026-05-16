<?php

namespace App\Models;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KantinDevice extends Model
{
    protected $table = 'kantin_devices';

    protected $fillable = [
        'outlet_id', 'nama', 'device_code', 'token_hash',
        'last_ip', 'last_seen_at', 'last_sync_at', 'is_active',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active'    => 'boolean',
    ];

    protected $hidden = ['token_hash'];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(KantinSyncLog::class);
    }

    public function offlineTransaksi(): HasMany
    {
        return $this->hasMany(KantinOfflineTransaksi::class);
    }

    /**
     * Generate token baru untuk device. Return plain token (hanya sekali).
     */
    public static function generateToken(): array
    {
        $plain = Str::random(60);
        return [
            'plain' => $plain,
            'hash'  => hash('sha256', $plain),
        ];
    }

    /**
     * Find device by plain token (verify hash).
     */
    public static function findByToken(string $plainToken): ?self
    {
        return static::where('token_hash', hash('sha256', $plainToken))
            ->where('is_active', true)
            ->first();
    }

    public function touchLastSeen(?string $ip = null): void
    {
        $this->update([
            'last_seen_at' => now(),
            'last_ip'      => $ip,
        ]);
    }
}
