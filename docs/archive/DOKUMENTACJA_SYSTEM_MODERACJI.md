# System Moderacji Recenzji - Dokumentacja Administratora

## Przegląd Systemu

System moderacji umożliwia administratorom zarządzanie recenzjami użytkowników w czasie rzeczywistym. Zapewnia pełną kontrolę nad zawartością publikowaną na platformie PetHelp.

### Główne Funkcje

- **Dashboard Administratora** - centralny panel zarządzania
- **Moderacja w czasie rzeczywistym** - natychmiastowe akcje
- **Operacje masowe** - działania na wielu recenzjach jednocześnie
- **Historia działań** - pełny audit log moderacji
- **Zaawansowane filtry** - precyzyjne wyszukiwanie
- **System uprawnień** - role-based access control

## Dostęp do Systemu

### Wymagania
- Konto użytkownika z rolą `admin`
- Uwierzytelniony dostęp do platformy
- Przeglądarka wspierająca nowoczesny JavaScript

### Logowanie
```
URL: http://127.0.0.1:8000/admin/reviews
Email: admin@pethelp.local
Hasło: password
```

### Konta Testowe
- **Administrator:** admin@pethelp.local / password
- **Moderator:** moderator@pethelp.local / password

## Interface Użytkownika

### Dashboard Główny

#### Sekcja Statystyk
Cztery karty statystyk wyświetlają kluczowe metryki:

1. **Wszystkie recenzje** - łączna liczba recenzji w systemie
2. **Oczekujące** - recenzje wymagające moderacji (⚠️ urgent indicator)
3. **Zatwierdzone** - recenzje opublikowane publicznie
4. **Oznaczone** - recenzje wymagające dodatkowej uwagi

#### Dodatkowe Statystyki
- **Średnia ocena** - ogólna średnia wszystkich zatwierdzonych recenzji
- **Recenzje dzisiaj** - liczba nowych recenzji z dzisiejszego dnia
- **Ten tydzień** - statystyki tygodniowe
- **Odrzucone** - liczba recenzji odrzuconych przez moderatorów

#### Panel Filtrów
Zaawansowane opcje filtrowania:

- **Status:** wszystkie / oczekujące / zatwierdzone / odrzucone
- **Minimalna ocena:** 1-5 gwiazdek
- **Tylko oznaczone:** checkbox do wyświetlania flagged content
- **Zakres dat:** od/do dla filtrowania temporalnego
- **Przycisk czyszczenia:** reset wszystkich filtrów

#### Akcje Masowe
System selekcji wielokrotnej umożliwia:
- **Zatwierdź wszystkie** - approve wybranych recenzji
- **Odrzuć wszystkie** - reject z wymaganym powodem  
- **Oznacz wszystkie** - flag do przyszłej moderacji
- **Anuluj selekcję** - wyczyść zaznaczenie

### Tabela Recenzji

#### Kolumny Tabeli
1. **Checkbox** - selekcja dla operacji masowych
2. **Recenzja** - avatar, nazwy użytkowników, preview tekstu, flagi
3. **Ocena** - gwiazdki + wartość numeryczna
4. **Status** - kolorowe badges (pending/approved/rejected)
5. **Data** - data i godzina utworzenia
6. **Akcje** - przyciski quick actions + menu dropdown

#### Elementy Wiersza
- **Avatar właściciela** - inicjały w kolorowym kółku
- **Relacja** - PropertyOwner → PetSitter ze strzałką
- **Preview tekstu** - pierwsze 100 znaków recenzji
- **Flagi statusu:**
  - 🏷️ **Oznaczona** - czerwona etykieta
  - ⭐ **Wyróżniona** - żółta etykieta

#### Quick Actions
- ✅ **Zatwierdź** - zielony przycisk dla pending reviews
- ❌ **Odrzuć** - czerwony przycisk z wymaganym powodem

