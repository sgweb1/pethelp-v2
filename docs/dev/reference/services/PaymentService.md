# Service: PaymentService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z payment-service.

## Lokalizacja
- **Plik**: `app/Services/PaymentService.php`

## Methods
### createPayment()
Opis metody createPayment.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createPayment();
```

### processPayment()
Opis metody processPayment.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->processPayment();
```

### refundPayment()
Opis metody refundPayment.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->refundPayment();
```

### calculateCommission()
Opis metody calculateCommission.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->calculateCommission();
```

### getSitterAmount()
Opis metody getSitterAmount.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getSitterAmount();
```

### getPaymentMethods()
Opis metody getPaymentMethods.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getPaymentMethods();
```

### getPaymentStatus()
Opis metody getPaymentStatus.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getPaymentStatus();
```

## Usage Example
```php
use App\Services\PaymentService;

$service = app(PaymentService::class);
// lub przez DI
public function __construct(private PaymentService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*