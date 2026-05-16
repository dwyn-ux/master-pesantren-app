<?php

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'finance_audit_logs';

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'before', 'after', 'ip', 'user_agent',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record(string $action, $subject = null, array $before = [], array $after = []): self
    {
        return static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'before'       => $before ?: null,
            'after'        => $after ?: null,
            'ip'           => request()?->ip(),
            'user_agent'   => request()?->userAgent(),
        ]);
    }
}
