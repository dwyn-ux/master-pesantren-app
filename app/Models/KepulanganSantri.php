<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KepulanganSantri extends Model
{
    protected $table = 'kepulangan_santri';

    protected $fillable = [
        'sesi_kepulangan_id', 'santri_id', 'status_administrasi',
        'keterangan_bendahara', 'tanggal_janji_bayar',
        'waktu_keluar', 'waktu_kembali'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_janji_bayar' => 'date',
            'waktu_keluar' => 'datetime',
            'waktu_kembali' => 'datetime',
        ];
    }

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiKepulangan::class, 'sesi_kepulangan_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
