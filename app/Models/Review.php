<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
        'is_visible',
        'moderated_at'
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_visible', true);
    }

    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where('reviewee_id', $userId);
    }

    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('reviewer_id', $userId);
    }

    public function scopeForSitter(Builder $query, int $sitterId): void
    {
        $query->where('reviewee_id', $sitterId)
              ->whereHas('booking', function($q) {
                  $q->where('status', 'completed');
              });
    }

    public function scopeRecent(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            1 => 'Bardzo słaba',
            2 => 'Słaba',
            3 => 'Średnia',
            4 => 'Dobra',
            5 => 'Bardzo dobra',
            default => 'Brak oceny'
        };
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->reviewer_id === $user->id &&
               $this->created_at->diffInHours() < 24;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->reviewer_id === $user->id;
    }

    public function hide(): void
    {
        $this->update(['is_visible' => false]);
    }

    public function show(): void
    {
        $this->update(['is_visible' => true]);
    }

    public function moderate(): void
    {
        $this->update(['moderated_at' => now()]);
    }
}
