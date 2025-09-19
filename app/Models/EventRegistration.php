<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'message',
        'organizer_notes',
        'registered_at',
        'status_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'status_updated_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for performance
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWaitingList($query)
    {
        return $query->where('status', 'waiting_list');
    }

    // Status management methods
    public function confirm(?string $organizerNotes = null): bool
    {
        return $this->updateStatus('confirmed', $organizerNotes);
    }

    public function reject(?string $organizerNotes = null): bool
    {
        return $this->updateStatus('rejected', $organizerNotes);
    }

    public function moveToWaitingList(?string $organizerNotes = null): bool
    {
        return $this->updateStatus('waiting_list', $organizerNotes);
    }

    public function cancel(): bool
    {
        return $this->updateStatus('cancelled');
    }

    private function updateStatus(string $status, ?string $organizerNotes = null): bool
    {
        $updated = $this->update([
            'status' => $status,
            'organizer_notes' => $organizerNotes ?? $this->organizer_notes,
            'status_updated_at' => now(),
        ]);

        // Update event participant count if confirmed/cancelled
        if ($updated && in_array($status, ['confirmed', 'cancelled', 'rejected'])) {
            $this->event->updateParticipantCount();
        }

        return $updated;
    }

    // Helper methods
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOnWaitingList(): bool
    {
        return $this->status === 'waiting_list';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
