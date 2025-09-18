# 🔔 System Powiadomień - PetHelp

## Przegląd systemu

System powiadomień w PetHelp umożliwia wysyłanie powiadomień w czasie rzeczywistym, email oraz zarządzanie powiadomieniami przez użytkowników.

## Architektura

### Backend (Laravel)
- **Notifications**: Klasy powiadomień Laravel z obsługą email + database + real-time
- **Events**: Laravel Events do broadcastingu WebSocket
- **Controllers**: API endpoints do zarządzania powiadomieniami
- **Queue**: Przetwarzanie asynchroniczne powiadomień

### Frontend (Vue.js + TypeScript)
- **NotificationBell**: Komponent dropdown w headerze
- **Real-time**: Laravel Echo + Pusher WebSocket integration
- **Browser Notifications**: Native browser notifications API

### Real-time (Pusher/WebSocket)
- **Channels**: Prywatne kanały dla każdego użytkownika
- **Broadcasting**: Automatyczne wysyłanie powiadomień WebSocket
- **Echo**: Frontend listener do real-time updates

## Komponenty

### 🎯 Backend Components

#### Klasy powiadomień
```php
// app/Notifications/BookingRequestNotification.php
// app/Notifications/BookingConfirmedNotification.php
```
- Implementują `ShouldQueue` dla wydajności
- Obsługują kanały: `mail`, `database`
- Automatyczny broadcasting przez `NewNotification` event

#### Event Broadcasting
```php
// app/Events/NewNotification.php
```
- Implements `ShouldBroadcast`
- Kanał: `user-notifications.{userId}`
- Event: `notification.created`

#### Controllers
```php
// app/Http/Controllers/NotificationController.php
// app/Http/Controllers/TestNotificationController.php
```

### 🎨 Frontend Components

#### NotificationBell Component
```vue
<!-- resources/js/Components/Notifications/NotificationBell.vue -->
```
**Funkcjonalności:**
- Dropdown z listą powiadomień
- Real-time updates przez Echo
- Browser notifications
- Mark as read/unread
- Delete notifications
- Unread counter z animacją

#### Echo Integration
```javascript
// resources/js/bootstrap.js
window.Echo = new Echo({
    broadcaster: 'pusher',
    // ... konfiguracja
});
```

### 🛠 Konfiguracja

