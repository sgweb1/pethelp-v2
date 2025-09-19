# API Reference - System Subskrypcji

## Spis treści
1. [Uwierzytelnianie](#uwierzytelnianie)
2. [Endpointy publiczne](#endpointy-publiczne)
3. [Endpointy autoryzowane](#endpointy-autoryzowane)
4. [Webhook PayU](#webhook-payu)
5. [Kody błędów](#kody-błędów)
6. [Przykłady implementacji](#przykłady-implementacji)

## Uwierzytelnianie

System używa standardowej sesji Laravel dla endpointów webowych oraz opcjonalnie API tokens dla endpointów JSON.

### Sesja webowa
```bash
# Logowanie przez formularz
POST /login
{
    "email": "user@example.com",
    "password": "password"
}
```

### API Token (opcjonalne)
```bash
# Dodanie tokenu do nagłówków
Authorization: Bearer your-api-token
Accept: application/json
```

## Endpointy publiczne

### GET /subscription/plans
Strona z planami cenowymi (Livewire component).

**Response**: HTML page
```html
<!DOCTYPE html>
<html>
<!-- Pełna strona z komponentem PricingPage -->
</html>
```

### POST /payu/notify
Webhook PayU do obsługi notyfikacji o płatnościach.

**Headers**:
```
Content-Type: application/json
OpenPayu-Signature: SHA256_SIGNATURE
```

**Request Body**:
```json
{
    "order": {
        "orderId": "PAYU_ORDER_123",
        "orderCreateDate": "2025-01-19T10:30:00.000Z",
        "notifyUrl": "https://pethelp.pl/payu/notify",
        "customerIp": "192.168.1.1",
        "merchantPosId": "300746",
        "description": "Subskrypcja Pro - PetHelp",
        "currencyCode": "PLN",
        "totalAmount": "4900",
        "status": "COMPLETED",
        "buyer": {
            "email": "user@example.com",
            "firstName": "Jan",
            "lastName": "Kowalski"
        },
        "products": [
            {
                "name": "Plan Pro",
                "unitPrice": "4900",
                "quantity": "1"
            }
        ]
    }
}
```

**Response (Success)**:
```json
{
    "status": "OK"
}
```

**Response (Error)**:
```json
{
    "error": "Processing failed"
}
```

## Endpointy autoryzowane

Wszystkie endpointy wymagają uwierzytelnienia (`auth` middleware).

### GET /subscription/dashboard
Dashboard zarządzania subskrypcją (Livewire component).

**Response**: HTML page z komponentem Dashboard

### POST /subscription/subscribe/{plan}
Subskrypcja wybranego planu.

**Parameters**:
- `plan` (SubscriptionPlan) - Model planu subskrypcji

**Request** (dla planów płatnych):
```http
POST /subscription/subscribe/2
Content-Type: application/x-www-form-urlencoded

_token=csrf_token_here
```

**Response (Plan darmowy)**:
```http
HTTP/1.1 302 Found
Location: /subscription/dashboard
Session: success="Plan podstawowy został aktywowany!"
```

**Response (Plan płatny - sukces)**:
```http
HTTP/1.1 302 Found
Location: https://secure.snd.payu.com/pay/12345
```

**Response (Plan płatny - błąd)**:
```http
HTTP/1.1 302 Found
Location: /subscription/plans
Session: error="Nie udało się utworzyć płatności. Spróbuj ponownie."
```

**JSON Response (Plan darmowy)**:
```json
{
    "success": true,
    "message": "Plan podstawowy został aktywowany",
    "subscription_id": 123
}
```

**JSON Response (Plan płatny - sukces)**:
```json
{
    "success": true,
    "payment_id": 456,
    "redirect_url": "https://secure.snd.payu.com/pay/12345",
    "order_id": "PAYU_ORDER_123"
}
```

**JSON Response (Błąd - już posiada plan)**:
```json
{
    "error": "Już posiadasz ten plan"
}
```

### GET /subscription/payment/success
Strona potwierdzenia udanej płatności.

**Query Parameters**:
- `orderId` (string, optional) - ID zamówienia PayU

**Response (Znaleziono płatność)**:
```http
HTTP/1.1 302 Found
Location: /subscription/dashboard
Session: success="Płatność została zrealizowana pomyślnie! Twoja subskrypcja jest już aktywna."
```

**Response (Nie znaleziono / w trakcie)**:
```http
HTTP/1.1 302 Found
Location: /subscription/dashboard
Session: info="Płatność jest w trakcie przetwarzania. Status zostanie zaktualizowany wkrótce."
```

### GET /subscription/payment/cancel
Strona anulowania płatności.

**Response**:
```http
HTTP/1.1 302 Found
Location: /subscription/plans
Session: warning="Płatność została anulowana. Możesz spróbować ponownie."
```

### GET /subscription/payments/{payment}/status
Status konkretnej płatności (JSON API).

**Parameters**:
- `payment` (Payment) - Model płatności

**Headers**:
```
Accept: application/json
```

**Response (Success)**:
```json
{
    "status": "completed",
    "external_status": "COMPLETED",
    "amount": 49.00,
    "currency": "PLN",
    "created_at": "2025-01-19T10:30:00.000Z",
    "paid_at": "2025-01-19T10:35:00.000Z",
    "failed_at": null,
    "failure_reason": null
}
```

**Response (Unauthorized)**:
```json
{
    "error": "Unauthorized"
}
```

## Webhook PayU

### Bezpieczeństwo webhook'a

PayU weryfikuje autentyczność webhook'ów poprzez sygnaturę w nagłówku `OpenPayu-Signature`.

### Obsługiwane statusy PayU

| Status PayU | Status w systemie | Akcja |
|-------------|-------------------|--------|
| NEW | pending | Utworzenie płatności |
| PENDING | pending | Płatność w trakcie |
| WAITING_FOR_CONFIRMATION | pending | Oczekuje potwierdzenia |
| COMPLETED | completed | ✅ Aktywacja subskrypcji |
| CANCELED | failed | ❌ Anulowanie płatności |
| REJECTED | failed | ❌ Odrzucenie płatności |

### Flow webhook'a

1. **PayU wysyła notyfikację** → `POST /payu/notify`
2. **System weryfikuje sygnaturę** → `PayUService::verifySignature()`
3. **Aktualizacja statusu płatności** → `PayUService::updatePaymentStatus()`
4. **Jeśli COMPLETED**:
   - Anulowanie poprzednich subskrypcji użytkownika
   - Utworzenie nowej aktywnej subskrypcji
   - Linkowanie płatności z subskrypcją
5. **Logowanie eventu** → `Log::info()`

### Retry policy

PayU ponawia webhook'i w przypadku braku odpowiedzi lub błędu:
- Natychmiast po pierwszym błędzie
- Po 15 minutach
- Po 1 godzinie
- Po 6 godzinach
- Po 24 godzinach

System powinien zwracać status 200 z `{"status": "OK"}` dla prawidłowo przetworzonych webhook'ów.

## Kody błędów

### HTTP Status Codes

| Kod | Znaczenie | Kiedy występuje |
|-----|-----------|-----------------|
| 200 | OK | Sukces (webhook, API) |
| 302 | Found | Redirect (web endpoints) |
| 400 | Bad Request | Błędne dane webhook'a |
| 401 | Unauthorized | Brak uwierzytelnienia |
| 403 | Forbidden | Brak dostępu do płatności innego użytkownika |
| 500 | Server Error | Błąd wewnętrzny serwera |

### Application Error Codes

#### Subskrypcja

```json
{
    "error": "Już posiadasz ten plan",
    "code": "PLAN_ALREADY_ACTIVE"
}
```

```json
{
    "error": "Wymagane logowanie",
    "code": "AUTHENTICATION_REQUIRED"
}
```

#### Funkcje premium

```json
{
    "message": "Ta funkcja jest dostępna tylko w planach premium.",
    "feature": "advanced_search",
    "upgrade_url": "/subscription/plans",
    "code": "FEATURE_REQUIRES_PREMIUM"
}
```

#### Limity ogłoszeń

```json
{
    "message": "Osiągnięto limit ogłoszeń dla Twojego planu.",
    "current_count": 3,
    "max_listings": 3,
    "upgrade_url": "/subscription/plans",
    "code": "LISTING_LIMIT_REACHED"
}
```

#### PayU Integration

```json
{
    "error": "Nie udało się utworzyć płatności. Spróbuj ponownie.",
    "code": "PAYMENT_CREATION_FAILED"
}
```

```json
{
    "error": "Invalid request",
    "code": "WEBHOOK_INVALID_DATA"
}
```

## Przykłady implementacji

### JavaScript - Sprawdzenie statusu płatności

```javascript
async function checkPaymentStatus(paymentId) {
    try {
        const response = await fetch(`/subscription/payments/${paymentId}/status`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            console.log('Payment status:', data.status);
            return data;
        } else {
            console.error('Error:', data.error);
            return null;
        }
    } catch (error) {
        console.error('Network error:', error);
        return null;
    }
}

// Użycie
checkPaymentStatus(123).then(status => {
    if (status && status.status === 'completed') {
        window.location.href = '/subscription/dashboard';
    }
});
```

### cURL - Subskrypcja planu (z sesją)

```bash
# 1. Logowanie i pobranie cookie sesji
curl -c cookies.txt -X POST 'http://pethelp.test/login' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'email=user@example.com&password=password&_token=CSRF_TOKEN'

# 2. Subskrypcja planu Pro
curl -b cookies.txt -X POST 'http://pethelp.test/subscription/subscribe/2' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d '_token=CSRF_TOKEN'

# 3. Sprawdzenie dashboard'a
curl -b cookies.txt 'http://pethelp.test/subscription/dashboard'
```

### PHP - Webhook handler (zewnętrzny system)

```php
<?php

// Odbieranie webhook'a PayU w zewnętrznym systemie
$webhookData = json_decode(file_get_contents('php://input'), true);
$signature = $_SERVER['HTTP_OPENPAYU_SIGNATURE'] ?? '';

if (!$webhookData || !$signature) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Przekazanie do systemu PetHelp
$response = file_get_contents('https://pethelp.pl/payu/notify', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'OpenPayu-Signature: ' . $signature
        ],
        'content' => json_encode($webhookData)
    ]
]));

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Forwarding failed']);
    exit;
}

echo $response;
?>
```

### Laravel Livewire - Komponent subskrypcji

```php
<?php

namespace App\Livewire;

use App\Traits\HasSubscriptionChecks;
use Livewire\Component;

class MyPremiumComponent extends Component
{
    use HasSubscriptionChecks;

    public function mount()
    {
        // Sprawdzenie dostępu przy inicjalizacji
        if (!$this->requiresFeature('advanced_search')) {
            session()->flash('error', 'Ta funkcja wymaga planu premium.');
            return redirect()->route('subscription.plans');
        }
    }

    public function performPremiumAction()
    {
        if (!$this->requiresFeature('analytics')) {
            session()->flash('error', 'Analityka dostępna tylko w planach premium.');
            return;
        }

        // Wykonanie akcji premium
        // ...
    }

    public function render()
    {
        $userInfo = $this->getUserSubscriptionInfo();

        return view('livewire.my-premium-component', [
            'userInfo' => $userInfo
        ]);
    }
}
```

### Blade - Integracja z komponentami

```blade
{{-- resources/views/some-page.blade.php --}}
<div class="page-content">
    <h1>Moja strona</h1>

    {{-- Sprawdzenie funkcji premium --}}
    @hasFeature('advanced_search')
        <div class="premium-section">
            <h2>Zaawansowane wyszukiwanie</h2>
            <!-- Formularz zaawansowanego wyszukiwania -->
        </div>
    @else
        <x-subscription.upgrade-prompt
            feature="advanced_search"
            title="Odblokuj zaawansowane wyszukiwanie"
            description="Znajdź dokładnie to, czego szukasz dzięki zaawansowanym filtrom."
        />
    @endhasFeature

    {{-- Sprawdzenie limitów ogłoszeń --}}
    @canCreateListing
        <a href="/advertisements/create" class="btn btn-primary">
            Dodaj ogłoszenie
        </a>
    @else
        <div class="alert alert-warning">
            <p>Osiągnięto limit ogłoszeń ({{ auth()->user()->advertisements->count() }}/{{ auth()->user()->activeSubscription?->subscriptionPlan->max_listings ?? 3 }}).</p>
            <x-subscription.feature-badge feature="unlimited_listings" />
        </div>
    @endcanCreateListing

    {{-- Meter wykorzystania --}}
    @auth
        <x-subscription.usage-meter
            :current="auth()->user()->advertisements->count()"
            :limit="auth()->user()->activeSubscription?->subscriptionPlan->max_listings"
            label="Wykorzystane ogłoszenia"
        />
    @endauth
</div>
```

### Axios - Subskrypcja przez AJAX

```javascript
// Frontend JavaScript dla subskrypcji
class SubscriptionManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    async subscribeToPlan(planId) {
        try {
            const response = await axios.post(`/subscription/subscribe/${planId}`, {
                _token: this.csrfToken
            }, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.data.success) {
                if (response.data.redirect_url) {
                    // Plan płatny - redirect do PayU
                    window.location.href = response.data.redirect_url;
                } else {
                    // Plan darmowy - redirect do dashboard
                    window.location.href = '/subscription/dashboard';
                }
            }

        } catch (error) {
            if (error.response && error.response.data) {
                alert(error.response.data.error || 'Wystąpił błąd podczas subskrypcji.');
            } else {
                alert('Wystąpił błąd sieciowy.');
            }
        }
    }

    async checkSubscriptionStatus() {
        try {
            const response = await axios.get('/subscription/dashboard', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            return response.data;
        } catch (error) {
            console.error('Error checking subscription:', error);
            return null;
        }
    }
}

// Użycie
const subscriptionManager = new SubscriptionManager();

// Event listener dla przycisków subskrypcji
document.querySelectorAll('.subscribe-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const planId = btn.dataset.planId;
        subscriptionManager.subscribeToPlan(planId);
    });
});
```

---

## Dodatkowe informacje

### Rate Limiting
Standardowe rate limiting Laravel (60 requestów/minutę dla web, 60/minutę dla API).

### CORS
Domyślnie system nie obsługuje CORS dla API. W przypadku potrzeby integracji z zewnętrznymi domenami, należy skonfigurować `config/cors.php`.

### Wersjonowanie API
Obecna wersja: v1 (implicit)
W przyszłości API może być wersjonowane poprzez prefix `/api/v2/subscription/...`

### Dokumentacja OpenAPI
W przyszłości można wygenerować specyfikację OpenAPI/Swagger dla endpointów JSON.