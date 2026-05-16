<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanWaliKelas extends Model
{
    protected $table = 'catatan_wali_kelas';

    protected $fillable = ['santri_id', 'tahun_ajaran_id', 'ustadz_id', 'catatan'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }
}
