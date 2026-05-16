<?php

namespace App\Models\Akademik;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalMengajar extends Model
{
    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'jadwal_pelajaran_id', 'ustadz_id', 'tanggal',
        'materi', 'halaman_kitab', 'pr', 'catatan',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }
}
