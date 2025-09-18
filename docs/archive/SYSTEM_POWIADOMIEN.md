# System Powiadomień - PetHelp

## 📧 Przegląd Systemu

System powiadomień PetHelp zapewnia użytkownikom informacje o wszystkich ważnych wydarzeniach w czasie rzeczywistym poprzez powiadomienia email i w aplikacji.

## 🎯 Funkcjonalności

### Rodzaje Powiadomień

#### 1. **Powiadomienia o Rezerwacjach**
- **BookingRequestNotification** - Nowe żądanie rezerwacji dla opiekunów
- **BookingConfirmedNotification** - Potwierdzenie rezerwacji dla właścicieli
- **BookingCompletedNotification** - Ukończenie usługi z zachętą do napisania opinii
- **BookingCancelledNotification** - Anulowanie rezerwacji z powodem

#### 2. **Powiadomienia o Opiniach**  
- **NewReviewNotification** - Nowa opinia dla opiekunów
- **ReviewModerationNotification** - Powiadomienia moderacyjne (admin)
- **ReviewRejectedNotification** - Odrzucenie opinii

### Kanały Dostarczania

- **📧 Email** - Wszystkie powiadomienia są wysyłane mailem
- **🔔 Database** - Powiadomienia w aplikacji z kropką oznaczającą nieprzeczytane
- **🎯 Real-time** - Polling co 30s dla aktualizacji licznika

## 🛠️ Architektura Techniczna

### Backend (PHP/Laravel)

#### Kontrolery
```php
// Powiadomienia użytkownika
app/Http/Controllers/NotificationController.php

// Integracje z istniejącymi kontrolerami
app/Http/Controllers/BookingController.php
app/Http/Controllers/ReviewController.php
```

#### Klasy Powiadomień
```php
app/Notifications/BookingRequestNotification.php
app/Notifications/BookingConfirmedNotification.php  
app/Notifications/BookingCompletedNotification.php
app/Notifications/BookingCancelledNotification.php
app/Notifications/NewReviewNotification.php
```

#### Modele i Traits
```php
// User model posiada trait Notifiable
App\Models\User uses Illuminate\Notifications\Notifiable

// Tabela database notifications
Schema: notifications table (utworzona)
```

### Frontend (Vue.js/TypeScript)

#### Komponenty
```vue
// Dzwonek powiadomień w navbarze
resources/js/Components/Notifications/NotificationBell.vue

// Pełna strona powiadomień
resources/js/Pages/Notifications.vue
```

#### Funkcjonalności Frontend
- Dropdown z ostatnimi powiadomieniami
- Licznik nieprzeczytanych z badge
- Oznaczanie jako przeczytane/nieprzeczytane
- Usuwanie pojedynczych powiadomień
- Usuwanie wszystkich przeczytanych
- Filtrowanie (wszystkie/nieprzeczytane/przeczytane)
- Automatyczne polling dla aktualizacji

### Kolejki i Performance

#### Konfiguracja Kolejek
```env
QUEUE_CONNECTION=database
QUEUE_DRIVER=database
```

#### Background Processing
- Wszystkie powiadomienia implementują `ShouldQueue`
- Queue worker uruchomiony: `php artisan queue:work --daemon`
- Failed jobs tracking w tabeli `failed_jobs`

## 🎨 UI/UX Design

### NotificationBell Komponent
- **Ikonka**: Ikona dzwonka w navbarze
- **Badge**: Czerwona kropka z liczbą nieprzeczytanych (max 99+)
- **Dropdown**: 380px szerokości z ostatnimi powiadomieniami
- **Actions**: Oznacz wszystkie, ustawienia, zobacz wszystkie

### Strona Powiadomień
- **Filtry**: Zakładki All/Unread/Read z liczbami
- **Bulk Actions**: Oznacz wszystkie jako przeczytane, usuń przeczytane  
- **Lista**: Karty powiadomień z ikonami, tytułami i akcjami
- **Paginacja**: Load more button dla dużych list

### Styling
- **Kolory**: Ikony powiadomień mają kolory według typu (blue/green/red/purple)
- **States**: Nieprzeczytane mają teal border-left i tło
- **Typography**: Font weight różni się dla przeczytanych/nieprzeczytanych
- **Responsive**: Wszystkie komponenty są mobilne

## 📨 Email Templates

### Struktura Email
- **Greeting**: "Cześć {name}!" / "Świetne wiadomości, {name}!"
- **Content**: Szczegółowe informacje o wydarzeniu
- **CTA Button**: Wyraźny przycisk akcji
- **Footer**: "Zespół PetHelp 🐾" + emotikony