#### Environment Variables
```env
# Broadcasting
BROADCAST_CONNECTION=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu

# Vite Pusher Configuration
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

#### Broadcasting Channels
```php
// routes/channels.php
Broadcast::channel('user-notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

## API Endpoints

### Zarządzanie powiadomieniami
```
GET    /api/notifications              # Lista powiadomień (paginated)
GET    /api/notifications/unread-count # Liczba nieprzeczytanych
POST   /api/notifications/{id}/read    # Oznacz jako przeczytane
POST   /api/notifications/mark-all-read # Oznacz wszystkie jako przeczytane
DELETE /api/notifications/{id}         # Usuń powiadomienie
DELETE /api/notifications/read/all     # Usuń wszystkie przeczytane
GET    /api/notifications/preferences  # Preferencje powiadomień
PUT    /api/notifications/preferences  # Aktualizuj preferencje
```

### Test endpoints (development only)
```
POST /api/test/notification             # Wysłanie testowego powiadomienia
GET  /api/test/browser-notification     # Test browser notification
GET  /test-notifications                # Strona testowa systemu
```

## Typy powiadomień

### 📋 Booking Request
```php
BookingRequestNotification::class
```
- **Trigger**: Nowe żądanie rezerwacji
- **Recipients**: Sitter
- **Channels**: Email + Database + Real-time
- **Icon**: 📋, **Color**: blue

### ✅ Booking Confirmed
```php
BookingConfirmedNotification::class
```
- **Trigger**: Potwierdzenie rezerwacji
- **Recipients**: Pet Owner
- **Channels**: Email + Database + Real-time
- **Icon**: ✅, **Color**: green

## Użycie w kodzie

### Wysyłanie powiadomienia
```php
use App\Notifications\BookingRequestNotification;

$sitter->notify(new BookingRequestNotification($booking));
```

### Real-time listening (Frontend)
```javascript
window.Echo.private(`user-notifications.${userId}`)
    .listen('.notification.created', (e) => {
        // Handle real-time notification
        console.log('New notification:', e.notification);
    });
```

### Browser notifications
```javascript
if (Notification.permission === 'granted') {
    new Notification(title, {
        body: message,
        icon: '/icon-192.png'
    });
}
```

## Queue Management

### Uruchamianie queue worker
```bash
php artisan queue:work --timeout=60
```

### Monitoring jobs
```bash
php artisan queue:failed    # Failed jobs
php artisan queue:retry all # Retry failed jobs
```

## Testing

### Strona testowa
Dostępna pod: `/test-notifications`

**Funkcjonalności testowe:**
- Wysyłanie różnych typów powiadomień
- Test browser notifications
- Status połączenia WebSocket
- Historia otrzymanych powiadomień w czasie rzeczywistym

### Manual testing
```bash
# Przez Tinker
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->notify(new App\Notifications\BookingRequestNotification($booking));
```

## Troubleshooting

### Pusher Connection Issues
1. Sprawdź credentials w `.env`
2. Sprawdź cluster (eu, us, ap-southeast-1)
3. Sprawdź network/firewall restrictions

### Queue nie przetwarza
```bash
php artisan queue:work --timeout=60 --tries=3
php artisan queue:failed
```

### Echo nie łączy
1. Sprawdź `VITE_PUSHER_*` variables
2. Sprawdź czy `window.authUserId` jest ustawione
3. Check browser console for WebSocket errors

### Browser notifications nie działają
1. Sprawdź permissions: `Notification.permission`
2. Test tylko na HTTPS (production) lub localhost
3. Niektóre przeglądarki blokują na HTTP

## Security

### Channel Authorization
- Prywatne kanały wymagają autoryzacji
- User może słuchać tylko własnych powiadomień
- Authorization w `routes/channels.php`

### CSRF Protection
- Wszystkie API endpoints zabezpieczone
- Automatic CSRF token w Axios headers

### XSS Prevention
- Dane powiadomień nie zawierają raw HTML
- Vue templates automatycznie escapują dane

## Performance

### Optimizations
- **Queue**: Powiadomienia email są asynchroniczne
- **Pagination**: API zwraca powiadomienia po 15/page
- **Caching**: Unread count cached przez 30s
- **Lazy loading**: Dropdown ładuje dane only when opened

### Monitoring
- Queue jobs metrics
- WebSocket connection health
- Notification delivery rates
- Email bounce rates

## Rozszerzenia

### Dodanie nowego typu powiadomienia

1. **Stwórz klasę powiadomienia**
```php
php artisan make:notification NewReviewNotification
```

2. **Dodaj broadcasting support**
```php
public function toDatabase($notifiable): array {
    $data = [...];
    
    $notification = $notifiable->notifications()->create([...]);
    broadcast(new NewNotification($notification))->toOthers();
    
    return $data;
}
```

3. **Dodaj typ do UI**
```javascript
// W getTypeBadgeClass() i getTypeLabel()
'new_review': 'bg-yellow-100 text-yellow-800'
```

### Push Notifications (Mobile)
- Integracja z `laravel-notification-channels/pusher-push-notifications`
- FCM dla Android/iOS
- Service Workers dla PWA

### Advanced Features
- **Notification Preferences**: Per-type preferences
- **Digest Notifications**: Daily/weekly summary emails  
- **Rich Notifications**: Images, actions, custom layouts
- **Notification History**: Archive/search funkcjonalność

## Dependencies

### Backend
```json
{
    "pusher/pusher-php-server": "^7.2",
    "laravel-notification-channels/pusher-push-notifications": "^4.3"
}
```

### Frontend
```json
{
    "pusher-js": "^8.0",
    "laravel-echo": "^1.15"
}
```