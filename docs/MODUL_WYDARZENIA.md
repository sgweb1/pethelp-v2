# Moduł Wydarzenia "Spotkajmy się" - Dokumentacja Techniczna

## Przegląd Systemu

Moduł "Spotkajmy się" to zaawansowany system organizacji i zarządzania wydarzeniami związanymi ze zwierzętami w aplikacji PetHelp. Umożliwia użytkownikom tworzenie, odkrywanie i uczestniczenie w różnorodnych spotkaniach dla właścicieli zwierząt.

## Architektura Bazy Danych

### Tabele i Relacje

#### 1. `event_types` - Typy Wydarzeń
```sql
- id (bigint, primary key)
- name (varchar 100) - nazwa typu (np. "Spacer", "Sesja treningowa")
- slug (varchar 100, unique) - unikalny identyfikator URL
- description (text) - szczegółowy opis typu wydarzenia
- icon (varchar 50) - ikona Heroicon
- color (varchar 7) - kolor HEX dla UI
- is_active (boolean, default true) - czy typ jest aktywny
- sort_order (int, default 0) - kolejność wyświetlania
- timestamps
```

**Indeksy wydajności:**
- `name` - wyszukiwanie po nazwie
- `is_active` - filtrowanie aktywnych typów
- `sort_order` - sortowanie
- `is_active + sort_order` - kompozytowy dla listy aktywnych

#### 2. `events` - Wydarzenia
```sql
- id (bigint, primary key)
- user_id (foreign key) - organizator wydarzenia
- event_type_id (foreign key) - typ wydarzenia
- title (varchar 200) - tytuł wydarzenia
- description (text) - szczegółowy opis
- starts_at (datetime) - data i czas rozpoczęcia
- ends_at (datetime, nullable) - data i czas zakończenia
- max_participants (smallint, nullable) - maksymalna liczba uczestników
- entry_fee (decimal 8,2, default 0) - opłata za udział
- currency (varchar 3, default 'PLN') - waluta
- is_invitation_only (boolean, default false) - tylko na zaproszenia
- status (enum: draft,published,cancelled,completed)
- is_featured (boolean, default false) - wydarzenie wyróżnione
- registration_deadline (datetime, nullable) - termin rejestracji
- allow_waiting_list (boolean, default true) - lista oczekujących
- current_participants (int, default 0) - obecna liczba uczestników
- view_count (int, default 0) - liczba wyświetleń
- timestamps
```

**Indeksy kompozytowe dla wydajności:**
- `status + starts_at` - główne zapytania o wydarzenia
- `event_type_id + status + starts_at` - filtrowanie po typie
- `user_id + status` - wydarzenia użytkownika
- `is_featured + status + starts_at` - wyróżnione wydarzenia

#### 3. `event_locations` - Lokalizacje Wydarzeń
```sql
- id (bigint, primary key)
- event_id (foreign key, unique) - jedno wydarzenie = jedna lokalizacja
- full_address (varchar 500) - pełny adres
- street (varchar 200) - ulica
- city (varchar 100) - miasto
- postal_code (varchar 10) - kod pocztowy
- country (varchar 2, default 'PL') - kraj
- latitude (decimal 10,8) - szerokość geograficzna
- longitude (decimal 11,8) - długość geograficzna
- public_location (varchar 200) - publiczna lokalizacja dla wydarzeń prywatnych
- location_notes (text) - dodatkowe uwagi o lokalizacji
- timestamps
```

**Indeksy przestrzenne:**
- `latitude + longitude` - zapytania geograficzne
- `city + country` - wyszukiwanie po mieście

#### 4. `event_registrations` - Rejestracje na Wydarzenia
```sql
- id (bigint, primary key)
- event_id (foreign key) - wydarzenie
- user_id (foreign key) - użytkownik
- status (enum: pending,confirmed,waiting_list,cancelled,rejected)
- message (text, nullable) - wiadomość użytkownika przy rejestracji
- organizer_notes (text, nullable) - prywatne notatki organizatora
- registered_at (datetime) - data rejestracji
- status_updated_at (datetime, nullable) - ostatnia zmiana statusu
- timestamps
```

**Ograniczenia:**
- `event_id + user_id` (unique) - jeden użytkownik = jedna rejestracja na wydarzenie

## Modele Eloquent

### EventType Model
```php
class EventType extends Model
{
    // Cachowanie popularnych typów
    public static function getActiveTypesCache()

    // Scopes
    public function scopeActive($query)
    public function scopeOrdered($query)
}
```

### Event Model
```php
class Event extends Model
{
    // Scopes wydajnościowe
    public function scopePublished($query)
    public function scopeUpcoming($query)
    public function scopeInCity($query, $city)
    public function scopeNearLocation($query, $lat, $lng, $radius)

    // Logika biznesowa
    public function canUserRegister(User $user): bool
    public function updateParticipantCount(): void
    public function getAvailableSpotsAttribute(): ?int
}
```

### EventLocation Model
```php
class EventLocation extends Model
{
    // Prywatność lokalizacji
    public function getDisplayLocationAttribute(): string

    // Obliczanie dystansu
    public function distanceTo(float $lat, float $lng): float
}
```

