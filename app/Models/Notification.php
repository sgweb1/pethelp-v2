<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'is_important'
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'is_important' => 'boolean'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): void
    {
        $query->whereNotNull('read_at');
    }

    public function scopeImportant(Builder $query): void
    {
        $query->where('is_important', true);
    }

    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);
        return $this;
    }

    public function markAsUnread(): self
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'booking_created' => '📅',
            'booking_confirmed' => '✅',
            'booking_cancelled' => '❌',
            'booking_completed' => '🎉',
            'payment_completed' => '💰',
            'payment_failed' => '💳',
            'message_received' => '💬',
            'review_received' => '⭐',
            'reminder' => '⏰',
            default => '📝'
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'booking_created' => 'Nowa rezerwacja',
            'booking_confirmed' => 'Rezerwacja potwierdzona',
            'booking_cancelled' => 'Rezerwacja anulowana',
            'booking_completed' => 'Rezerwacja zakończona',
            'payment_completed' => 'Płatność zakończona',
            'payment_failed' => 'Płatność nieudana',
            'message_received' => 'Nowa wiadomość',
            'review_received' => 'Nowa opinia',
            'reminder' => 'Przypomnienie',
            default => 'Powiadomienie'
        };
    }
}
