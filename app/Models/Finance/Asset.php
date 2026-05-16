<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $table = 'finance_assets';

    protected $fillable = [
        'kode', 'nama', 'kategori', 'tanggal_perolehan',
        'harga_perolehan', 'nilai_residu', 'umur_ekonomis_bulan',
        'metode_penyusutan', 'akumulasi_penyusutan', 'nilai_buku',
        'status', 'lokasi', 'keterangan', 'account_id',
    ];

    protected $casts = [
        'tanggal_perolehan'    => 'date',
        'harga_perolehan'      => 'decimal:2',
        'nilai_residu'         => 'decimal:2',
        'akumulasi_penyusutan' => 'decimal:2',
        'nilai_buku'           => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id');
    }

    public function penyusutanBulanan(): float
    {
        if ($this->metode_penyusutan === 'garis_lurus') {
            $base = (float) $this->harga_perolehan - (float) $this->nilai_residu;
            return $this->umur_ekonomis_bulan > 0 ? $base / $this->umur_ekonomis_bulan : 0;
        }
        return ((float) $this->nilai_buku) * 0.02;
    }
}
