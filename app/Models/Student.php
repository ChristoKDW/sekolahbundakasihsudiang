<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'nisn',
        'education_level',
        'name',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'address',
        'phone',
        'class',
        'major',
        'status',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get education level options
     */
    public static function getEducationLevels(): array
    {
        return [
            'TK' => 'TK (Taman Kanak-kanak)',
            'SD' => 'SD (Sekolah Dasar)',
            'SMP' => 'SMP (Sekolah Menengah Pertama)',
            'SMA' => 'SMA (Sekolah Menengah Atas)',
        ];
    }

    /**
     * Get education level label
     */
    public function getEducationLevelLabelAttribute(): string
    {
        return self::getEducationLevels()[$this->education_level] ?? $this->education_level;
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function unpaidBills(): HasMany
    {
        return $this->hasMany(Bill::class)->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    public function getTotalUnpaidAttribute(): float
    {
        return $this->unpaidBills()->sum(\DB::raw('total_amount - paid_amount'));
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address;
    }

    public function getAgeAttribute(): int
    {
        if (!$this->date_of_birth) {
            return 0;
        }
        return \Carbon\Carbon::parse($this->date_of_birth)->age;
    }
}
