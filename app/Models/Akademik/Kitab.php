<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kitab extends Model
{
    protected $table = 'kitab';

    protected $fillable = ['mata_pelajaran_id', 'nama', 'pengarang', 'total_halaman'];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
