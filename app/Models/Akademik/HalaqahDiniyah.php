<?php

namespace App\Models\Akademik;

use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HalaqahDiniyah extends Model
{
    protected $table = 'halaqah_diniyah';

    protected $fillable = ['nama', 'ustadz_id', 'kitab_id', 'deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }

    public function kitab(): BelongsTo
    {
        return $this->belongsTo(Kitab::class);
    }

    public function santri(): HasMany
    {
        return $this->hasMany(HalaqahDiniyahSantri::class);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(SetoranDiniyah::class);
    }
}
