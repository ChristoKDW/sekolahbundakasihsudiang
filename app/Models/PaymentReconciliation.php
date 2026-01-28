<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'reconciliation_date',
        'start_date',
        'end_date',
        'total_transactions',
        'matched_transactions',
        'unmatched_transactions',
        'matched_count',
        'unmatched_count',
        'total_amount',
        'matched_amount',
        'unmatched_amount',
        'status',
        'report_file',
        'details',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'details' => 'array',
        'processed_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'matched_amount' => 'decimal:2',
        'unmatched_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reconciliation) {
            if (empty($reconciliation->batch_number)) {
                $reconciliation->batch_number = self::generateBatchNumber();
            }
        });
    }

    public static function generateBatchNumber(): string
    {
        return 'REC' . now()->format('YmdHis') . rand(100, 999);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'reconciliation_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getMatchRateAttribute(): float
    {
        if ($this->total_transactions === 0) {
            return 0;
        }
        return round(($this->matched_transactions / $this->total_transactions) * 100, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Menunggu</span>',
            'processing' => '<span class="badge bg-info">Diproses</span>',
            'completed' => '<span class="badge bg-success">Selesai</span>',
            'failed' => '<span class="badge bg-danger">Gagal</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }
}
