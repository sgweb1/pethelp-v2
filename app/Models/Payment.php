<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'user_id',
        'subscription_plan_id',
        'status',
        'amount',
        'original_amount',
        'proration_credit',
        'commission',
        'payment_method',
        'external_id',
        'gateway_response',
        'processed_at',
        'metadata'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'proration_credit' => 'decimal:2',
            'commission' => 'decimal:2',
            'gateway_response' => 'array',
            'metadata' => 'array',
            'processed_at' => 'datetime'
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
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

    public function isSubscriptionPayment(): bool
    {
        return !is_null($this->subscription_plan_id);
    }

    public function isBookingPayment(): bool
    {
        return !is_null($this->booking_id);
    }

    public function getProrationSavingsAttribute(): float
    {
        return $this->proration_credit ?? 0;
    }

    public function getFinalAmountAttribute(): float
    {
        return $this->amount;
    }

    public function getTypeAttribute(): string
    {
        if ($this->isSubscriptionPayment()) {
            return 'subscription';
        } elseif ($this->isBookingPayment()) {
            return 'booking';
        }
        return 'other';
    }
}
