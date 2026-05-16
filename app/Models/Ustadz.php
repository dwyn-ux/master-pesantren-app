<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ustadz extends Model
{
    protected $table = 'ustadz';

    protected $fillable = ['user_id', 'nik', 'nama', 'alamat', 'tanggal_lahir', 'no_hp'];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function halaqah(): HasMany
    {
        return $this->hasMany(Halaqah::class);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class, 'penerima_id');
    }

    public function kunjunganKlinik()
    {
        return $this->morphMany(KunjunganKlinik::class, 'patient');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(\App\Models\Finance\UstadzSalary::class, 'ustadz_id');
    }

    public function latestSalary()
    {
        return $this->hasOne(\App\Models\Finance\UstadzSalary::class, 'ustadz_id')
            ->where('is_active', true)
            ->latestOfMany('berlaku_sejak');
    }
}
