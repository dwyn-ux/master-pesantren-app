<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayablePayment extends Model
{
    protected $table = 'finance_payable_payments';

    protected $fillable = [
        'payable_id', 'tanggal', 'nominal', 'kas_bank_id',
        'keterangan', 'journal_id', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class, 'payable_id');
    }

    public function kasBank(): BelongsTo
    {
        return $this->belongsTo(KasBank::class, 'kas_bank_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }
}
