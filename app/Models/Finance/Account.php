<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $table = 'finance_accounts';

    protected $fillable = [
        'kode', 'nama', 'tipe', 'saldo_normal',
        'parent_id', 'is_kas_bank', 'is_active', 'keterangan',
    ];

    protected $casts = [
        'is_kas_bank' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public const TIPE = [
        'asset'     => 'Aset',
        'liability' => 'Liabilitas',
        'equity'    => 'Ekuitas',
        'revenue'   => 'Pendapatan',
        'expense'   => 'Beban',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }

    public function getSaldoAttribute(): float
    {
        $debit  = (float) $this->journalLines()->sum('debit');
        $kredit = (float) $this->journalLines()->sum('kredit');

        return $this->saldo_normal === 'debit' ? $debit - $kredit : $kredit - $debit;
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeTipe($q, string $tipe)
    {
        return $q->where('tipe', $tipe);
    }
}
