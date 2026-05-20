<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KunjunganKlinik extends Model
{
    protected $table = 'kunjungan_klinik';

    protected $fillable = [
        'patient_type', 'patient_id', 'tanggal_kunjungan', 'keluhan',
        'diagnosa', 'tindakan_obat', 'status_pengobatan', 'lama_istirahat_hari',
        'pemeriksa_id',
        'perlu_rujuk', 'perlu_dirawat_ortu', 'catatan_ortu', 'notified_at',
        'wali_konfirmasi', 'wali_konfirmasi_at', 'wali_konfirmasi_pesan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan'   => 'datetime',
            'lama_istirahat_hari' => 'integer',
            'perlu_rujuk'         => 'boolean',
            'perlu_dirawat_ortu'  => 'boolean',
            'notified_at'         => 'datetime',
            'wali_konfirmasi_at'  => 'datetime',
        ];
    }

    public function patient(): MorphTo
    {
        return $this->morphTo();
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'pemeriksa_id');
    }

    public function voiceNotes(): HasMany
    {
        return $this->hasMany(VoiceNote::class, 'kunjungan_klinik_id');
    }
}
