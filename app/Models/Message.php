<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'is_read',
        'read_at',
        'is_hidden',
        'hidden_reason',
        'hidden_by',
        'hidden_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'is_hidden' => 'boolean',
            'hidden_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    public function scopeUnread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    public function scopeForReceiver(Builder $query, int $receiverId): void
    {
        $query->where('sender_id', '!=', $receiverId);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function isFromUser(User $user): bool
    {
        return $this->sender_id === $user->id;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_hidden', false);
    }

    public function scopeHidden(Builder $query): void
    {
        $query->where('is_hidden', true);
    }

    public function hide(?int $moderatorId = null, ?string $reason = null): void
    {
        $this->update([
            'is_hidden' => true,
            'hidden_by' => $moderatorId,
            'hidden_at' => now(),
            'hidden_reason' => $reason,
        ]);
    }

    public function unhide(): void
    {
        $this->update([
            'is_hidden' => false,
            'hidden_by' => null,
            'hidden_at' => null,
            'hidden_reason' => null,
        ]);
    }

    public function isHidden(): bool
    {
        return $this->is_hidden;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Update conversation's last message time
            $message->conversation->updateLastMessageTime();
        });
    }
}
