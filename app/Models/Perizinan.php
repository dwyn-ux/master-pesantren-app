<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    protected $table = 'perizinan';

    protected $fillable = [
        'santri_id', 'tanggal_mulai', 'tanggal_selesai',
        'waktu_keluar_aktual', 'waktu_kembali_aktual',
        'alasan', 'status', 'diajukan_oleh', 'disetujui_oleh'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'waktu_keluar_aktual' => 'datetime',
            'waktu_kembali_aktual' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
