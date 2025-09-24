# Service: NotificationService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z notification-service.

## Lokalizacja
- **Plik**: `app/Services/NotificationService.php`

## Methods
### createNotification()
Opis metody createNotification.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createNotification();
```

### notifyBookingCreated()
Opis metody notifyBookingCreated.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyBookingCreated();
```

### notifyBookingConfirmed()
Opis metody notifyBookingConfirmed.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyBookingConfirmed();
```

### notifyBookingCancelled()
Opis metody notifyBookingCancelled.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyBookingCancelled();
```

### notifyBookingCompleted()
Opis metody notifyBookingCompleted.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyBookingCompleted();
```

### notifyPaymentCompleted()
Opis metody notifyPaymentCompleted.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyPaymentCompleted();
```

### notifyPaymentFailed()
Opis metody notifyPaymentFailed.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyPaymentFailed();
```

### notifyBookingReminder()
Opis metody notifyBookingReminder.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyBookingReminder();
```

### getUserNotifications()
Opis metody getUserNotifications.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getUserNotifications();
```

### getUnreadCount()
Opis metody getUnreadCount.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getUnreadCount();
```

### markAllAsRead()
Opis metody markAllAsRead.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->markAllAsRead();
```

### markAsRead()
Opis metody markAsRead.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->markAsRead();
```

### notifyReviewReceived()
Opis metody notifyReviewReceived.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyReviewReceived();
```

### notifyMessageReceived()
Opis metody notifyMessageReceived.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->notifyMessageReceived();
```

### deleteOldNotifications()
Opis metody deleteOldNotifications.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->deleteOldNotifications();
```

## Usage Example
```php
use App\Services\NotificationService;

$service = app(NotificationService::class);
// lub przez DI
public function __construct(private NotificationService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*