#### Menu Dropdown
- 👁️ **Szczegóły** - otwiera modal ze szczegółami
- ✅ **Zatwierdź** - approve recenzji
- ❌ **Odrzuć** - reject z formularzem powodu
- ⭐ **Wyróżnij/Usuń wyróżnienie** - toggle featured status
- 🏷️ **Oznacz/Usuń oznaczenie** - toggle flagged status
- 🗑️ **Usuń permanentnie** - hard delete (z potwierdzeniem)

## Modalne Okna

### Modal Szczegółów Recenzji

#### Sekcja Główna
- **Status i akcje** - bieżący status + przyciski akcji
- **Informacje o uczestnikach** - właściciel i opiekun z avatarami
- **Szczegółowe oceny** - wszystkie kategorie gwiazdek
- **Tekst recenzji** - pełny komentarz użytkownika
- **Plusy i minusy** - sekcje w kolorowych ramkach
- **Odpowiedź opiekuna** - jeśli istnieje

#### Sidebar Kontekstowy
- **Statystyki użytkowników:**
  - Łączne recenzje opiekuna
  - Średnia ocena opiekuna  
  - Recenzje napisane przez właściciela
  - Dni od zakończenia rezerwacji
- **Historia moderacji:**
  - Data moderacji
  - Notatki moderatora
  - Powód odrzucenia (jeśli applicable)

#### Formularz Odrzucenia
- **Powód odrzucenia** - wymagane pole tekstowe
- **Notatki wewnętrzne** - opcjonalne dla zespołu
- Walidacja wymaganego powodu przed submitem

### Modal Masowego Odrzucania

#### Ostrzeżenie
- Czerwone pole z informacją o nieodwracalności akcji
- Liczba wybranych recenzji z proper Polish grammar

#### Formularz
- **Powód odrzucenia** - wspólny dla wszystkich wybranych
- **Notatki wewnętrzne** - opcjonalne dla dokumentacji
- **Informacje pomocnicze** - tooltips o visibility powodów

### Modal Historii Działań

#### Filtry Aktywności
- **Administrator** - dropdown z listą moderatorów
- **Typ akcji** - zatwierdzone/odrzucone
- **Przycisk odświeżenia** - reload danych

#### Lista Aktywności
Każdy wpis zawiera:
- **Ikonę akcji** - kolorową odpowiadającą typowi
- **Moderatora** - imię i akcję w past tense
- **Czas** - relative time (np. "2 godz. temu")
- **Szczegóły recenzji** - uczestnicy, ocena, preview
- **Detale moderacji** - powód odrzucenia, notatki

## API Endpoints

### Lista Recenzji
```
GET /api/admin/reviews
```

**Parametry Query:**
- `status` - pending|approved|rejected
- `min_rating` - 1-5
- `flagged_only` - boolean
- `date_from` - YYYY-MM-DD
- `date_to` - YYYY-MM-DD
- `sitter_name` - string
- `owner_name` - string
- `page` - numer strony

**Response:**
```json
{
  "reviews": {
    "data": [...],
    "current_page": 1,
    "last_page": 10,
    "total": 150
  },
  "statistics": {
    "total_reviews": 150,
    "pending_reviews": 5,
    "approved_reviews": 140,
    "rejected_reviews": 5,
    "flagged_reviews": 2,
    "reviews_today": 3,
    "reviews_this_week": 15,
    "average_rating": 4.2
  }
}
```

### Szczegóły Recenzji
```
GET /api/admin/reviews/{reviewId}
```

**Response:**
```json
{
  "review": {
    "id": 123,
    "overall_rating": 4.5,
    "review_text": "...",
    "status": "pending",
    "is_flagged": false,
    "is_featured": false,
    "owner": { "name": "Jan Kowalski" },
    "sitter": { "name": "Anna Nowak" },
    "..."
  },
  "context": {
    "sitter_total_reviews": 25,
    "sitter_average_rating": 4.3,
    "owner_total_reviews_given": 8,
    "days_after_booking": 2
  }
}
```

