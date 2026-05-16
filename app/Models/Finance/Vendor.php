<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $table = 'finance_vendors';

    protected $fillable = [
        'kode', 'nama', 'kontak_person', 'telepon', 'email', 'alamat',
        'npwp', 'bank_nama', 'bank_rekening', 'bank_atas_nama', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class, 'vendor_id');
    }
}
