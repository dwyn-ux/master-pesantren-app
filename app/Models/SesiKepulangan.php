<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiKepulangan extends Model
{
    protected $table = 'sesi_kepulangan';

    protected $fillable = [
        'nama_sesi', 'tanggal_pulang', 'tanggal_kembali', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pulang' => 'date',
            'tanggal_kembali' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function kepulanganSantri(): HasMany
    {
        return $this->hasMany(KepulanganSantri::class);
    }
}