### Zatwierdzenie Recenzji
```
POST /api/admin/reviews/{reviewId}/approve
```

**Body:**
```json
{
  "notes": "Recenzja zgodna z regulaminem" // optional
}
```

### Odrzucenie Recenzji
```
POST /api/admin/reviews/{reviewId}/reject
```

**Body:**
```json
{
  "reason": "Naruszenie regulaminu - język obraźliwy", // required
  "notes": "Zgłoszona przez innych użytkowników" // optional
}
```

### Operacje Masowe
```
POST /api/admin/reviews/bulk-action
```

**Body:**
```json
{
  "action": "approve|reject|flag|unflag|feature|unfeature",
  "review_ids": [1, 2, 3, 4, 5],
  "reason": "Wymagany dla reject/flag", // conditional
  "notes": "Opcjonalne notatki" // optional
}
```

### Historia Działań
```
GET /api/admin/reviews/activity/log
```

**Parametry:**
- `admin_id` - ID moderatora
- `action` - approved|rejected
- `page` - paginacja

## Bezpieczeństwo

### Middleware Ochrony
```php
// app/Http/Middleware/AdminMiddleware.php
if ($user->role !== 'admin') {
    abort(403, 'Unauthorized - Admin access required');
}
```

### Walidacja Uprawnień
- Każdy endpoint sprawdza rolę użytkownika
- Sesje administratorów są monitorowane
- Wszystkie akcje są logowane z user_id

### Audit Log
Każda akcja moderacyjna zapisuje:
- ID administratora
- Timestamp akcji  
- Typ operacji (approve/reject/flag/etc.)
- ID recenzji i powiązanych użytkowników
- Powody i notatki

## Konfiguracja Techniczna

### Role Użytkowników
Enum w tabeli `users`:
```sql
role CHECK(role IN ("owner", "sitter", "both", "admin"))
```

### Struktura Bazy - Tabela Reviews
```sql
-- Moderacja podstawowa
status ENUM('pending', 'approved', 'rejected', 'hidden')
moderated_at TIMESTAMP NULL
moderated_by INT NULL -- FK do users
moderation_notes TEXT NULL
rejection_reason TEXT NULL -- widoczny dla użytkownika

-- System flag
is_flagged BOOLEAN DEFAULT FALSE
flagged_at TIMESTAMP NULL
flagged_by INT NULL -- FK do users  
flag_reason VARCHAR(500) NULL

-- System wyróżniania
is_featured BOOLEAN DEFAULT FALSE
featured_at TIMESTAMP NULL
featured_by INT NULL -- FK do users
```

### Indeksy Wydajnościowe
```sql
INDEX(status, moderated_at)    -- szybkie filtrowanie
INDEX(is_flagged, flagged_at)  -- flagged content
INDEX(is_featured, featured_at) -- featured reviews
INDEX(sitter_id, status)       -- public listings
```

## Przepływ Moderacji

### Automatyczne Zatwierdzanie
- Nowe recenzje otrzymują status `approved` 
- Są natychmiast widoczne publicznie
- Moderatorzy mogą zmienić status post-factum

### Workflow Moderacji
1. **Tworzenie recenzji** - status: `approved` (auto)
2. **Flagowanie** - admin może oznaczyć do review
3. **Szczegółowa moderacja** - analiza w modal details
4. **Akcja moderacyjna** - approve/reject z dokumentacją
5. **Audit log** - zapis działania w history

### Powiadomienia (Przyszłość)
- Email do opiekuna przy nowej recenzji
- Powiadomienie o odrzuceniu dla właściciela  
- Alerty dla adminów o flagged content

## Rozszerzenia Funkcjonalne

### Planowane Features
1. **Dashboard Analytics**
   - Wykresy trendów moderacji
   - Heat mapy aktywności
   - KPI metrics dla zespołu

2. **ML Auto-Moderation**
   - Automatyczne wykrywanie spam
   - Analiza sentymentu  
   - Flagowanie potencjalnie problematycznych treści

