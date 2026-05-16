<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HalaqahSantri extends Pivot
{
    protected $table = 'halaqah_santri';

    public $timestamps = false;

    protected $fillable = ['santri_id', 'halaqah_id', 'tanggal_bergabung'];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }
}
