<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    protected $table = 'finance_journal_lines';

    protected $fillable = [
        'journal_id', 'account_id', 'kas_bank_id',
        'debit', 'kredit', 'keterangan',
    ];

    protected $casts = [
        'debit'  => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function kasBank(): BelongsTo
    {
        return $this->belongsTo(KasBank::class, 'kas_bank_id');
    }
}
