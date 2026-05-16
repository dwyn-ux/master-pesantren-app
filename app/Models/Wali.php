<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wali extends Model
{
    protected $table = "wali";

    protected $fillable = ["user_id", "nama", "nama_ar", "no_hp"];

    public function getSaldoAttribute(): int
    {
        if ($this->relationLoaded("santri")) {
            return (int) $this->santri->sum("saldo");
        }

        return (int) $this->santri()->sum("saldo");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function santri(): BelongsToMany
    {
        return $this->belongsToMany(Santri::class, "santri_wali")
            ->withPivot("hubungan")
            ->using(SantriWali::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
