<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = ['nama', 'kode', 'deskripsi', 'urutan'];

    public function kitab(): HasMany
    {
        return $this->hasMany(Kitab::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function komponenNilai(): HasMany
    {
        return $this->hasMany(KomponenNilai::class);
    }
}
