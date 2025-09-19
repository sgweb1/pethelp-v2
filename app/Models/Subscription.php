<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'price',
        'billing_period',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'last_payment_at',
        'next_billing_at',
        'payment_method',
        'external_id',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PAUSED = 'paused';
    const STATUS_PENDING = 'pending';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere('ends_at', '<=', now());
        });
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeUpForRenewal($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('next_billing_at', '<=', now()->addDays(3));
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->starts_at <= now()
            && $this->ends_at > now();
    }

    public function isExpired(): bool
    {
        return $this->ends_at <= now() || $this->status === self::STATUS_EXPIRED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function hasFeature(string $feature): bool
    {
        return $this->isActive() && $this->subscriptionPlan->hasFeature($feature);
    }

    public function cancel(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();

        return $this->save();
    }

    public function resume(): bool
    {
        if ($this->isCancelled() && $this->ends_at > now()) {
            $this->status = self::STATUS_ACTIVE;
            $this->cancelled_at = null;

            return $this->save();
        }

        return false;
    }

    public function renew(): bool
    {
        if ($this->isActive()) {
            $period = $this->billing_period === 'yearly' ? 12 : 1;

            $this->ends_at = $this->ends_at->addMonths($period);
            $this->next_billing_at = $this->next_billing_at->addMonths($period);
            $this->last_payment_at = now();

            return $this->save();
        }

        return false;
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return $this->ends_at->diffInDays(now());
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' PLN';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Aktywna',
            self::STATUS_CANCELLED => 'Anulowana',
            self::STATUS_EXPIRED => 'Wygasła',
            self::STATUS_PAUSED => 'Wstrzymana',
            self::STATUS_PENDING => 'Oczekująca',
            default => 'Nieznany status'
        };
    }

    public function getNextBillingAmountAttribute(): float
    {
        return $this->price;
    }

    public function canBeCancelled(): bool
    {
        return $this->isActive() && !$this->isCancelled();
    }

    public function canBeResumed(): bool
    {
        return $this->isCancelled() && $this->ends_at > now();
    }

    public function getRemainingListingsAttribute(): ?int
    {
        if (!$this->subscriptionPlan->max_listings) {
            return null; // Unlimited
        }

        $usedListings = $this->user->advertisements()->count();
        return max(0, $this->subscriptionPlan->max_listings - $usedListings);
    }

    public function hasUnlimitedListings(): bool
    {
        return is_null($this->subscriptionPlan->max_listings);
    }

    public static function createFromPlan(User $user, SubscriptionPlan $plan, array $metadata = []): self
    {
        $period = $plan->billing_period === 'yearly' ? 12 : 1;
        $starts_at = now();
        $ends_at = $starts_at->copy()->addMonths($period);
        $next_billing_at = $plan->price > 0 ? $ends_at->copy() : null;

        return static::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => $plan->price > 0 ? self::STATUS_PENDING : self::STATUS_ACTIVE,
            'price' => $plan->price,
            'billing_period' => $plan->billing_period,
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'next_billing_at' => $next_billing_at,
            'metadata' => $metadata,
        ]);
    }
}