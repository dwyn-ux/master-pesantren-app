<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifLaundry extends Model
{
    protected $table = 'tarif_laundry';

    // Immutable — hanya created_at
    const UPDATED_AT = null;

    protected $fillable = ['harga_per_kg', 'diubah_oleh', 'berlaku_mulai'];

    protected function casts(): array
    {
        return [
            'harga_per_kg'  => 'integer',
            'berlaku_mulai' => 'date',
            'created_at'    => 'datetime',
        ];
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    // Tarif yang sedang berlaku
    public static function aktif(): static
    {
        return static::orderByDesc('berlaku_mulai')->firstOrFail();
    }
}
