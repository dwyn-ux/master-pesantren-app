<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $fillable = ['nama', 'urutan'];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