### EventRegistration Model
```php
class EventRegistration extends Model
{
    // Zarządzanie statusem
    public function confirm(?string $notes = null): bool
    public function reject(?string $notes = null): bool
    public function moveToWaitingList(?string $notes = null): bool

    // Sprawdzanie stanu
    public function isConfirmed(): bool
    public function isPending(): bool
}
```

## Funkcjonalności Systemu

### 1. Tworzenie Wydarzenia

**Proces:**
1. Wybór typu wydarzenia z listy aktywnych typów
2. Wypełnienie podstawowych informacji (tytuł, opis, data)
3. Ustawienie lokalizacji z geokodowaniem
4. Konfiguracja parametrów (liczba miejsc, opłaty, prywatność)
5. Publikacja lub zapisanie jako szkic

**Walidacje:**
- Data rozpoczęcia nie może być w przeszłości
- Maksymalna liczba uczestników > 0 (jeśli ustawiona)
- Wymagane pola: tytuł, typ, data, lokalizacja
- Opłata >= 0
- Deadline rejestracji przed datą wydarzenia

### 2. System Prywatności

**Wydarzenia publiczne:**
- Pełna lokalizacja widoczna dla wszystkich
- Każdy może się zarejestrować (jeśli są miejsca)
- Natychmiastowe potwierdzenie rejestracji

**Wydarzenia na zaproszenia:**
- Lokalizacja ukryta (tylko miasto/dzielnica)
- Rejestracja wymaga akceptacji organizatora
- Pełna lokalizacja dostępna po potwierdzeniu
- Organizator może odrzucić zgłoszenia

### 3. System Rejestracji

**Statusy rejestracji:**
- `pending` - oczekuje na akceptację (tylko wydarzenia prywatne)
- `confirmed` - potwierdzona rejestracja
- `waiting_list` - lista oczekujących (gdy brak miejsc)
- `cancelled` - anulowana przez użytkownika
- `rejected` - odrzucona przez organizatora

**Logika rejestracji:**
1. Sprawdzenie uprawnień użytkownika
2. Kontrola dostępnych miejsc
3. Respektowanie deadline'u rejestracji
4. Automatyczne przypisanie do listy oczekujących jeśli brak miejsc

### 4. Odkrywanie Wydarzeń

**Filtry wyszukiwania:**
- Typ wydarzenia (spacer, trening, socjalizacja...)
- Lokalizacja (miasto, promień w km)
- Zakres dat (od-do)
- Dostępność miejsc
- Bezpłatne wydarzenia
- Wyszukiwanie tekstowe (tytuł, opis)

**Sortowanie:**
- Wyróżnione wydarzenia na górze
- Chronologicznie (najbliższe pierwsze)
- Według popularności (liczba uczestników)
- Według odległości (zapytania geograficzne)

### 5. Zarządzanie przez Organizatora

**Panel organizatora:**
- Lista swoich wydarzeń (wszystkie statusy)
- Zarządzanie rejestracjami (akceptacja/odrzucenie)
- Edycja szczegółów wydarzenia
- Komunikacja z uczestnikami
- Statystyki i raporty

## Optymalizacje Wydajności

### 1. Strategia Cachowania

**Poziomy cache:**
```php
// Typy wydarzeń - 1 godzina
'event_types.active' => 3600

// Nadchodzące wydarzenia - 15 minut
'events.upcoming.{limit}.{page}' => 900

// Wyróżnione wydarzenia - 30 minut
'events.featured' => 1800

// Statystyki użytkownika - 10 minut
'events.user.{id}.stats' => 600
```

**Unieważnianie cache:**
- Po utworzeniu/edycji wydarzenia
- Po zmianie statusu rejestracji
- Po publikacji/anulowaniu wydarzenia

### 2. Indeksowanie Bazy

**Indeksy kompozytowe:**
```sql
-- Główne zapytania o wydarzenia
INDEX events_discovery_idx (status, starts_at, is_featured)

-- Zapytania geograficzne
INDEX locations_coordinates_idx (latitude, longitude)

-- Historia rejestracji użytkownika
INDEX registrations_user_history_idx (user_id, status, registered_at)
```

### 3. Optymalizacje Zapytań

**Eager loading:**
```php
Event::with(['eventType:id,name,icon,color', 'location:event_id,city,public_location'])
    ->published()
    ->upcoming()
    ->get();
```

**Ograniczanie kolumn:**
```php
->select(['id', 'title', 'starts_at', 'entry_fee', 'current_participants', 'max_participants'])
```

**Zapytania przestrzenne:**
```sql
SELECT *, (6371 * acos(cos(radians(?)) * cos(radians(latitude))
* cos(radians(longitude) - radians(?)) + sin(radians(?))
* sin(radians(latitude)))) AS distance
HAVING distance <= ?
ORDER BY distance
```

### 4. Denormalizacja Danych

**Liczniki wydajności:**
- `current_participants` w tabeli `events`
- `view_count` dla popularności
- Aktualizacja batch'owa co godzinę

