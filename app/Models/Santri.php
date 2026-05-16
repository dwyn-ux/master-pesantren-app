<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    protected $table = 'santri';

    protected $fillable = [
        'nis', 'nik', 'nama', 'nama_ar', 'kelas', 'jenis_kelamin', 'alamat', 'no_hp_ortu', 'tanggal_lahir', 'foto',
        'fingerprint_id', 'rfid_uid', 'saldo', 'is_aktif',
        'tipe_limit', 'nominal_limit',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'saldo'         => 'integer',
            'is_aktif'      => 'boolean',
            'nominal_limit' => 'integer',
        ];
    }

    public function checkLimitTransaksi(int $amount): void
    {
        if ($this->tipe_limit === 'tidak_ada' || ! $this->nominal_limit) {
            return;
        }

        $start = $this->tipe_limit === 'harian' ? now()->startOfDay() : now()->startOfWeek();

        $terpakai = WalletTransaction::where('santri_id', $this->id)
            ->where('jenis', 'debit')
            ->where('created_at', '>=', $start)
            ->sum('nominal');

        if ($terpakai + $amount > $this->nominal_limit) {
            $sisa    = max(0, $this->nominal_limit - $terpakai);
            $periode = $this->tipe_limit === 'harian' ? 'hari ini' : 'minggu ini';
            throw new \RuntimeException(
                "Limit uang saku {$periode} tercapai. Sisa limit: Rp " . number_format($sisa) . '.'
            );
        }
    }

    public function wali(): BelongsToMany
    {
        return $this->belongsToMany(Wali::class, 'santri_wali')
            ->withPivot('hubungan')
            ->using(SantriWali::class);
    }

    public function halaqah(): BelongsToMany
    {
        return $this->belongsToMany(Halaqah::class, 'halaqah_santri')
            ->withPivot('tanggal_bergabung')
            ->using(HalaqahSantri::class);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function transaksiKasir(): HasMany
    {
        return $this->hasMany(TransaksiKasir::class);
    }

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }

    public function kunjunganKlinik()
    {
        return $this->morphMany(KunjunganKlinik::class, 'patient');
    }

    public function kelasSantri(): HasMany
    {
        return $this->hasMany(\App\Models\Akademik\KelasSantri::class);
    }
}
