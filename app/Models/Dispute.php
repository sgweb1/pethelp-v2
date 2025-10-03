<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Dispute - reprezentuje zgłoszenie problemu lub sporu.
 *
 * Umożliwia użytkownikom zgłaszanie problemów związanych z rezerwacjami
 * oraz administratorom zarządzanie i rozwiązywanie sporów.
 *
 * @package App\Models
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class Dispute extends Model
{
    protected $fillable = [
        'booking_id',
        'reported_by',
        'against_user_id',
        'status',
        'category',
        'title',
        'description',
        'admin_notes',
        'assigned_to',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function againstUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes

    public function scopeNew(Builder $query): void
    {
        $query->where('status', 'new');
    }

    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', 'in_progress');
    }

    public function scopeResolved(Builder $query): void
    {
        $query->where('status', 'resolved');
    }

    public function scopeRejected(Builder $query): void
    {
        $query->where('status', 'rejected');
    }

    public function scopeUnassigned(Builder $query): void
    {
        $query->whereNull('assigned_to');
    }

    public function scopeAssignedTo(Builder $query, int $adminId): void
    {
        $query->where('assigned_to', $adminId);
    }

    public function scopeByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    // Helper methods

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function assignTo(int $adminId): void
    {
        $this->update([
            'assigned_to' => $adminId,
            'status' => 'in_progress',
        ]);
    }

    public function resolve(int $adminId, string $resolution): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_by' => $adminId,
            'resolved_at' => now(),
        ]);
    }

    public function reject(int $adminId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'resolution' => $reason,
            'resolved_by' => $adminId,
            'resolved_at' => now(),
        ]);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'cancellation' => 'Anulowanie',
            'payment' => 'Płatność',
            'behavior' => 'Zachowanie',
            'quality' => 'Jakość usługi',
            'other' => 'Inne',
            default => 'Nieznana'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Nowe',
            'in_progress' => 'W trakcie',
            'resolved' => 'Rozwiązane',
            'rejected' => 'Odrzucone',
            default => 'Nieznany'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'rejected' => 'danger',
            default => 'gray'
        };
    }
}
