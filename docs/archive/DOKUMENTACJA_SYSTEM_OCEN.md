# System Ocen i Recenzji - Dokumentacja

## Przegląd Systemu

System ocen i recenzji umożliwia właścicielom zwierząt ocenianie opiekunów po zakończonych rezerwacjach. System wspiera wielopoziomowe oceny, moderację oraz interakcje między użytkownikami.

### Główne Funkcje

- **Oceny wielopoziomowe**: Ogólna ocena + 5 kategorii szczegółowych
- **Recenzje tekstowe**: Opinie, plusy i minusy
- **Moderacja**: Automatyczne i ręczne zatwierdzanie
- **Odpowiedzi opiekunów**: Możliwość odpowiedzi na recenzje
- **Anonimowość**: Opcjonalne publikowanie anonimowe
- **Statystyki**: Kompleksowe podsumowania i analityka

## Architektura Systemu

### Model Bazy Danych

#### Tabela `reviews`
```sql
- id (primary key)
- booking_id (foreign key) - powiązanie z rezerwacją
- sitter_id (foreign key) - oceniany opiekun
- owner_id (foreign key) - autor recenzji
- overall_rating (decimal) - ocena ogólna (1-5)
- communication_rating (integer, nullable) - komunikacja
- reliability_rating (integer, nullable) - niezawodność
- pet_care_rating (integer, nullable) - opieka nad zwierzęciem
- cleanliness_rating (integer, nullable) - czystość
- value_rating (integer, nullable) - stosunek jakości do ceny
- review_text (text, nullable) - tekst recenzji
- pros (text, nullable) - pozytywne aspekty
- cons (text, nullable) - obszary do poprawy
- is_anonymous (boolean) - czy anonimowa
- is_verified (boolean) - czy zweryfikowana
- is_featured (boolean) - czy wyróżniona
- status (enum: pending, approved, rejected) - status moderacji
- sitter_response (text, nullable) - odpowiedź opiekuna
- sitter_responded_at (timestamp, nullable) - data odpowiedzi
- edit_deadline (timestamp) - termin edycji
- created_at, updated_at, deleted_at
```

### Backend - Laravel

#### Controller: `ReviewController`

**Główne metody:**

1. **`index(Request $request, $sitterId)`**
   - Pobiera recenzje dla opiekuna
   - Filtry: minimalna ocena, okres czasu
   - Paginacja (10 per strona)
   - Zwraca: recenzje + statystyki

2. **`store(Request $request, $bookingId)`**
   - Tworzy nową recenzję
   - Waliduje uprawnienia (tylko właściciel rezerwacji)
   - Sprawdza możliwość recenzowania
   - Automatyczne zatwierdzanie

3. **`update(Request $request, $reviewId)`**
   - Edytuje recenzję (w oknie czasowym)
   - Sprawdza właściciela
   - Waliduje deadline edycji

4. **`destroy($reviewId)`**
   - Usuwa recenzję (soft delete)
   - Sprawdza uprawnienia i deadline

5. **`respond(Request $request, $reviewId)`**
   - Dodaje odpowiedź opiekuna
   - Sprawdza, czy to właściwy opiekun
   - Ograniczenie do jednej odpowiedzi

6. **`statistics($sitterId)`**
   - Zwraca szczegółowe statystyki
   - Średnie oceny, rozkład, wyróżnione recenzje

#### Model: `Review`

**Relacje:**
- `belongsTo(User::class, 'owner_id')` - właściciel
- `belongsTo(User::class, 'sitter_id')` - opiekun
- `belongsTo(Booking::class)` - rezerwacja

**Scope methods:**
- `forSitter($sitterId)` - recenzje dla opiekuna
- `visible()` - tylko zatwierdzone i widoczne
- `byRating($minRating)` - filtr minimum oceny
- `recent($days)` - z ostatnich X dni

**Helper methods:**
- `isApproved()` - czy zatwierdzona
- `canEdit()` - czy można edytować
- `hasResponse()` - czy ma odpowiedź opiekuna
- `addSitterResponse($response)` - dodaj odpowiedź

### Frontend - Vue.js Components

#### 1. `StarRating.vue`
**Przeznaczenie:** Interaktywny komponent oceny gwiazdkami

**Props:**
- `modelValue: number` - aktualna ocena
- `size: number` - rozmiar gwiazdek (px)
- `readonly: boolean` - tylko do odczytu
- `showValue: boolean` - pokaż wartość numeryczną
- `required: boolean` - wymagane (nie można wyzerować)

