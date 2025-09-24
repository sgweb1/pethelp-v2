# Service: PayUService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z pay-u-service.

## Lokalizacja
- **Plik**: `app/Services/PayUService.php`

## Methods
### createSubscriptionPayment()
Opis metody createSubscriptionPayment.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createSubscriptionPayment();
```

### handleNotification()
Opis metody handleNotification.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->handleNotification();
```

### verifySignature()
Opis metody verifySignature.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->verifySignature();
```

## Usage Example
```php
use App\Services\PayUService;

$service = app(PayUService::class);
// lub przez DI
public function __construct(private PayUService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*