<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranDiniyah extends Model
{
    protected $table = 'setoran_diniyah';

    protected $fillable = [
        'halaqah_diniyah_id', 'santri_id', 'ustadz_id', 'tanggal',
        'halaman_mulai', 'halaman_selesai', 'jenis', 'nilai', 'catatan',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function halaqah(): BelongsTo
    {
        return $this->belongsTo(HalaqahDiniyah::class, 'halaqah_diniyah_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }
}
