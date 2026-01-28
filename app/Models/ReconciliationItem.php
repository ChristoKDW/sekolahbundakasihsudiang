<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReconciliationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_id',
        'payment_id',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'midtrans_amount',
        'system_amount',
        'match_status',
        'notes',
    ];

    protected $casts = [
        'midtrans_amount' => 'decimal:2',
        'system_amount' => 'decimal:2',
    ];

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(PaymentReconciliation::class, 'reconciliation_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getMatchStatusBadgeAttribute(): string
    {
        return match($this->match_status) {
            'matched' => '<span class="badge bg-success">Cocok</span>',
            'amount_mismatch' => '<span class="badge bg-warning">Selisih</span>',
            'not_found' => '<span class="badge bg-danger">Tidak Ditemukan</span>',
            'duplicate' => '<span class="badge bg-info">Duplikat</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    public function getDifferenceAttribute(): float
    {
        if ($this->system_amount === null) {
            return (float) $this->midtrans_amount;
        }
        return (float) ($this->midtrans_amount - $this->system_amount);
    }
}
