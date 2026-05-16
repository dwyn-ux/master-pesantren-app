<?php

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseRequest extends Model
{
    protected $table = 'finance_expense_requests';

    protected $fillable = [
        'nomor', 'tanggal', 'judul', 'deskripsi', 'nominal',
        'kategori_id', 'kas_bank_id', 'vendor_id', 'bukti', 'status', 'current_level',
        'created_by', 'approved_by_kepala', 'approved_kepala_at',
        'approved_by_yayasan', 'approved_yayasan_at', 'reject_reason', 'transaksi_id',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'approved_kepala_at'  => 'datetime',
        'approved_yayasan_at' => 'datetime',
        'nominal'             => 'decimal:2',
    ];

    public function kategori(): BelongsTo { return $this->belongsTo(Kategori::class, 'kategori_id'); }
    public function kasBank(): BelongsTo  { return $this->belongsTo(KasBank::class, 'kas_bank_id'); }
    public function vendor(): BelongsTo   { return $this->belongsTo(Vendor::class, 'vendor_id'); }
    public function creator(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }
    public function transaksi(): BelongsTo { return $this->belongsTo(Transaksi::class); }

    public static function generateNomor(): string
    {
        $prefix = 'ER/' . now()->format('Ym') . '/';
        $last   = static::where('nomor', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num    = 1;
        if ($last) {
            $parts = explode('/', $last->nomor);
            $num   = (int) end($parts) + 1;
        }
        return $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }

    public static function statusLabels(): array
    {
        return [
            'draft' => 'Draft',
            'pending_kepala' => 'Menunggu Kepala Pondok',
            'pending_yayasan' => 'Menunggu Yayasan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'paid' => 'Dibayar',
            'void' => 'Void',
        ];
    }
}
