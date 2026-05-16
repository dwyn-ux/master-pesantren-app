<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kkm extends Model
{
    protected $table = 'kkm';

    protected $fillable = ['mata_pelajaran_id', 'tingkat_id', 'tahun_ajaran_id', 'nilai_kkm'];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tingkat(): BelongsTo
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
