<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $fillable = [
        'santri_id', 'mata_pelajaran_id', 'komponen_nilai_id', 'tahun_ajaran_id', 'nilai',
    ];

    protected $casts = ['nilai' => 'decimal:2'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function komponen(): BelongsTo
    {
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
