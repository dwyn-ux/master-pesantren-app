<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    protected $table = 'finance_asset_depreciations';

    protected $fillable = [
        'asset_id', 'tahun', 'bulan', 'nominal', 'akumulasi', 'nilai_buku', 'journal_id',
    ];

    protected $casts = [
        'nominal'    => 'decimal:2',
        'akumulasi'  => 'decimal:2',
        'nilai_buku' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }
}
