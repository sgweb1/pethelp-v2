# System Subskrypcji Premium - PetHelp

## Spis treści
1. [Przegląd systemu](#przegląd-systemu)
2. [Architektura](#architektura)
3. [Modele danych](#modele-danych)
4. [Plany subskrypcji](#plany-subskrypcji)
5. [Kontrola dostępu](#kontrola-dostępu)
6. [Integracja PayU](#integracja-payu)
7. [API i endpointy](#api-i-endpointy)
8. [Komponenty UI](#komponenty-ui)
9. [Testy](#testy)
10. [Konfiguracja](#konfiguracja)
11. [Deployment](#deployment)

## Przegląd systemu

System subskrypcji premium umożliwia monetyzację platformy PetHelp poprzez model SaaS z planami miesięcznymi/rocznymi. Zaprojektowany zgodnie z polskimi wymogami prawnymi i księgowymi.

### Główne funkcjonalności:
- 4-poziomowy system planów (Basic, Pro, Premium, Business)
- Kontrola dostępu oparta na funkcjach (feature-based access control)
- Integracja z PayU dla płatności online
- Limity ogłoszeń według planu
- Dashboard zarządzania subskrypcją
- Automatyczne odnowienia i zarządzanie cyklem życia

### Korzyści biznesowe:
- Przewidywalne przychody (MRR/ARR)
- Prostsze rozliczenia VAT w Polsce
- Skalowalna struktura cenowa
- Łatwe dodawanie nowych funkcji premium

## Architektura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   PayU API      │
│                 │    │                 │    │                 │
│ • Pricing Page  │◄──►│ • Controllers   │◄──►│ • Payment       │
│ • Dashboard     │    │ • Services      │    │ • Webhooks      │
│ • Components    │    │ • Middleware    │    │ • OAuth         │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │    Database     │
                    │                 │
                    │ • Users         │
                    │ • Subscriptions │
                    │ • Plans         │
                    │ • Payments      │
                    └─────────────────┘
```

### Komponenty systemu:

#### Backend (Laravel)
- **Modele**: `User`, `Subscription`, `SubscriptionPlan`, `Payment`
- **Middleware**: `RequiresFeature`, `RequiresActiveSubscription`, `CheckListingLimits`
- **Serwisy**: `PayUService` - integracja z API PayU
- **Kontrolery**: `PaymentController` - obsługa płatności i subskrypcji

#### Frontend (Livewire + Blade)
- **Komponenty Livewire**: `PricingPage`, `Dashboard`
- **Komponenty Blade**: `pricing-card`, `feature-badge`, `upgrade-prompt`, `usage-meter`
- **Layouts**: responsywne, dark mode, accessibility

#### Integracje zewnętrzne
- **PayU**: płatności online, webhooks, OAuth
- **Laravel Gates**: autoryzacja funkcji premium

## Modele danych

### SubscriptionPlan
Plan subskrypcji z konfiguracją funkcji i cen.

```php
// Atrybuty
id: integer
name: string              // Nazwa planu (Basic, Pro, Premium, Business)
slug: string              // Slug dla URL (basic, pro, premium, business)
description: text         // Opis planu
price: decimal(8,2)       // Cena w PLN
billing_period: string    // monthly, yearly
max_listings: integer     // Limit ogłoszeń (null = unlimited)
features: json            // Lista dostępnych funkcji
is_popular: boolean       // Wyróżnienie "Najpopularniejszy"
is_active: boolean        // Status aktywności planu
sort_order: integer       // Kolejność wyświetlania

// Relacje
subscriptions: hasMany(Subscription)

// Metody
hasFeature(string $feature): bool
getFormattedPriceAttribute(): string
getMonthlyPriceAttribute(): float
getFeatureListAttribute(): array
```

### Subscription
Aktywna subskrypcja użytkownika.

```php
// Atrybuty
id: integer
user_id: integer          // Właściciel subskrypcji
subscription_plan_id: int // Plan subskrypcji
status: string            // active, cancelled, expired, paused, pending
price: decimal(8,2)       // Cena w momencie zakupu
billing_period: string    // monthly, yearly
starts_at: timestamp      // Data rozpoczęcia
ends_at: timestamp        // Data zakończenia
cancelled_at: timestamp   // Data anulowania
last_payment_at: timestamp // Ostatnia płatność
next_billing_at: timestamp // Następna płatność
payment_method: string    // Metoda płatności
external_id: string       // ID w systemie płatności
metadata: json            // Dodatkowe dane

// Relacje
user: belongsTo(User)
subscriptionPlan: belongsTo(SubscriptionPlan)
payments: hasMany(Payment)

// Statusy
const STATUS_ACTIVE = 'active'
const STATUS_CANCELLED = 'cancelled'
const STATUS_EXPIRED = 'expired'
const STATUS_PAUSED = 'paused'
const STATUS_PENDING = 'pending'

// Metody
isActive(): bool
isExpired(): bool
isCancelled(): bool
isPending(): bool
hasFeature(string $feature): bool
cancel(): bool
resume(): bool
renew(): bool
canBeCancelled(): bool
canBeResumed(): bool
```

### Payment
Rejestr płatności i transakcji.

```php
// Atrybuty
id: integer
user_id: integer          // Płacący użytkownik
subscription_id: integer  // Powiązana subskrypcja
type: string              // subscription, addon, boost, verification
status: string            // pending, completed, failed, refunded
amount: decimal(8,2)      // Kwota w PLN
currency: string          // Waluta (PLN)
payment_method: string    // payu, stripe, bank_transfer
external_id: string       // ID transakcji w systemie płatności
external_status: string   // Status z systemu płatności
description: text         // Opis płatności
payment_data: json        // Dane z gateway
paid_at: timestamp        // Data realizacji płatności
failed_at: timestamp      // Data niepowodzenia
refunded_at: timestamp    // Data zwrotu
failure_reason: text      // Powód niepowodzenia

// Relacje
user: belongsTo(User)
subscription: belongsTo(Subscription)
```

### User (rozszerzenia)
Dodane metody związane z subskrypcją.

```php
// Nowe relacje
subscriptions: hasMany(Subscription)
activeSubscription: hasOne(Subscription) // Aktywna subskrypcja
advertisements: hasMany(Advertisement)

// Nowe metody
hasFeature(string $feature): bool
hasActiveSubscription(): bool
canCreateListing(): bool
```

## Plany subskrypcji

### Basic (Darmowy)
```
Cena: 0 PLN/miesiąc
Limit ogłoszeń: 3
Funkcje:
- basic_search (Podstawowe wyszukiwanie)
- listings (Ogłoszenia)
- messaging (Wiadomości)
- reviews (Opinie i oceny)
- basic_support (Podstawowe wsparcie)
```

### Pro
```
Cena: 49 PLN/miesiąc
Limit ogłoszeń: Nielimitowane
Funkcje:
- Wszystkie z Basic +
- priority_search (Priorytet w wyszukiwaniu)
- unlimited_listings (Nielimitowane ogłoszenia)
- advanced_search (Zaawansowane wyszukiwanie)
- analytics (Analityka i statystyki)
- verified_badge (Badge "Zweryfikowany")
```

### Premium (Najpopularniejszy)
```
Cena: 99 PLN/miesiąc
Limit ogłoszeń: Nielimitowane
Funkcje:
- Wszystkie z Pro +
- ai_matching (AI-powered matching)
- promoted_listings (Promowane ogłoszenia)
- priority_support (Priorytetowe wsparcie)
- advanced_dashboard (Zaawansowany panel)
```

### Business
```
Cena: 199 PLN/miesiąc
Limit ogłoszeń: Nielimitowane
Funkcje:
- Wszystkie z Premium +
- api_access (Dostęp do API)
- white_label (White-label rozwiązania)
- team_accounts (Konta zespołowe)
- custom_integrations (Niestandardowe integracje)
```

## Kontrola dostępu

### Middleware

#### RequiresFeature
Sprawdza czy użytkownik ma dostęp do konkretnej funkcji.

```php
// Użycie w routach
Route::middleware('requires.feature:advanced_search')->group(function () {
    Route::get('/search/advanced', [SearchController::class, 'advanced']);
});

// Użycie w kontrolerach
public function __construct() {
    $this->middleware('requires.feature:analytics');
}
```

#### RequiresActiveSubscription
Wymaga aktywnej subskrypcji (dowolnej płatnej).

```php
Route::middleware('requires.subscription')->group(function () {
    Route::get('/premium-dashboard', [DashboardController::class, 'premium']);
});
```

#### CheckListingLimits
Sprawdza limity ogłoszeń przed utworzeniem nowego.

```php
Route::middleware('check.listing.limits')->group(function () {
    Route::post('/advertisements', [AdvertisementController::class, 'store']);
});
```

### Blade Directives

```blade
{{-- Sprawdzenie dostępu do funkcji --}}
@hasFeature('advanced_search')
    <button>Zaawansowane wyszukiwanie</button>
@endhasFeature

{{-- Sprawdzenie aktywnej subskrypcji --}}
@hasActiveSubscription
    <div class="premium-content">...</div>
@endhasActiveSubscription

{{-- Sprawdzenie możliwości dodania ogłoszenia --}}
@canCreateListing
    <a href="/advertisements/create">Dodaj ogłoszenie</a>
@else
    <x-subscription.upgrade-prompt feature="unlimited_listings" />
@endcanCreateListing

{{-- Sprawdzenie konkretnego planu --}}
@subscriptionPlan('premium')
    <span class="badge">Premium User</span>
@endsubscriptionPlan
```

### Laravel Gates

```php
// Sprawdzenie w kontrolerach
if (Gate::allows('access-analytics')) {
    // Dostęp do analityki
}

// Sprawdzenie w Blade
@can('access-promoted-listings')
    <button>Promuj ogłoszenie</button>
@endcan

// Dostępne Gates:
- create-listing
- access-feature (z parametrem)
- access-advanced-search
- access-analytics
- access-promoted-listings
- access-ai-matching
- access-priority-support
- access-api
```

### Trait HasSubscriptionChecks

```php
use App\Traits\HasSubscriptionChecks;

class MyController extends Controller {
    use HasSubscriptionChecks;

    public function someMethod() {
        if (!$this->requiresFeature('analytics')) {
            return response()->json($this->getSubscriptionError('analytics'), 403);
        }

        $userInfo = $this->getUserSubscriptionInfo();
        // ...
    }
}
```

## Integracja PayU

### Konfiguracja

```php
// config/payu.php
return [
    'environment' => env('PAYU_ENVIRONMENT', 'sandbox'),
    'merchant_id' => env('PAYU_MERCHANT_ID'),
    'secret_key' => env('PAYU_SECRET_KEY'),
    'oauth_client_id' => env('PAYU_OAUTH_CLIENT_ID'),
    'oauth_client_secret' => env('PAYU_OAUTH_CLIENT_SECRET'),
    'currency' => 'PLN',
    'vat_rate' => 0.23,
];
```

### Zmienne środowiskowe

```env
# PayU Configuration
PAYU_ENVIRONMENT=sandbox
PAYU_MERCHANT_ID=your_merchant_id
PAYU_SECRET_KEY=your_secret_key
PAYU_OAUTH_CLIENT_ID=your_client_id
PAYU_OAUTH_CLIENT_SECRET=your_client_secret
```

### Flow płatności

1. **Inicjalizacja płatności**
   ```php
   $payuService = app(PayUService::class);
   $result = $payuService->createSubscriptionPayment($user, $plan);

   if ($result['success']) {
       return redirect($result['redirect_url']);
   }
   ```

2. **Webhook PayU**
   - PayU wysyła notyfikacje na endpoint `/payu/notify`
   - System automatycznie aktualizuje status płatności
   - Po udanej płatności tworzona jest subskrypcja

3. **Powrót użytkownika**
   - Sukces: redirect na `/subscription/payment/success`
   - Anulowanie: redirect na `/subscription/payment/cancel`

### PayUService API

```php
// Tworzenie płatności za subskrypcję
createSubscriptionPayment(User $user, SubscriptionPlan $plan): array

// Obsługa notyfikacji PayU
handleNotification(array $data): bool

// Weryfikacja sygnatury
verifySignature(array $data, string $signature): bool
```

## API i endpointy

### Publiczne endpointy

```
GET  /subscription/plans           - Strona z planami cenowymi
POST /payu/notify                  - Webhook PayU (bez auth)
```

### Endpointy wymagające logowania

```
GET  /subscription/dashboard       - Dashboard subskrypcji
POST /subscription/subscribe/{plan} - Subskrypcja planu
GET  /subscription/payment/success - Strona potwierdzenia płatności
GET  /subscription/payment/cancel  - Strona anulowania płatności
GET  /subscription/payments/{payment}/status - Status płatności (JSON)
```

### API Responses

#### Sukces subskrypcji (darmowy plan)
```json
{
    "success": true,
    "message": "Plan podstawowy został aktywowany",
    "subscription_id": 123
}
```

#### Płatność PayU
```json
{
    "success": true,
    "payment_id": 456,
    "redirect_url": "https://secure.snd.payu.com/pay/12345",
    "order_id": "PAYU_ORDER_123"
}
```

#### Błąd dostępu do funkcji
```json
{
    "message": "Ta funkcja jest dostępna tylko w planach premium.",
    "feature": "advanced_search",
    "upgrade_url": "/subscription/plans"
}
```

#### Status płatności
```json
{
    "status": "completed",
    "external_status": "COMPLETED",
    "amount": 49.00,
    "currency": "PLN",
    "created_at": "2025-01-19T10:30:00Z",
    "paid_at": "2025-01-19T10:35:00Z",
    "failed_at": null,
    "failure_reason": null
}
```

## Komponenty UI

### Komponenty Blade

#### pricing-card
Karta planu subskrypcji z cenami i funkcjami.

```blade
<x-subscription.pricing-card
    :plan="$plan"
    :is-popular="true"
    :current-plan="false"
/>
```

#### feature-badge
Badge pokazujący status funkcji premium.

```blade
<x-subscription.feature-badge
    feature="advanced_search"
    :show-upgrade="true"
    size="sm"
/>
```

#### upgrade-prompt
Zachęta do aktualizacji planu z listą korzyści.

```blade
<x-subscription.upgrade-prompt
    feature="analytics"
    title="Potrzebujesz analityki?"
    description="Uzyskaj dostęp do szczegółowych statystyk i raportów."
    :show-plans="true"
/>
```

#### usage-meter
Meter pokazujący wykorzystanie limitów (np. ogłoszenia).

```blade
<x-subscription.usage-meter
    :current="3"
    :limit="5"
    label="Ogłoszenia"
    :show-upgrade="true"
/>
```

### Komponenty Livewire

#### PricingPage
Kompletna strona z planami, porównaniem funkcji i FAQ.

```php
Route::get('/subscription/plans', \App\Livewire\Subscription\PricingPage::class)
    ->name('subscription.plans');
```

#### Dashboard
Panel zarządzania subskrypcją z historią płatności.

```php
Route::get('/subscription/dashboard', \App\Livewire\Subscription\Dashboard::class)
    ->name('subscription.dashboard')->middleware('auth');
```

### Przykłady implementacji

#### Ograniczenie funkcji w komponencie
```blade
{{-- resources/views/livewire/search.blade.php --}}
<div>
    @hasFeature('advanced_search')
        <!-- Zaawansowane filtry -->
        <div class="advanced-filters">
            <!-- ... -->
        </div>
    @else
        <x-subscription.feature-badge feature="advanced_search" />
    @endhasFeature
</div>
```

#### Sprawdzenie limitów w kontrolerze
```php
class AdvertisementController extends Controller
{
    use HasSubscriptionChecks;

    public function create()
    {
        if (!$this->canCreateListing()) {
            return redirect()->route('subscription.plans')
                ->with('error', $this->getListingLimitError()['message']);
        }

        return view('advertisements.create');
    }
}
```

## Testy

### Uruchamianie testów

```bash
# Wszystkie testy subskrypcji
php artisan test --filter=Subscription

# Konkretne grupy testów
php artisan test tests/Feature/SubscriptionTest.php
php artisan test tests/Feature/SubscriptionMiddlewareTest.php
php artisan test tests/Feature/PaymentControllerTest.php
```

### Pokrycie testów

#### SubscriptionTest.php
- Tworzenie subskrypcji z planu
- Sprawdzanie statusów (aktywna/wygasła)
- Anulowanie i wznawianie subskrypcji
- Dostęp do funkcji premium
- Limity ogłoszeń
- Odnowienia subskrypcji

#### SubscriptionMiddlewareTest.php
- Middleware `RequiresFeature`
- Middleware `RequiresActiveSubscription`
- Middleware `CheckListingLimits`
- Obsługa requestów JSON i web

#### PaymentControllerTest.php
- Subskrypcja planów darmowych i płatnych
- Integracja z PayU (z mock'ami)
- Obsługa webhooków
- Strony success/cancel
- Autoryzacja dostępu do płatności
- UI komponenty (PricingPage, Dashboard)

### Przykłady testów

```php
test('user can subscribe to free plan', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create(['price' => 0.00]);

    $response = $this->actingAs($user)
        ->post(route('subscription.subscribe', $plan));

    $response->assertRedirect(route('subscription.dashboard'));
    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
});

test('middleware blocks access without feature', function () {
    $user = User::factory()->create();
    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresFeature();
    $response = $middleware->handle(
        $request,
        fn() => new Response('success'),
        'advanced_search'
    );

    expect($response->getStatusCode())->toBe(302);
});
```

## Konfiguracja

### Wymagane zależności

```json
{
    "require": {
        "laravel/framework": "^11.0",
        "livewire/livewire": "^3.0",
        "guzzlehttp/guzzle": "^7.0"
    }
}
```

### Migracje

```bash
# Uruchomienie migracji subskrypcji
php artisan migrate

# Konkretne migracje (w kolejności)
php artisan migrate --path=/database/migrations/2025_09_19_101139_create_subscription_plans_table.php
php artisan migrate --path=/database/migrations/2025_09_19_101154_create_subscriptions_table.php
php artisan migrate --path=/database/migrations/2025_09_19_101208_create_payments_table.php
```

### Seeders

```bash
# Utworzenie domyślnych planów
php artisan db:seed --class=SubscriptionPlanSeeder

# Lub dodanie w DatabaseSeeder
$this->call([
    SubscriptionPlanSeeder::class,
]);
```

### Cache i Queue

```bash
# Czyszczenie cache po zmianach
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Kolejki dla procesowania płatności (opcjonalne)
php artisan queue:work
```

## Deployment

### Zmienne środowiskowe (production)

```env
# PayU Production
PAYU_ENVIRONMENT=secure
PAYU_MERCHANT_ID=production_merchant_id
PAYU_SECRET_KEY=production_secret_key
PAYU_OAUTH_CLIENT_ID=production_client_id
PAYU_OAUTH_CLIENT_SECRET=production_client_secret

# URLs
APP_URL=https://pethelp.pl

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pethelp_production
DB_USERNAME=pethelp_user
DB_PASSWORD=secure_password

# Queue (dla dużego ruchu)
QUEUE_CONNECTION=redis
```

### Deployment checklist

#### Przed wdrożeniem:
- [ ] Przetestuj wszystkie funkcje w środowisku sandbox PayU
- [ ] Sprawdź poprawność konfiguracji VAT (23%)
- [ ] Zweryfikuj URLs webhook'ów w panelu PayU
- [ ] Przetestuj flow płatności end-to-end
- [ ] Uruchom pełny zestaw testów

#### Po wdrożeniu:
- [ ] Zweryfikuj działanie webhook'ów w production
- [ ] Przetestuj płatność kartą testową
- [ ] Sprawdź logi PayU w panelu
- [ ] Skonfiguruj monitoring płatności
- [ ] Ustaw alerty dla nieudanych płatności

### Monitoring i logowanie

```php
// Monitorowanie ważnych eventów
Log::info('Subscription activated', [
    'user_id' => $user->id,
    'plan' => $plan->slug,
    'payment_id' => $payment->id
]);

Log::warning('Payment failed', [
    'user_id' => $user->id,
    'plan_id' => $plan->id,
    'error' => $error->getMessage()
]);
```

### Backup i recovery

```bash
# Backup bazy danych z subskrypcjami
mysqldump pethelp_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Monitoring ważnych tabel
# - subscriptions (aktywne subskrypcje)
# - payments (historia płatności)
# - subscription_plans (konfiguracja planów)
```

---

## Wsparcie i rozwój

### Dodawanie nowych funkcji premium

1. **Dodaj funkcję do planów**:
```php
// W seederze lub migration
$plan->update([
    'features' => array_merge($plan->features, ['new_feature'])
]);
```

2. **Zaimplementuj kontrolę dostępu**:
```php
Route::middleware('requires.feature:new_feature')->group(function () {
    // Chronione endpointy
});
```

3. **Dodaj do UI**:
```blade
@hasFeature('new_feature')
    <!-- Nowa funkcja -->
@else
    <x-subscription.feature-badge feature="new_feature" />
@endhasFeature
```

### Troubleshooting

#### Problem: Webhook PayU nie działa
```bash
# Sprawdź logi
tail -f storage/logs/laravel.log | grep PayU

# Sprawdź URL w konfiguracji PayU
echo config('payu.notify_url')
```

#### Problem: Użytkownik ma błędny dostęp do funkcji
```bash
# Debug w tinker
php artisan tinker
$user = User::find(1);
$user->hasFeature('advanced_search');
$user->activeSubscription;
```

#### Problem: Płatność się nie potwierdza
1. Sprawdź status w panelu PayU
2. Zweryfikuj zewnętrzne ID w tabeli payments
3. Sprawdź logi webhook'ów

### Kontakt
- **Dokumentacja**: Ten plik
- **Testy**: `tests/Feature/*Subscription*`
- **Konfiguracja**: `config/payu.php`
- **Migracje**: `database/migrations/*subscription*`