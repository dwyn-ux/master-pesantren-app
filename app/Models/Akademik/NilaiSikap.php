<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiSikap extends Model
{
    protected $table = 'nilai_sikap';

    protected $fillable = ['santri_id', 'tahun_ajaran_id', 'aspek', 'predikat', 'deskripsi'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
