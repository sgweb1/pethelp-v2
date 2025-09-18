<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'status',
        'amount',
        'commission',
        'payment_method',
        'external_id',
        'gateway_response',
        'processed_at'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
            'gateway_response' => 'array',
            'processed_at' => 'datetime'
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->where('status', 'failed');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Oczekująca',
            'processing' => 'Przetwarzana',
            'completed' => 'Zakończona',
            'failed' => 'Nieudana',
            'refunded' => 'Zwrócona',
            default => 'Nieznany'
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'card' => 'Karta płatnicza',
            'blik' => 'BLIK',
            'transfer' => 'Przelew bankowy',
            default => 'Inne'
        };
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' &&
               $this->processed_at?->diffInDays(now()) <= 30;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getSitterAmountAttribute(): float
    {
        return $this->amount - $this->commission;
    }
}
