<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'bill_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_channel',
        'payment_gateway',
        'status',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'midtrans_response',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_payment_id',
        'xendit_va_id',
        'va_number',
        'va_bank',
        'xendit_response',
        'paid_at',
        'expired_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'midtrans_response' => 'array',
        'xendit_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
            if (empty($payment->order_id)) {
                $payment->order_id = self::generateOrderId();
            }
        });

        static::updated(function ($payment) {
            if ($payment->isDirty('status')) {
                $payment->bill->updatePaymentStatus();
            }
        });
    }

    public static function generateTransactionId(): string
    {
        return 'TRX' . now()->format('YmdHis') . rand(1000, 9999);
    }

    public static function generateOrderId(): string
    {
        return 'ORD' . now()->format('YmdHis') . rand(1000, 9999);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $amount = $this->amount ?? 0;
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Menunggu</span>',
            'success' => '<span class="badge bg-success">Berhasil</span>',
            'failed' => '<span class="badge bg-danger">Gagal</span>',
            'expired' => '<span class="badge bg-secondary">Kadaluarsa</span>',
            'refunded' => '<span class="badge bg-info">Dikembalikan</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'echannel' => 'Mandiri Bill',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            default => $this->payment_method ?? '-',
        };
    }

    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
