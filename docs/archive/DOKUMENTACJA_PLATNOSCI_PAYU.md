# Dokumentacja - System Płatności PayU

## Przegląd
System płatności zintegrowany z PayU umożliwia właścicielom zwierząt opłacanie rezerwacji u opiekunów.

## Architektura Systemu

### Modele
1. **Booking** - główny model rezerwacji
2. **Order** - zamówienie dla PayU (implementuje `PayuOrderInterface`)
3. **Payment** - płatność obsługiwana przez PayU

### Przepływ płatności
```
Booking → Order → Payment (PayU) → Potwierdzenie
```

## Struktura Plików

### Backend - Laravel

#### `app/Http/Controllers/PaymentController.php`
Główny kontroler obsługujący płatności PayU:

**Metody:**
- `createPayment()` - Utworzenie płatności dla rezerwacji
- `checkPaymentStatus()` - Sprawdzenie statusu płatności
- `confirmPayment()` - Potwierdzenie płatności (dla statusu WAITING_FOR_CONFIRMATION)
- `cancelPayment()` - Anulowanie płatności
- `refundPayment()` - Zwrot płatności
- `getPaymentMethods()` - Pobieranie dostępnych metod płatności

#### `app/Models/Order.php`
Model zamówienia implementujący interfejs PayU:
```php
class Order extends Model implements PayuOrderInterface
{
    // Wymagane metody PayU
    public function orderId();
    public function orderCost();
    public function orderFirstname();
    public function orderLastname();
    public function orderPhone();
    public function orderEmail();
}
```

#### `app/Models/Payment.php`
Model płatności dostosowany do struktury PayU:
- Używa pól tabeli PayU: `payu_id`, `status`, `cost`, `total`
- Accessor `getAmountAttribute()` dla kompatybilności
- Metody pomocnicze: `isPaid()`, `isPending()`, `markAsPaid()`

### Routes (`routes/web.php`)
```php
Route::prefix('payments')->group(function () {
    Route::post('booking/{booking}/create', [PaymentController::class, 'createPayment']);
    Route::get('booking/{booking}/status', [PaymentController::class, 'checkPaymentStatus']);
    Route::post('booking/{booking}/confirm', [PaymentController::class, 'confirmPayment']);
    Route::post('booking/{booking}/cancel', [PaymentController::class, 'cancelPayment']);
    Route::post('booking/{booking}/refund', [PaymentController::class, 'refundPayment']);
    Route::get('methods', [PaymentController::class, 'getPaymentMethods']);
});
```

### Frontend - Vue.js

#### `resources/js/Components/Payment/PaymentButton.vue`
Komponent płatności z pełną funkcjonalnością:

**Funkcjonalności:**
- Wyświetlanie informacji o płatności
- Przycisk "Pay with PayU"
- Otwieranie okna PayU w nowej karcie
- Monitorowanie zamknięcia okna płatności
- Sprawdzanie statusu płatności
- Anulowanie płatności
- Wyświetlanie dostępnych metod płatności
- Obsługa błędów i komunikatów sukcesu

**Props:**
```typescript
interface Props {
    booking: {
        id: number;
        total_amount: number;
        is_paid: boolean;
        status: string;
    };
}
```

**Events:**
- `@payment-completed` - płatność zakończona
- `@payment-cancelled` - płatność anulowana

## Konfiguracja

### Zmienne środowiskowe (.env)
```bash
# PayU Configuration (Sandbox)
PAYU_ENV=sandbox
PAYU_POS_ID=300746
PAYU_POS_MD5=b6ca15b0d1020e8094d9b5f8d163db54
PAYU_CLIENT_ID=300746
PAYU_CLIENT_SECRET=2ee86a66e5d97e3fadc400c9f19b065d
```

### Konfiguracja PayU (`config/payu.php`)
```php
return [
    'env' => env('PAYU_ENV', 'sandbox'),
    'pos_id' => env('PAYU_POS_ID', ''),
    'pos_md5' => env('PAYU_POS_MD5', ''),
    'client_id' => env('PAYU_CLIENT_ID', ''),
    'client_secret' => env('PAYU_CLIENT_SECRET', ''),
    'currency' => 'PLN',
    'enable' => true,
    'routes' => true,
    'migrations' => true,
];
```