3. **Powiadomienia Real-time**
   - WebSocket notifications
   - Browser push notifications
   - Email alerts dla kritycznych przypadków

4. **System Eskalacji**
   - Multi-level approval workflow
   - Supervisor override capabilities
   - SLA tracking dla resolution time

5. **Raporty i Eksport**
   - PDF/Excel exports
   - Scheduled reports
   - Custom reporting queries

## Troubleshooting

### Częste Problemy

#### 1. Brak dostępu do /admin/reviews
**Symptom:** 403 Forbidden
**Rozwiązanie:**
- Sprawdź rolę użytkownika: `SELECT role FROM users WHERE email = 'user@example.com'`
- Upewnij się że middleware admin jest active
- Zweryfikuj sesję użytkownika

#### 2. Puste statystyki na dashboard
**Symptom:** Wszystkie liczniki pokazują 0
**Rozwiązanie:**
- Sprawdź połączenie z API: Network tab w devtools
- Zweryfikuj endpoint: `GET /api/admin/reviews`
- Sprawdź permissions dla admin API routes

#### 3. Błędy przy operacjach masowych
**Symptom:** Timeout lub partial updates
**Rozwiązanie:**
```php
// Zwiększ timeout w kontrolerze
set_time_limit(300);

// Sprawdź batch size - maksymalnie 50 records
if (count($request->review_ids) > 50) {
    return response()->json(['error' => 'Too many items selected'], 422);
}
```

#### 4. Błędy walidacji przy odrzucaniu
**Symptom:** "Reason is required" mimo wypełnionego pola
**Rozwiązanie:**
- Sprawdź czy front-end wysyła poprawny JSON
- Zweryfikuj Content-Type header: `application/json`
- Upewnij się że reason nie jest empty string

### Debugging Commands

```bash
# Sprawdź routes
php artisan route:list | grep admin

# Sprawdź logs
tail -f storage/logs/laravel.log | grep -i review

# Sprawdź migracje
php artisan migrate:status

# Sprawdź permissions w bazie
php artisan tinker
>>> User::where('role', 'admin')->get(['name', 'email', 'role'])

# Test API endpoint
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/admin/reviews
```

### Logi do Monitorowania

```bash
# Aktywność moderacji
grep "Review.*by admin" storage/logs/laravel.log

# Błędy permissions
grep "Unauthorized.*Admin" storage/logs/laravel.log  

# Performance issues
grep "Query.*slow" storage/logs/laravel.log
```

## Wydajność i Optimalizacja

### Cache Strategia
```php
// Cache statystyk (refresh co 5 min)
Cache::remember('admin.review.stats', 300, function () {
    return [
        'total_reviews' => Review::count(),
        'pending_reviews' => Review::where('status', 'pending')->count(),
        // ...
    ];
});
```

### Database Optimizations
- Użycie indeksów dla często filtrowanych kolumn
- LIMIT queries dla dużych tabel
- Eager loading relacji (with(['owner', 'sitter']))
- Connection pooling dla wysokiego traffic

### Frontend Performance
- Lazy loading dla modal content
- Debounced search inputs
- Virtual scrolling dla długich list
- Component memoization w Vue.js

## Backup i Disaster Recovery

### Backup Strategia
- **Codzienne backupy** bazy danych
- **Retention policy** - 30 dni daily, 12 miesięcy weekly
- **Point-in-time recovery** dla critical operations
- **Audit log preservation** - permanent storage

### Rollback Procedures
```bash
# Rollback ostatniej migracji
php artisan migrate:rollback --step=1

# Restore z backup
php artisan backup:restore --date=2025-09-06

# Recovery audit trail
php artisan review:audit:rebuild --from=2025-09-01
```

---

**Dokumentacja wersja:** 2.0  
**Data aktualizacji:** 07.09.2025  
**Status:** Produkcja - pełna funkcjonalność  
**Kontakt techniczny:** admin@pethelp.local  
**System:** Laravel 12.28.1 + Vue 3 + TypeScript