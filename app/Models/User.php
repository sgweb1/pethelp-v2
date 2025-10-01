<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model reprezentujący użytkownika aplikacji PetHelp.
 *
 * Reprezentuje zarówno właścicieli zwierząt jak i opiekunów oferujących swoje usługi.
 * Zawiera podstawowe dane użytkownika, profil, relacje do zwierząt i usług.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator użytkownika
 * @property string $name Pełne imię i nazwisko użytkownika
 * @property string $email Adres email używany do logowania
 * @property \Carbon\Carbon|null $email_verified_at Data weryfikacji adresu email
 * @property string $password Zahashowane hasło użytkownika
 * @property string|null $remember_token Token do zapamiętania sesji
 * @property \Carbon\Carbon|null $premium_until Data wygaśnięcia statusu premium
 * @property \Carbon\Carbon $created_at Data utworzenia konta
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 * @property-read \App\Models\UserProfile|null $profile Profil użytkownika z dodatkowymi danymi
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Pet> $pets Zwierzęta należące do użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Service> $services Usługi oferowane przez użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Location> $locations Lokalizacje użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Booking> $ownerBookings Rezerwacje złożone przez użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Booking> $sitterBookings Rezerwacje dla usług użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Review> $reviewsGiven Opinie wystawione przez użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Review> $reviewsReceived Opinie otrzymane przez użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Availability> $availability Dostępności czasowe użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Notification> $notifications Powiadomienia użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Conversation> $conversations Rozmowy użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Message> $sentMessages Wysłane wiadomości
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Subscription> $subscriptions Subskrypcje użytkownika
 * @property-read \App\Models\Subscription|null $activeSubscription Aktywna subskrypcja
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Advertisement> $advertisements Ogłoszenia użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Event> $events Wydarzenia użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Photo> $photos Zdjęcia użytkownika
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\ProfessionalService> $professionalServices Profesjonalne usługi
 *
 * @method static \App\Models\User create(array $attributes = []) Tworzy nowego użytkownika
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_until' => 'datetime',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'premium_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relacja do profilu użytkownika.
     *
     * Każdy użytkownik może mieć jeden rozszerzony profil zawierający
     * dodatkowe informacje takie jak opis, lokalizacja, zdjęcie itp.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\UserProfile>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relacja do zwierząt użytkownika.
     *
     * Użytkownik może być właścicielem wielu zwierząt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Pet>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    /**
     * Relacja do usług oferowanych przez użytkownika.
     *
     * Użytkownik może oferować różne typy usług opieki nad zwierzętami.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Service>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'sitter_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function ownerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }

    public function sitterBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'sitter_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class, 'sitter_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Sprawdza czy użytkownik jest właścicielem zwierząt.
     *
     * @return bool True jeśli użytkownik ma rolę owner lub both
     *
     * @example
     * if ($user->isOwner()) {
     *     // Pokaż funkcje dla właścicieli
     * }
     */
    public function isOwner(): bool
    {
        return in_array($this->profile?->role, ['owner', 'both']);
    }

    /**
     * Sprawdza czy użytkownik jest opiekunem zwierząt.
     *
     * @return bool True jeśli użytkownik ma rolę sitter lub both
     *
     * @example
     * if ($user->isSitter()) {
     *     // Pokaż funkcje dla opiekunów
     * }
     */
    public function isSitter(): bool
    {
        return in_array($this->profile?->role, ['sitter', 'both']);
    }

    public function isAdmin(): bool
    {
        return $this->profile?->role === 'admin';
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user_one_id')
            ->orWhere('user_two_id', $this->id);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', Subscription::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function professionalServices(): HasMany
    {
        return $this->hasMany(ProfessionalService::class);
    }

    public function getUnreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function ($query) {
            $query->forUser($this->id);
        })->forReceiver($this->id)->unread()->count();
    }

    /**
     * Zwraca liczbę nieprzeczytanych powiadomień użytkownika.
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function hasFeature(string $feature): bool
    {
        $subscription = $this->activeSubscription;

        return $subscription && $subscription->hasFeature($feature);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription !== null;
    }

    public function canCreateListing(): bool
    {
        $subscription = $this->activeSubscription;

        if (! $subscription) {
            return $this->advertisements()->count() < 3; // Basic plan limit
        }

        if ($subscription->hasUnlimitedListings()) {
            return true;
        }

        return $this->advertisements()->count() < $subscription->subscriptionPlan->max_listings;
    }

    /**
     * Sprawdza czy użytkownik ma aktywny status premium.
     *
     * @return bool True jeśli użytkownik ma aktywny premium
     *
     * @example
     * if ($user->isPremium()) {
     *     // Pokaż funkcje premium
     * }
     */
    public function isPremium(): bool
    {
        return $this->premium_until && $this->premium_until->isFuture();
    }

    public function getMaxAvailabilitySlots(): int
    {
        return $this->isPremium() ? 6 : 3;
    }

    /**
     * Pobiera aktualny plan subskrypcji użytkownika.
     *
     * @return \App\Models\SubscriptionPlan|null Aktualny plan subskrypcji lub null jeśli brak
     *
     * @example
     * $currentPlan = $user->currentSubscriptionPlan();
     * if ($currentPlan && $currentPlan->isPro()) {
     *     // Użytkownik ma plan Pro
     * }
     */
    public function currentSubscriptionPlan(): ?SubscriptionPlan
    {
        $subscription = $this->activeSubscription;

        if ($subscription) {
            return $subscription->subscriptionPlan;
        }

        // Jeśli nie ma aktywnej subskrypcji, zwróć plan Basic (darmowy)
        return SubscriptionPlan::where('slug', 'basic')->first();
    }

    /**
     * Sprawdza czy użytkownik jest obecnie w trybie urlopowym.
     *
     * @return bool True jeśli użytkownik ma aktywny urlop
     */
    public function isOnVacation(): bool
    {
        return $this->availability()
            ->where('is_available', false)
            ->whereNotNull('vacation_end_date')
            ->where('available_date', '<=', now()->toDateString())
            ->where('vacation_end_date', '>=', now()->toDateString())
            ->exists();
    }

    /**
     * Pobiera szczegóły aktualnego urlopu użytkownika.
     *
     * @return \App\Models\Availability|null Szczegóły urlopu lub null jeśli nie ma urlopu
     */
    public function getCurrentVacation(): ?Availability
    {
        return $this->availability()
            ->where('is_available', false)
            ->whereNotNull('vacation_end_date')
            ->where('available_date', '<=', now()->toDateString())
            ->where('vacation_end_date', '>=', now()->toDateString())
            ->first();
    }
}
