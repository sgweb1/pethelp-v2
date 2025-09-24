<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Model reprezentujący usługę opieki nad zwierzętami.
 *
 * Zawiera informacje o usługach oferowanych przez opiekunów, takich jak
 * spacery, opieka noclegowa, wizyty domowe itp. Obsługuje zaawansowane
 * opcje cenowe, różne typy zwierząt i lokalizacje świadczenia usług.
 *
 * @package App\Models
 * @author Claude AI Assistant
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator usługi
 * @property int $sitter_id ID użytkownika oferującego usługę
 * @property int|null $category_id ID kategorii usługi
 * @property string $title Tytuł/nazwa usługi
 * @property string $slug Slug URL usługi
 * @property string|null $description Szczegółowy opis usługi
 * @property decimal|null $price_per_hour Cena za godzinę
 * @property decimal|null $price_per_day Cena za dzień
 * @property decimal|null $price_per_visit Cena za wizytę
 * @property decimal|null $price_per_week Cena za tydzień
 * @property decimal|null $price_per_month Cena za miesiąc
 * @property array|null $pet_types Akceptowane typy zwierząt
 * @property array|null $pet_sizes Akceptowane rozmiary zwierząt
 * @property bool $home_service Czy świadczy usługi u klienta
 * @property bool $sitter_home Czy świadczy usługi u siebie
 * @property int|null $max_pets Maksymalna liczba zwierząt jednocześnie
 * @property bool $is_active Czy usługa jest aktywna
 * @property array|null $metadata Dodatkowe metadane usługi
 * @property int|null $service_radius Promień świadczenia usług w km
 * @property \Carbon\Carbon $created_at Data utworzenia usługi
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 *
 * @property-read \App\Models\User $sitter Użytkownik oferujący usługę
 * @property-read \App\Models\ServiceCategory|null $category Kategoria usługi
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Booking> $bookings Rezerwacje usługi
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Review> $reviews Opinie o usłudze
 * @property-read string $display_price Sformatowana cena do wyświetlenia
 * @property-read array $service_types Typy lokalizacji świadczenia usługi
 * @property-read float $average_rating Średnia ocena usługi
 * @property-read int $reviews_count Liczba opinii
 *
 * @method static \App\Models\Service create(array $attributes = []) Tworzy nową usługę
 * @method static \Illuminate\Database\Eloquent\Builder active() Filtruje aktywne usługi
 * @method static \Illuminate\Database\Eloquent\Builder byLocation(float $lat, float $lng, int $radius) Filtruje w promieniu geograficznym
 * @method static \Illuminate\Database\Eloquent\Builder byPetType(string $petType) Filtruje akceptujące typ zwierzęcia
 * @method static \Illuminate\Database\Eloquent\Builder byPetSize(string $petSize) Filtruje akceptujące rozmiar zwierzęcia
 * @method static \Illuminate\Database\Eloquent\Builder byPriceRange(?float $minPrice, ?float $maxPrice, string $priceType) Filtruje po przedziale cenowym
 * @method static \Illuminate\Database\Eloquent\Builder withAvgRating() Ładuje średnią ocenę
 * @method static \Illuminate\Database\Eloquent\Builder minRating(float $minRating) Filtruje po minimalnej ocenie
 */
