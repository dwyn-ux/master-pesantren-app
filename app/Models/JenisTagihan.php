<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisTagihan extends Model
{
    protected $table = 'jenis_tagihan';

    public $timestamps = false;

    protected $fillable = ['nama', 'kelompok', 'nominal', 'is_nominal_tetap', 'is_aktif'];

    protected function casts(): array
    {
        return [
            'is_nominal_tetap' => 'boolean',
            'is_aktif'         => 'boolean',
        ];
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }
}
