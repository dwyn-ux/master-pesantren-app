<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $table = 'finance_transaksi';

    protected $fillable = [
        'nomor', 'tanggal', 'tipe', 'kategori_id',
        'kas_bank_id', 'kas_bank_tujuan_id',
        'nominal', 'pihak', 'keterangan', 'bukti',
        'journal_id', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function kasBank(): BelongsTo
    {
        return $this->belongsTo(KasBank::class, 'kas_bank_id');
    }

    public function kasBankTujuan(): BelongsTo
    {
        return $this->belongsTo(KasBank::class, 'kas_bank_tujuan_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public static function generateNomor(string $tipe): string
    {
        $prefix = match ($tipe) {
            'masuk'    => 'CI',
            'keluar'   => 'CO',
            'transfer' => 'TR',
            default    => 'TX',
        };
        $prefix = $prefix . '/' . now()->format('Ym') . '/';
        $last   = static::where('nomor', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num    = 1;
        if ($last) {
            $parts = explode('/', $last->nomor);
            $num   = (int) end($parts) + 1;
        }
        return $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }

    public function scopePosted($q)
    {
        return $q->where('status', 'posted');
    }
}
