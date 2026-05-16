<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kategori extends Model
{
    protected $table = 'finance_kategoris';

    protected $fillable = [
        'kode', 'nama', 'tipe', 'account_id', 'is_active', 'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function scopePemasukan($q)
    {
        return $q->where('tipe', 'pemasukan');
    }

    public function scopePengeluaran($q)
    {
        return $q->where('tipe', 'pengeluaran');
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }
}
