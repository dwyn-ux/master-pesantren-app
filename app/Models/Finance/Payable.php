<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payable extends Model
{
    protected $table = 'finance_payables';

    protected $fillable = [
        'nomor', 'tanggal', 'jatuh_tempo', 'vendor_id',
        'nominal', 'terbayar', 'sisa', 'status',
        'keterangan', 'journal_id',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'jatuh_tempo' => 'date',
        'nominal'     => 'decimal:2',
        'terbayar'    => 'decimal:2',
        'sisa'        => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayablePayment::class, 'payable_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function recalcStatus(): void
    {
        $this->terbayar = (float) $this->payments()->sum('nominal');
        $this->sisa     = (float) $this->nominal - (float) $this->terbayar;
        $this->status   = $this->sisa <= 0
            ? 'lunas'
            : ($this->terbayar > 0 ? 'sebagian' : 'belum_bayar');
        $this->save();
    }

    public static function generateNomor(): string
    {
        $prefix = 'AP/' . now()->format('Ym') . '/';
        $last   = static::where('nomor', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num    = 1;
        if ($last) {
            $parts = explode('/', $last->nomor);
            $num   = (int) end($parts) + 1;
        }
        return $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
