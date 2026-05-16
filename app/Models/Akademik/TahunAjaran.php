<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = ['nama', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_active'];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_active'       => 'boolean',
    ];

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function kelasSantri(): HasMany
    {
        return $this->hasMany(KelasSantri::class);
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nama} - " . ucfirst($this->semester);
    }
}