## API Endpoints

### Utworzenie płatności
```http
POST /api/payments/booking/{booking_id}/create
```
**Response:**
```json
{
    "payment_url": "https://secure.snd.payu.com/pl/standard/user/oauth/authorize?response_type=code...",
    "order_id": 1,
    "message": "Payment URL created successfully"
}
```

### Sprawdzenie statusu
```http
GET /api/payments/booking/{booking_id}/status
```
**Response:**
```json
{
    "booking_id": 1,
    "order_id": 1,
    "payment_status": "COMPLETED",
    "is_paid": true,
    "latest_payment": {...}
}
```

## Statusy Płatności PayU

- `PENDING` - Oczekująca
- `WAITING_FOR_CONFIRMATION` - Oczekuje potwierdzenia
- `COMPLETED` - Zakończona pomyślnie  
- `CANCELED` - Anulowana
- `REJECTED` - Odrzucona
- `FAILED` - Nieudana
- `REFUNDED` - Zwrócona

## Proces Płatności

1. **Utworzenie zamówienia** - Tworzenie modelu Order z danymi rezerwacji
2. **Generowanie URL PayU** - Wywołanie `Payu::pay($order)`
3. **Przekierowanie klienta** - Otwieranie okna PayU
4. **Przetwarzanie płatności** - Klient dokonuje płatności w PayU
5. **Potwierdzenie** - Automatyczna aktualizacja statusu przez PayU
6. **Finalizacja** - Aktualizacja statusu rezerwacji

## Bezpieczeństwo

### Autoryzacja
- Tylko właściciel rezerwacji może utworzyć płatność
- Sprawdzanie uprawnień w każdym endpoincie
- Walidacja danych wejściowych

### Transakcje
- Używanie transakcji bazodanowych
- Rollback w przypadku błędów
- Logowanie wszystkich operacji

## Środowiska

### Sandbox (Testowe)
- URL: `https://secure.snd.payu.com/`
- Dane testowe: POS ID 300746
- Dostępne karty testowe w dokumentacji PayU

### Produkcja
- URL: `https://secure.payu.com/`
- Wymagane prawdziwe dane konta PayU
- Zmiana `PAYU_ENV=secure`

## Testowanie

### Test przepływu płatności
```bash
# 1. Utworzenie płatności
curl -X POST /api/payments/booking/1/create

# 2. Sprawdzenie statusu
curl -X GET /api/payments/booking/1/status

# 3. Potwierdzenie (jeśli wymagane)
curl -X POST /api/payments/booking/1/confirm
```

### Komponenty Vue.js
```vue
<template>
  <PaymentButton 
    :booking="booking" 
    @payment-completed="handlePaymentCompleted"
    @payment-cancelled="handlePaymentCancelled"
  />
</template>
```

## Monitorowanie

### Logi
Wszystkie operacje płatności są logowane:
- Tworzenie płatności
- Sprawdzanie statusu  
- Błędy i wyjątki
- Informacje debugowania

### Metryki
Można monitorować:
- Liczbę udanych płatności
- Czas przetwarzania
- Błędy integracji
- Anulowane płatności

## Rozwiązywanie Problemów

### Częste błędy
1. **"Payment creation failed"** - Sprawdź konfigurację PayU
2. **"No payment order found"** - Upewnij się, że Order został utworzony
3. **"Unauthorized"** - Sprawdź uprawnienia użytkownika
4. **"Booking already paid"** - Rezerwacja już opłacona

### Debugowanie
- Włączyć `APP_DEBUG=true`
- Sprawdzić logi w `storage/logs/laravel.log`
- Weryfikować konfigurację PayU
- Testować w środowisku sandbox

## Status Implementacji
✅ **Zakończone:**
- Instalacja i konfiguracja pakietu PayU
- Modele i migracje
- Kontroler płatności z wszystkimi metodami
- Routes API
- Komponent Vue.js PaymentButton
- Dokumentacja kompletna
- Konfiguracja sandbox

🔄 **Do testowania:**
- Pełny przepływ płatności
- Integracja z dashboardem użytkownika
- Obsługa różnych statusów