class Service extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = static::generateUniqueSlug($service->title);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('title') && empty($service->slug)) {
                $service->slug = static::generateUniqueSlug($service->title, $service->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'pet_types' => 'array',
            'pet_sizes' => 'array',
            'home_service' => 'boolean',
            'sitter_home' => 'boolean',
            'is_active' => 'boolean',
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'price_per_visit' => 'decimal:2',
            'price_per_week' => 'decimal:2',
            'price_per_month' => 'decimal:2',
            'weekend_surcharge_percent' => 'decimal:2',
            'holiday_surcharge_percent' => 'decimal:2',
            'early_morning_surcharge_percent' => 'decimal:2',
            'late_evening_surcharge_percent' => 'decimal:2',
            'bulk_discount_threshold' => 'decimal:2',
            'bulk_discount_percent' => 'decimal:2',
            'long_term_discount_days' => 'integer',
            'long_term_discount_percent' => 'decimal:2',
            'additional_services_pricing' => 'array',
            'free_consultation' => 'boolean',
            'free_trial_visit' => 'boolean',
            'cancellation_hours' => 'integer',
            'cancellation_fee_percent' => 'decimal:2',
            'metadata' => 'array',
            'allow_mixed_pet_types' => 'boolean',
            'requires_consultation' => 'boolean',
            'emergency_contact' => 'boolean',
            'insurance_coverage' => 'boolean',
            'vaccination_requirements' => 'boolean',
        ];
    }

    protected $fillable = [
        'sitter_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price_per_hour',
        'price_per_day',
        'price_per_visit',
        'price_per_week',
        'price_per_month',
        'weekend_surcharge_percent',
        'holiday_surcharge_percent',
        'early_morning_surcharge_percent',
        'late_evening_surcharge_percent',
        'bulk_discount_threshold',
        'bulk_discount_percent',
        'long_term_discount_days',
        'long_term_discount_percent',
        'additional_services_pricing',
        'free_consultation',
        'free_trial_visit',
        'payment_method',
        'cancellation_hours',
        'cancellation_fee_percent',
        'pet_types',
        'pet_sizes',
        'home_service',
        'sitter_home',
        'max_pets',
        'is_active',
        'metadata',
        'service_radius',
        'allow_mixed_pet_types',
        'minimum_duration',
        'maximum_duration',
        'price_structure',
        'requires_consultation',
        'emergency_contact',
        'experience_years',
        'insurance_coverage',
        'vaccination_requirements',
    ];

    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }

    // Search Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation(Builder $query, float $lat, float $lng, int $radius = 10): Builder
    {
        return $query->whereHas('sitter.locations', function ($q) use ($lat, $lng, $radius) {
            $q->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
              ->having('distance', '<=', $radius)
              ->orderBy('distance');
        });
    }

    public function scopeByPetType(Builder $query, string $petType): Builder
    {
        return $query->whereJsonContains('pet_types', $petType);
    }

    public function scopeByPetSize(Builder $query, string $petSize): Builder
    {
        return $query->whereJsonContains('pet_sizes', $petSize);
    }

    public function scopeByServiceType(Builder $query, string $serviceType): Builder
    {
        if ($serviceType === 'home_service') {
            return $query->where('home_service', true);
        }
        if ($serviceType === 'sitter_home') {
            return $query->where('sitter_home', true);
        }
        return $query;
    }

    public function scopeByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice, string $priceType = 'hour'): Builder
    {
        $column = $priceType === 'day' ? 'price_per_day' : 'price_per_hour';

        if ($minPrice !== null) {
            $query->where($column, '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where($column, '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeWithAvgRating(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating');
    }

    public function scopeMinRating(Builder $query, float $minRating): Builder
    {
        return $query->withAvgRating()
                     ->having('reviews_avg_rating', '>=', $minRating);
    }

    // Helper Methods
    public function getDisplayPriceAttribute(): string
    {
        $formatPrice = function($price) {
            return number_format($price, $price == intval($price) ? 0 : 2, ',', ' ');
        };

        if ($this->price_per_hour && $this->price_per_day) {
            return "od {$formatPrice($this->price_per_hour)}zł/h ({$formatPrice($this->price_per_day)}zł/dzień)";
        }
        if ($this->price_per_hour) {
            return "{$formatPrice($this->price_per_hour)}zł/h";
        }
        if ($this->price_per_day) {
            return "{$formatPrice($this->price_per_day)}zł/dzień";
        }
        return 'Do uzgodnienia';
    }

    public function getServiceTypesAttribute(): array
    {
        $types = [];
        if ($this->home_service) $types[] = 'U klienta';
        if ($this->sitter_home) $types[] = 'U opiekuna';
        return $types;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews_avg_rating ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
