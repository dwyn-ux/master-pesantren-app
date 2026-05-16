<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $table = 'import_logs';

    // Immutable — hanya created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'admin_id', 'tipe', 'total_baris', 'berhasil', 'gagal', 'file_asli',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
