<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setoran extends Model
{
    protected $table = 'setoran';

    protected $fillable = [
        'santri_id', 'penerima_id', 'tipe_halaqah', 'jenis',
        'surah_awal', 'ayat_awal', 'surah_akhir', 'ayat_akhir',
        'jumlah_halaman', 'status', 'catatan', 'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal'        => 'date',
            'jumlah_halaman' => 'decimal:1',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'penerima_id');
    }

    public function surahAwal(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'surah_awal');
    }

    public function surahAkhir(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'surah_akhir');
    }

    // Total halaman ziyadah yang sudah maqbul
    public function scopeTotalHafalan($query, int $santriId): mixed
    {
        return $query->where('santri_id', $santriId)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->sum('jumlah_halaman');
    }

    // Posisi terakhir ziyadah maqbul untuk auto-fill form setoran baru
    public function scopePosisiTerakhir($query, int $santriId): mixed
    {
        return $query->where('santri_id', $santriId)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->latest('tanggal')
            ->first();
    }
}
