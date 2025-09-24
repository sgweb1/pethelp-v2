# Formularz Noclegowy - Dokumentacja

## Przegląd

Kompletny formularz usługi noclegowej dla PetHelp został stworzony w architekturze Laravel TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire).

## Pliki utworzone/zmodyfikowane

### 1. Komponent Livewire
- **Plik**: `app/Livewire/Services/NightCareServiceForm.php`
- **Funkcja**: Główna logika formularza noclegowego rozszerzająca BaseServiceForm
- **Kluczowe cechy**:
  - Ceny (podstawowa i weekendowa za noc)
  - Długość pobytu (min/max nocy)
  - Preferencje zwierząt (mieszanie typów, różni właściciele)
  - Transport (opcjonalny z zasięgiem)
  - Usługi w cenie (karmienie, spacery, zabawa, etc.)
  - Szczegółowa walidacja

### 2. Widok Blade
- **Plik**: `resources/views/livewire/services/night-care-service-form.blade.php`
- **Funkcja**: UI formularza z responsywnym designem
- **Sekcje**:
  - Informacje podstawowe
  - Rodzaje i rozmiary zwierząt
  - Cennik (3-kolumnowy grid)
  - Długość pobytu
  - Preferencje zwierząt (czytelne karty)
  - Transport (conditional rendering)
  - Usługi w cenie (grid 3-kolumnowy)
  - Informacje kontaktowe
  - Lokalizacja

### 3. Wrapper aktualizowany
- **Plik**: `resources/views/services/forms/night-care-wrapper.blade.php`
- **Zmiana**: Aktualizacja z home-care na night-care component

### 4. Testy
- **Plik**: `tests/Feature/NightCareServiceFormTest.php`
- **Pokrycie**:
  - Renderowanie formularza
  - Wypełnienie fake danymi
  - Walidacja pól wymaganych
  - Walidacja cen i ograniczeń
  - Walidacja zakresu nocy
  - Walidacja transportu
  - Wartości domyślne
  - Conditional rendering

## Konfiguracja bazy danych

Formularz wykorzystuje istniejące pola tabeli `services`:

```sql
- price_per_night (decimal)
- weekend_price_per_night (decimal, nullable)
- min_nights (int)
- max_nights (int)
- transport_enabled (boolean)
- transport_radius_km (int, nullable)
- allows_multiple_owners (boolean)
- allows_mixing_pet_types (boolean)
- metadata (JSON) - dodatkowe ustawienia
```

## Metadata JSON Structure

```json
{
  "feeding_included": true,
  "walking_included": true,
  "play_time": true,
  "basic_grooming": false,
  "medication_admin": false,
  "daily_updates": true,
  "special_notes": "Dodatkowe informacje..."
}
```

## Routing

Formularz jest automatycznie dostępny przez istniejący routing:
- `/services/create/8` (gdzie 8 to ID kategorii "opieka-nocna")
- Slug mapping: `'opieka-nocna' => 'services.forms.night-care-wrapper'`

## Walidacja

### Wymagane pola:
- Tytuł usługi
- Opis usługi
- Cena za noc (min: 30 PLN, max: 500 PLN)
- Minimum/maksimum nocy
- Rodzaje zwierząt
- Rozmiary zwierząt
- Kontakt (telefon, email)
- Adres

### Logika biznesowa:
- Cena weekendowa nie może być niższa od podstawowej
- Maksimum nocy nie może być mniejsze od minimum
- Transport wymaga podania zasięgu jeśli włączony
- Maksymalnie 5 zwierząt jednocześnie

## Fake Data

Przycisk "Wypełnij testowymi danymi" wypełnia formularz realistycznymi danymi:
- Cena: 80 PLN/noc (weekendy: 100 PLN)
- Pobyt: 1-14 nocy
- Maksymalnie 3 zwierzęta
- Transport: 15 km zasięgu
- Wszystkie podstawowe usługi włączone

## Features

### 1. Responsive Design
- Mobile-first approach
- Grid adaptacyjny (1 kolumna na mobile, 2-3 na desktop)
- Czytelne labele i helptexty

### 2. User Experience
- Loading states podczas zapisywania
- Conditional rendering (transport radius)
- Error handling z precyzyjnymi komunikatami
- Dark mode support

### 3. Accessibility
- Proper labeling
- Required field indicators (*)
- Keyboard navigation
- Color contrast compliance

### 4. Integration
- Dziedziczenie po BaseServiceForm
- MapItem automatycznie tworzony/aktualizowany
- Service Categories integration
- User profile pre-filling

## Użycie

1. Użytkownik wybiera kategorię "Opieka nocna"
2. System przekierowuje na `/services/create/8`
3. Router ładuje `night-care-wrapper.blade.php`
4. Wrapper inicjuje `NightCareServiceForm` component
5. Formularz ładuje się z wartościami domyślnymi
6. Po wypełnieniu i zapisaniu tworzy się Service + MapItem

## Rozwój

Formularz jest gotowy do produkcji i integruje się z istniejącą architekturą. Możliwe rozszerzenia:
- Calendar picker dla dostępności
- Galeria zdjęć domu
- Dodatkowe usługi premium
- Recenzje i oceny
- Integracja z systemem płatności

## Testowanie

Uruchom testy:
```bash
php artisan test --filter=NightCareServiceFormTest
```

Dostęp do formularza w przeglądarce:
```
http://pethelp.test/services/create/8
```
(wymagane zalogowanie)