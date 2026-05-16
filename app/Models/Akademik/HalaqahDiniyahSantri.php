<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HalaqahDiniyahSantri extends Model
{
    protected $table = 'halaqah_diniyah_santri';

    protected $fillable = ['halaqah_diniyah_id', 'santri_id', 'tanggal_bergabung', 'is_active'];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'is_active'         => 'boolean',
    ];

    public function halaqah(): BelongsTo
    {
        return $this->belongsTo(HalaqahDiniyah::class, 'halaqah_diniyah_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
