<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KenaikanKelas extends Model
{
    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'santri_id', 'tahun_ajaran_id',
        'kelas_asal_id', 'kelas_tujuan_id',
        'keputusan', 'catatan',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelasAsal(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_asal_id');
    }

    public function kelasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan_id');
    }
}