**Funkcje:**
- Hover effects z podświetleniem
- Kliknięcie tej samej gwiazdki wyzeruje (jeśli nie required)
- Responsive design
- v-model binding

#### 2. `ReviewForm.vue`
**Przeznaczenie:** Formularz dodawania/edytowania recenzji

**Props:**
- `booking: Object` - obiekt rezerwacji z danymi opiekuna

**Funkcje:**
- Ocena ogólna (wymagana)
- 5 kategorii szczegółowych (opcjonalne)
- Pole tekstowe (2000 znaków)
- Plusy (1000 znaków)
- Minusy (1000 znaków)
- Checkbox anonimowości
- Walidacja po stronie klienta
- Loading states i error handling

#### 3. `ReviewCard.vue`
**Przeznaczenie:** Wyświetlanie pojedynczej recenzji

**Props:**
- `review: Object` - obiekt recenzji
- `canRespond: boolean` - czy może odpowiedzieć
- `canEdit: boolean` - czy może edytować

**Funkcje:**
- Wyświetlanie wszystkich ocen i tekstów
- Avatar z inicjałami
- Oznaczenia: zweryfikowana, nowa
- Sekcja odpowiedzi opiekuna
- Formularz odpowiedzi (dla opiekunów)
- Przyciski edycji/usunięcia
- Plusy/minusy w kolorowych sekcjach

#### 4. `ReviewsList.vue`
**Przeznaczenie:** Lista recenzji z filtrami i paginacją

**Props:**
- `sitterId: number` - ID opiekuna
- `canRespond: boolean` - czy użytkownik może odpowiadać
- `autoLoad: boolean` - automatyczne ładowanie

**Funkcje:**
- Filtry: minimalna ocena, okres czasu
- Sortowanie: data, ocena
- Paginacja z nawigacją
- Loading states
- Integracja z `ReviewCard`
- Events: edit, delete, refresh

#### 5. `SitterRatingSummary.vue`
**Przeznaczenie:** Podsumowanie ocen opiekuna

**Props:**
- `sitterId: number` - ID opiekuna
- `showExtendedStats: boolean` - dodatkowe statystyki

**Funkcje:**
- Średnia ocena i liczba recenzji
- Rozkład ocen (histogram)
- Średnie ocen szczegółowych
- Wyróżnione recenzje (3 najnowsze)
- Statystyki dodatkowe (% odpowiedzi, zweryfikowanych)
- Oznaczenia nowych recenzji

### API Endpoints

#### Publiczne (bez autoryzacji)
```
GET /api/sitters/{sitter}/reviews - lista recenzji
GET /api/sitters/{sitter}/statistics - statystyki opiekuna
```

#### Autoryzowane
```
POST /api/reviews/booking/{booking} - dodaj recenzję
GET /api/reviews/{review} - szczegóły recenzji
PUT /api/reviews/{review} - edytuj recenzję
DELETE /api/reviews/{review} - usuń recenzję
POST /api/reviews/{review}/respond - odpowiedź opiekuna
```

### Parametry Zapytań

#### Lista recenzji (`/api/sitters/{sitter}/reviews`)
- `min_rating` - minimalna ocena (1-5)
- `period` - okres (recent=30 dni, last_year=365 dni)
- `page` - numer strony (paginacja)

## Proces Biznesowy

### 1. Dodawanie Recenzji
1. Właściciel kończy rezerwację (status: completed)
2. System udostępnia możliwość recenzji (przez 30 dni)
3. Właściciel wypełnia formularz recenzji
4. Recenzja jest automatycznie zatwierdzana
5. Opiekun otrzyma powiadomienie

### 2. Odpowiedź Opiekuna
1. Opiekun widzi nową recenzję w swoim panelu
2. Może dodać jedną odpowiedź (do 1000 znaków)
3. Odpowiedź jest widoczna publicznie
4. Brak możliwości edycji odpowiedzi

### 3. Edycja/Usunięcie
1. Właściciel może edytować przez 7 dni
2. Po tym czasie recenzja jest zablokowana
3. Usunięcie to soft delete (zachowanie danych)

### 4. Moderacja
1. Wszystkie recenzje są obecnie auto-zatwierdzane
2. System przewiduje ręczną moderację (status: pending/approved/rejected)
3. Możliwość oznaczania jako featured

