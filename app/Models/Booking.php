<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    protected $fillable = [
        'owner_id',
        'sitter_id',
        'service_id',
        'pet_id',
        'start_date',
        'end_date',
        'status',
        'total_price',
        'special_instructions',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ownerReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_id', $this->owner_id);
    }

    public function sitterReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_id', $this->sitter_id);
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForSitter(Builder $query, int $sitterId): Builder
    {
        return $query->where('sitter_id', $sitterId);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    // Helper Methods
    public function getDurationInHoursAttribute(): float
    {
        return $this->start_date->diffInHours($this->end_date);
    }

    public function getDurationInDaysAttribute(): float
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Oczekująca',
            'confirmed' => 'Potwierdzona',
            'in_progress' => 'W trakcie',
            'completed' => 'Zakończona',
            'cancelled' => 'Anulowana',
            default => 'Nieznany'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'in_progress' => 'green',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               $this->start_date->isFuture();
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'confirmed' &&
               $this->start_date->isPast() &&
               $this->end_date->isFuture();
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress' ||
               ($this->status === 'confirmed' && $this->end_date->isPast());
    }

    public function canBeReviewedBy(User $user): bool
    {
        return $this->status === 'completed' &&
               ($this->owner_id === $user->id || $this->sitter_id === $user->id) &&
               !$this->hasReviewBy($user);
    }

    public function hasReviewBy(User $user): bool
    {
        return $this->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function getReviewBy(User $user): ?Review
    {
        return $this->reviews()->where('reviewer_id', $user->id)->first();
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->visible()->avg('rating') ?? 0;
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
