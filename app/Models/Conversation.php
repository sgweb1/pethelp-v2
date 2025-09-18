<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'booking_id',
        'last_message_at'
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where(function($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        });
    }

    public function scopeRecent(Builder $query): void
    {
        $query->orderBy('last_message_at', 'desc')
              ->orderBy('updated_at', 'desc');
    }

    public function getOtherUser(User $user): User
    {
        if ($this->user_one_id === $user->id) {
            return $this->userTwo;
        }
        return $this->userOne;
    }

    public function getUnreadCount(User $user): int
    {
        return $this->messages()
                   ->where('sender_id', '!=', $user->id)
                   ->where('is_read', false)
                   ->count();
    }

    public function markAsRead(User $user): void
    {
        $this->messages()
             ->where('sender_id', '!=', $user->id)
             ->where('is_read', false)
             ->update([
                 'is_read' => true,
                 'read_at' => now()
             ]);
    }

    public function updateLastMessageTime(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    public static function findOrCreateBetween(User $userOne, User $userTwo, ?Booking $booking = null): self
    {
        // Ensure consistent ordering of users to avoid duplicates
        $userOneId = min($userOne->id, $userTwo->id);
        $userTwoId = max($userOne->id, $userTwo->id);

        return static::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
            'booking_id' => $booking?->id,
        ]);
    }
}
