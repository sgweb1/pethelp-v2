<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'max_listings',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_listings' => 'integer',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function scopeMonthly($query)
    {
        return $query->where('billing_period', 'monthly');
    }

    public function scopeYearly($query)
    {
        return $query->where('billing_period', 'yearly');
    }

    // Methods
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' PLN';
    }

    public function getMonthlyPriceAttribute(): float
    {
        return $this->billing_period === 'yearly' ? $this->price / 12 : $this->price;
    }

    public function isBasic(): bool
    {
        return $this->slug === 'basic';
    }

    public function isPro(): bool
    {
        return $this->slug === 'pro';
    }

    public function isPremium(): bool
    {
        return $this->slug === 'premium';
    }

    public function isBusiness(): bool
    {
        return $this->slug === 'business';
    }

    public function getFeatureListAttribute(): array
    {
        $defaultFeatures = [
            'basic_search' => 'Podstawowe wyszukiwanie',
            'listings' => 'Ogłoszenia',
            'messaging' => 'Wiadomości',
            'reviews' => 'Opinie i oceny',
            'basic_support' => 'Podstawowe wsparcie',
            'priority_search' => 'Priority w wyszukiwaniu',
            'unlimited_listings' => 'Nielimitowane ogłoszenia',
            'advanced_search' => 'Zaawansowane wyszukiwanie',
            'analytics' => 'Analityka i statystyki',
            'verified_badge' => 'Badge "Zweryfikowany"',
            'ai_matching' => 'AI-powered matching',
            'promoted_listings' => 'Promowane ogłoszenia',
            'priority_support' => 'Priorytetowe wsparcie',
            'advanced_dashboard' => 'Zaawansowany panel',
            'api_access' => 'Dostęp do API',
            'white_label' => 'White-label rozwiązania',
            'team_accounts' => 'Konta zespołowe',
            'custom_integrations' => 'Niestandardowe integracje',
        ];

        return collect($this->features ?? [])
            ->mapWithKeys(fn($feature) => [$feature => $defaultFeatures[$feature] ?? $feature])
            ->toArray();
    }

    public static function getDefaultPlans(): array
    {
        return [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Idealny na start - podstawowe funkcjonalności',
                'price' => 0.00,
                'billing_period' => 'monthly',
                'max_listings' => 3,
                'features' => ['basic_search', 'listings', 'messaging', 'reviews', 'basic_support'],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Dla aktywnych użytkowników',
                'price' => 49.00,
                'billing_period' => 'monthly',
                'max_listings' => null,
                'features' => ['basic_search', 'listings', 'messaging', 'reviews', 'priority_search', 'unlimited_listings', 'advanced_search', 'analytics', 'verified_badge'],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Pełne możliwości platformy',
                'price' => 99.00,
                'billing_period' => 'monthly',
                'max_listings' => null,
                'features' => ['basic_search', 'listings', 'messaging', 'reviews', 'priority_search', 'unlimited_listings', 'advanced_search', 'analytics', 'verified_badge', 'ai_matching', 'promoted_listings', 'priority_support', 'advanced_dashboard'],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Dla firm i profesjonalistów',
                'price' => 199.00,
                'billing_period' => 'monthly',
                'max_listings' => null,
                'features' => ['basic_search', 'listings', 'messaging', 'reviews', 'priority_search', 'unlimited_listings', 'advanced_search', 'analytics', 'verified_badge', 'ai_matching', 'promoted_listings', 'priority_support', 'advanced_dashboard', 'api_access', 'team_accounts', 'custom_integrations'],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];
    }
}