<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Journal extends Model
{
    protected $table = 'finance_journals';

    protected $fillable = [
        'nomor', 'tanggal', 'referensi', 'tipe',
        'source_type', 'source_id', 'keterangan',
        'total_debit', 'total_kredit', 'status',
        'created_by', 'posted_by', 'posted_at',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'posted_at'    => 'datetime',
        'total_debit'  => 'decimal:2',
        'total_kredit' => 'decimal:2',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function isBalanced(): bool
    {
        return abs((float) $this->total_debit - (float) $this->total_kredit) < 0.01;
    }

    public static function generateNomor(string $tipe = 'JU'): string
    {
        $prefix = strtoupper($tipe) . '/' . now()->format('Ym') . '/';
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
