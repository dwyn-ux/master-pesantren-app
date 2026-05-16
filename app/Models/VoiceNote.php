<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceNote extends Model
{
    protected $table = 'voice_notes';

    // Immutable — hanya created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'pengirim_santri_id', 'pengirim_wali_id', 'pengirim_ustadz_id',
        'penerima_santri_id', 'penerima_wali_id',
        'audio_url', 'durasi_detik', 'biaya', 'is_read',
        'konteks', 'kunjungan_klinik_id',
    ];

    protected function casts(): array
    {
        return [
            'biaya'      => 'integer',
            'is_read'    => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function pengirimSantri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'pengirim_santri_id');
    }

    public function pengirimWali(): BelongsTo
    {
        return $this->belongsTo(Wali::class, 'pengirim_wali_id');
    }

    public function pengirimUstadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'pengirim_ustadz_id');
    }

    public function penerimaSantri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'penerima_santri_id');
    }

    public function penerimaWali(): BelongsTo
    {
        return $this->belongsTo(Wali::class, 'penerima_wali_id');
    }

    public function kunjunganKlinik(): BelongsTo
    {
        return $this->belongsTo(KunjunganKlinik::class, 'kunjungan_klinik_id');
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('is_read', false);
    }

    public function getPengirimNamaAttribute(): string
    {
        if ($this->pengirim_ustadz_id) {
            return $this->pengirimUstadz?->nama ?? 'Ustadz';
        }
        if ($this->pengirim_wali_id) {
            return $this->pengirimWali?->nama ?? 'Wali';
        }
        if ($this->pengirim_santri_id) {
            return $this->pengirimSantri?->nama ?? 'Santri';
        }
        return '-';
    }
}
