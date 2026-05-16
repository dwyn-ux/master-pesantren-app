<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasBank extends Model
{
    protected $table = 'finance_kas_banks';

    protected $fillable = [
        'kode', 'nama', 'tipe',
        'nama_bank', 'no_rekening', 'atas_nama',
        'account_id', 'saldo_awal', 'saldo_berjalan',
        'is_active', 'keterangan',
    ];

    protected $casts = [
        'saldo_awal'     => 'decimal:2',
        'saldo_berjalan' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kas_bank_id');
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }

    public function recalcSaldo(): void
    {
        $masuk   = (float) Transaksi::where('kas_bank_id', $this->id)->where('tipe', 'masuk')->where('status', 'posted')->sum('nominal');
        $keluar  = (float) Transaksi::where('kas_bank_id', $this->id)->where('tipe', 'keluar')->where('status', 'posted')->sum('nominal');
        $trfIn   = (float) Transaksi::where('kas_bank_tujuan_id', $this->id)->where('tipe', 'transfer')->where('status', 'posted')->sum('nominal');
        $trfOut  = (float) Transaksi::where('kas_bank_id', $this->id)->where('tipe', 'transfer')->where('status', 'posted')->sum('nominal');

        $this->saldo_berjalan = (float) $this->saldo_awal + $masuk - $keluar + $trfIn - $trfOut;
        $this->save();
    }
}
