<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'is_flexible',
        'is_mandatory',
        'is_recurring',
        'recurring_period',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_flexible' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    public function getMandatoryLabelAttribute(): string
    {
        return $this->is_mandatory ? 'Wajib' : 'Sukarela';
    }

    public function getMandatoryBadgeAttribute(): string
    {
        return $this->is_mandatory 
            ? '<span class="badge bg-danger">Wajib</span>' 
            : '<span class="badge bg-info">Sukarela</span>';
    }
}