### Personalizacja
- Dynamiczne treści zależne od danych  
- Wielojęzyczność (obecnie PL)
- Emojis dla lepszego engagement
- Warunkowe sekcje (np. tylko gdy message exists)

## 🔧 API Endpoints

### Powiadomienia Użytkownika
```http
GET    /api/notifications              # Lista powiadomień
GET    /api/notifications/unread-count # Licznik nieprzeczytanych
POST   /api/notifications/{id}/read    # Oznacz jako przeczytane
POST   /api/notifications/mark-all-read # Oznacz wszystkie jako przeczytane
DELETE /api/notifications/{id}         # Usuń powiadomienie
DELETE /api/notifications/read/all     # Usuń wszystkie przeczytane
GET    /api/notifications/preferences  # Preferencje użytkownika
PUT    /api/notifications/preferences  # Aktualizuj preferencje
```

### Parametry Zapytań
```http
?unread_only=true     # Tylko nieprzeczytane
?type=booking_request # Filter po typie
?per_page=20         # Ilość na stronę
?page=2              # Paginacja
```

## 🚀 Integracje

### BookingController Integration
```php
// Nowa rezerwacja
$sitter->notify(new BookingRequestNotification($booking));

// Potwierdzenie  
$booking->owner->notify(new BookingConfirmedNotification($booking));

// Ukończenie
$booking->owner->notify(new BookingCompletedNotification($booking));

// Anulowanie
$recipient->notify(new BookingCancelledNotification($booking, $cancelledBy));
```

### ReviewController Integration
```php
// Nowa opinia
$booking->sitter->notify(new NewReviewNotification($review));
```

## 📊 Monitoring i Analytics

### Tracking Wydarzenia
- Log all notification sends w Laravel logs
- Failed jobs tracking w queue:failed
- Database metrics: read/unread ratios

### Performance Metrics
- Email delivery rates
- Database notification read rates
- API response times dla endpoints

## 🔒 Bezpieczeństwo

### Autoryzacja
- Notifications middleware requires auth
- Users can only access own notifications  
- Admin notifications mają dodatkowo admin middleware

### Prywatność
- Anonymous reviews ukrywają dane właściciela
- Sensitive data nie są logged w plain text
- Email adresy są walidowane

## 🧪 Testowanie

### Unit Tests
```php
// Test powiadomień
php artisan test --filter NotificationTest

// Test kolejek
php artisan test --filter QueueTest
```

### Manual Testing
1. Stwórz booking → sprawdź email opiekuna
2. Potwierdź booking → sprawdź email właściciela  
3. Ukończ booking → sprawdź email + in-app notification
4. Napisz opinię → sprawdź email opiekuna
5. Sprawdź dzwonek powiadomień w navbar
6. Test filtrowania na stronie notifications

## 📈 Przyszłe Rozszerzenia

### Planowane Funkcjonalności
- **Push Notifications** - Browser push notifications
- **SMS Notifications** - Opcjonalne SMS dla ważnych wydarzeń
- **Slack/Discord** - Integracja z teamami
- **Notification Preferences** - Granular control co user chce otrzymywać

### Technical Improvements  
- **Redis Queue** - Dla lepszej wydajności
- **WebSockets** - Real-time notifications bez polling
- **Template Engine** - Bardziej zaawansowane email templates
- **A/B Testing** - Testing różnych email subjects/content

## 🛠️ Maintenance

### Daily Tasks
- Monitor queue health: `php artisan queue:monitor`
- Check failed jobs: `php artisan queue:failed`  
- Clear old notifications: custom command (recommend after 90 days)

### Scaling Considerations
- Redis dla session + cache gdy app rośnie
- Dedicated email service (SendGrid/Mailgun) dla production
- CDN dla static assets w email templates
- Database indexing na notifications table

---

## ✅ Status Implementacji

**✅ UKOŃCZONE:**
- [x] Backend notification classes
- [x] Database notifications table  
- [x] Queue worker setup
- [x] Frontend NotificationBell component
- [x] Full notifications page
- [x] API endpoints
- [x] Integration z BookingController i ReviewController
- [x] Email templates z polskimi tekstami
- [x] UI/UX design zgodny z aplikacją

**🔄 DZIAŁAJĄCE FUNKCJE:**
- Email notifications dla wszystkich wydarzeń booking/review
- In-app notifications z real-time polling
- Mark as read/unread functionality
- Bulk actions (mark all read, delete all read)
- Filtering and pagination
- Responsive design dla mobile/desktop

System powiadomień jest **w pełni funkcjonalny** i gotowy do użycia! 🎉