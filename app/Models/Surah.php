<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    protected $table = 'surah';

    public $timestamps = false;

    protected $fillable = ['id', 'nama', 'nama_latin', 'jumlah_ayat', 'juz_awal'];

    // Relasi balik: semua setoran yang dimulai dari surah ini
    public function setoranAwal(): HasMany
    {
        return $this->hasMany(Setoran::class, 'surah_awal');
    }

    // Relasi balik: semua setoran yang berakhir di surah ini
    public function setoranAkhir(): HasMany
    {
        return $this->hasMany(Setoran::class, 'surah_akhir');
    }
}