## Konfiguracja i Instalacja

### 1. Migracje
```bash
php artisan migrate
```

Utworzy tabelę `reviews` z wszystkimi wymaganymi kolumnami.

### 2. Seeder (opcjonalny)
```bash
php artisan db:seed --class=ReviewSeeder
```

### 3. Komponenty Frontend
Wszystkie komponenty są w katalogu `resources/js/Components/Reviews/`:
- `StarRating.vue`
- `ReviewForm.vue` 
- `ReviewCard.vue`
- `ReviewsList.vue`
- `SitterRatingSummary.vue`

### 4. Import w innych komponentach
```js
import ReviewsList from '@/Components/Reviews/ReviewsList.vue'
import SitterRatingSummary from '@/Components/Reviews/SitterRatingSummary.vue'
```

## Integracja z Profilem Opiekuna

### Dodanie do strony profilu opiekuna:

```vue
<template>
  <!-- Inne sekcje profilu -->
  
  <!-- Podsumowanie ocen -->
  <SitterRatingSummary 
    :sitter-id="sitter.id"
    :show-extended-stats="true"
    class="mb-6"
  />
  
  <!-- Lista recenzji -->
  <ReviewsList 
    :sitter-id="sitter.id"
    :can-respond="canRespond"
  />
</template>

<script setup>
import SitterRatingSummary from '@/Components/Reviews/SitterRatingSummary.vue'
import ReviewsList from '@/Components/Reviews/ReviewsList.vue'

// canRespond = użytkownik to opiekun AND to jego profil
const canRespond = computed(() => {
  return user.value?.id === sitter.id && user.value?.is_sitter
})
</script>
```

## Walidacja i Bezpieczeństwo

### Walidacja Backend
- Oceny: 1-5 (overall wymagana, szczegółowe opcjonalne)
- Teksty: maksymalne długości (review_text: 2000, pros/cons: 1000)
- Autoryzacja: tylko właściciel rezerwacji może recenzować
- Czasowa: można recenzować tylko completed bookings
- Unikalność: jedna recenzja na rezerwację

### Walidacja Frontend
- Real-time validation w formularzach
- Liczniki znaków
- Wymagane pola oznaczone
- Confirmation dialogs przed usunięciem

### Zabezpieczenia
- CSRF protection na wszystkich endpoints
- Rate limiting dla API
- XSS protection w wyświetlaniu tekstów
- Soft deletes zamiast hard deletes
- Logging wszystkich operacji

## Rozszerzenia i Plany

### Planowane funkcje:
1. **System moderacji** - panel administracyjny
2. **Powiadomienia** - email/SMS dla nowych recenzji
3. **Odpowiedzi wielopoziomowe** - dyskusje pod recenzjami
4. **Załączniki** - zdjęcia w recenzjach
5. **ML moderacja** - automatyczne wykrywanie spam/abuse
6. **Statystyki zaawansowane** - trendy, porównania
7. **Export danych** - PDF/Excel dla opiekunów

### Optimalizacje:
- Cache dla statystyk opiekunów
- Indexy bazodanowe dla wydajności
- Lazy loading dla długich list
- CDN dla avatarów/zdjęć

## Troubleshooting

### Częste problemy:

1. **"Unauthorized" przy dodawaniu recenzji**
   - Sprawdź czy użytkownik to właściciel booking
   - Sprawdź status rezerwacji (musi być completed)

2. **"Review can no longer be edited"**
   - Sprawdź edit_deadline w bazie danych
   - Domyślnie 7 dni od utworzenia

3. **Brak statystyk opiekuna**
   - Sprawdź czy opiekun ma zatwierdzone recenzje
   - Sprawdź relationships w modelu User

4. **Frontend nie ładuje recenzji**
   - Sprawdź network tab w devtools
   - Sprawdź czy routes są poprawnie zdefiniowane
   - Sprawdź axios configuration

### Logi do sprawdzenia:
```bash
tail -f storage/logs/laravel.log | grep -i review
```

### Przydatne komendy artisan:
```bash
# Sprawdź routes
php artisan route:list | grep -i review

# Wyczyść cache
php artisan config:clear
php artisan view:clear

# Sprawdź migracje
php artisan migrate:status
```

---

**Wersja dokumentacji:** 1.0  
**Data ostatniej aktualizacji:** 06.09.2025  
**Autor:** System PetHelp  
**Status:** Aktywny - gotowy do wdrożenia