## Bezpieczeństwo

### 1. Autoryzacja

**Uprawnienia:**
- Tylko zalogowani mogą tworzyć wydarzenia
- Organizator może edytować swoje wydarzenia
- Uczestnicy widzą pełne dane po potwierdzeniu
- Prywatność lokalizacji dla wydarzeń zamkniętych

### 2. Walidacja Danych

**Zabezpieczenia:**
- Sanityzacja adresów (XSS)
- Walidacja koordynat geograficznych
- Ograniczenia długości opisów
- Kontrola dat (przeszłość/przyszłość)

### 3. Rate Limiting

**Ograniczenia:**
- Tworzenie wydarzeń: 10/godzinę na użytkownika
- Rejestracje: 30/godzinę na użytkownika
- Wyszukiwanie: 100/minutę na IP

## Metryki i Monitoring

### 1. KPI Wydarzeń
- Liczba utworzonych wydarzeń/dzień
- Współczynnik konwersji (wyświetlenia → rejestracje)
- Średnia liczba uczestników na wydarzenie
- Najpopularniejsze typy wydarzeń

### 2. Wydajność Systemu
- Średni czas odpowiedzi zapytań
- Cache hit ratio
- Wykorzystanie indeksów bazodanowych
- Liczba zapytań na stronę

### 3. Jakość Danych
- Wydarzenia z pełnymi danymi lokalizacji
- Procent wydarzeń z opisami
- Aktywność organizatorów
- Współczynnik anulowania

## Rozwój i Rozszerzenia

### 1. Planowane Funkcjonalności
- **Płatności online** - integracja z systemem płatniczym
- **Powiadomienia push** - przypomnienia o wydarzeniach
- **Chat grupowy** - komunikacja uczestników
- **Oceny i opinie** - system feedback po wydarzeniu
- **API publiczne** - integracja z zewnętrznymi aplikacjami

### 2. Skalowanie
- **Sharding geograficzny** - podział bazy według lokalizacji
- **CDN dla zdjęć** - optymalizacja mediów
- **Queue system** - asynchroniczne powiadomienia
- **Read replicas** - rozdzielenie odczytów i zapisów

### 3. Integracje
- **Mapy Google** - wyświetlanie lokalizacji
- **Kalendarz** - eksport do aplikacji kalendarza
- **Media społecznościowe** - udostępnianie wydarzeń
- **Systemy płatnicze** - PayU, Stripe, PayPal

## Testowanie

### 1. Testy Jednostkowe
```php
// Model Event
test('can check if user can register for event')
test('updates participant count correctly')
test('respects registration deadline')

// Model EventLocation
test('shows appropriate location based on privacy')
test('calculates distance correctly')

// Model EventRegistration
test('changes status with proper notifications')
```

### 2. Testy Funkcjonalne
```php
// Tworzenie wydarzenia
test('authenticated user can create event')
test('validates required fields')
test('creates location with coordinates')

// Rejestracja
test('user can register for public event')
test('requires approval for private events')
test('respects participant limits')
```

### 3. Testy Wydajności
- Load testing dla 1000 równoczesnych użytkowników
- Stress testing zapytań geograficznych
- Memory profiling dla dużych list wydarzeń

## Deployment i Konfiguracja

### 1. Wymagania Środowiska
```env
# Cache (Redis zalecany)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Baza danych (MySQL 8.0+)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pethelp

# Mapy (Google Maps API)
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### 2. Konfiguracja Produkcyjna
```php
// config/events.php
return [
    'cache_ttl' => [
        'event_types' => 3600,
        'events_list' => 900,
        'user_stats' => 600,
    ],
    'search' => [
        'default_radius_km' => 25,
        'max_radius_km' => 100,
        'results_per_page' => 15,
    ],
    'limits' => [
        'max_participants' => 500,
        'max_description_length' => 5000,
        'max_events_per_user_daily' => 10,
    ]
];
```

### 3. Monitoring Produkcyjny
```php
// Logi aplikacji
Log::channel('events')->info('Event created', [
    'event_id' => $event->id,
    'user_id' => $user->id,
    'type' => $event->eventType->name
]);

// Metryki (Prometheus/Grafana)
app('metrics')->increment('events.created', [
    'type' => $event->eventType->slug
]);
```

## Podsumowanie

Moduł Wydarzenia "Spotkajmy się" to kompletny system organizacji spotkań dla społeczności właścicieli zwierząt. Charakteryzuje się:

✅ **Wysoką wydajnością** - optymalizowane zapytania, inteligentny cache, indeksy bazodanowe
✅ **Skalowalnością** - architektura przygotowana na tysiące wydarzeń i użytkowników
✅ **Bezpieczeństwem** - system prywatności, autoryzacja, walidacja danych
✅ **UX/UI** - intuicyjny interfejs, responsywny design, szybkie wyszukiwanie
✅ **Rozszerzalnością** - modularna struktura, API-ready, łatwe integracje

System jest gotowy do implementacji i może być stopniowo rozbudowywany o dodatkowe funkcjonalności zgodnie z potrzebami użytkowników.