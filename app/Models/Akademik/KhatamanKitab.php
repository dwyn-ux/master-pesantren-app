<?php

namespace App\Models\Akademik;

use App\Models\Santri;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KhatamanKitab extends Model
{
    protected $table = 'khataman_kitab';

    protected $fillable = [
        'santri_id', 'kitab_id', 'ustadz_id',
        'tanggal_khatam', 'nomor_sertifikat', 'catatan',
    ];

    protected $casts = ['tanggal_khatam' => 'date'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function kitab(): BelongsTo
    {
        return $this->belongsTo(Kitab::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }
}
