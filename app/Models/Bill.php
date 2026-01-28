<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'bill_type_id',
        'amount',
        'discount',
        'fine',
        'total_amount',
        'paid_amount',
        'due_date',
        'status',
        'academic_year',
        'month',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'fine' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->invoice_number)) {
                $bill->invoice_number = self::generateInvoiceNumber();
            }
            if (empty($bill->total_amount)) {
                $bill->total_amount = $bill->amount - $bill->discount + $bill->fine;
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastBill = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastBill ? (int) substr($lastBill->invoice_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function billType(): BelongsTo
    {
        return $this->belongsTo(BillType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'success');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->paid_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Menunggu</span>',
            'partial' => '<span class="badge bg-info">Sebagian</span>',
            'paid' => '<span class="badge bg-success">Lunas</span>',
            'overdue' => '<span class="badge bg-danger">Terlambat</span>',
            'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->successfulPayments()->sum('amount');
        $this->paid_amount = $totalPaid;

        if ($totalPaid >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date < now() && $this->status !== 'paid') {
            $this->status = 'overdue';
        }

        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && !in_array($this->status, ['paid', 'cancelled']);
    }
}
