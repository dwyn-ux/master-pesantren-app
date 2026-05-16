<?php

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $table = 'finance_approvals';

    protected $fillable = [
        'approvable_type', 'approvable_id', 'user_id',
        'status', 'level', 'catatan', 'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
