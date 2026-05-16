<?php

namespace App\Models\Akademik;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'kelas_id', 'mata_pelajaran_id', 'ustadz_id', 'tahun_ajaran_id',
        'hari', 'jam_mulai', 'jam_selesai', 'ruangan',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiPelajaran::class);
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(JurnalMengajar::class);
    }
}
