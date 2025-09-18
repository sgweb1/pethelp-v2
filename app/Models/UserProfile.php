<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'instant_booking' => 'boolean',
            'flexible_cancellation' => 'boolean',
            'has_insurance' => 'boolean',
            'certifications' => 'array',
            'rating_average' => 'decimal:2',
        ];
    }

    protected $fillable = [
        'user_id',
        'role',
        'first_name',
        'last_name',
        'phone',
        'bio',
        'avatar',
        'address',
        'is_verified',
        'verified_at',
        'experience_years',
        'instant_booking',
        'flexible_cancellation',
        'has_insurance',
        'insurance_details',
        'certifications',
        'rating_average',
        'reviews_count',
        'total_bookings',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getExperienceDisplayAttribute(): string
    {
        if (!$this->experience_years) {
            return 'Brak doświadczenia';
        }

        if ($this->experience_years == 1) {
            return '1 rok doświadczenia';
        }

        if ($this->experience_years < 5) {
            return $this->experience_years . ' lata doświadczenia';
        }

        return $this->experience_years . ' lat doświadczenia';
    }

    public function isExperienced(): bool
    {
        return $this->experience_years >= 3;
    }

    public function hasHighRating(): bool
    {
        return $this->rating_average >= 4.5;
    }

    public function isActiveProvider(): bool
    {
        return $this->total_bookings >= 5;
    }
}
