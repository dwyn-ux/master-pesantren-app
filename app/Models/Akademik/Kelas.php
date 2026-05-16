<?php

namespace App\Models\Akademik;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['tingkat_id', 'nama', 'wali_kelas_id', 'kapasitas'];

    public function tingkat(): BelongsTo
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'wali_kelas_id');
    }

    public function santri(): HasMany
    {
        return $this->hasMany(KelasSantri::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
