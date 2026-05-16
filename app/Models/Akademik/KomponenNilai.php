<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomponenNilai extends Model
{
    protected $table = 'komponen_nilai';

    protected $fillable = ['mata_pelajaran_id', 'tahun_ajaran_id', 'nama', 'bobot', 'urutan'];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
