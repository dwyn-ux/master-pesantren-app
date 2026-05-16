<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPelajaran extends Model
{
    protected $table = 'absensi_pelajaran';

    protected $fillable = ['jadwal_pelajaran_id', 'santri_id', 'tanggal', 'status', 'keterangan'];

    protected $casts = ['tanggal' => 'date'];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
