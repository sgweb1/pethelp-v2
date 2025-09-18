# Techniczne Detale Implementacji

*Kluczowe informacje techniczne z archiwum*

## 🏗️ Architektura Aplikacji

### Framework Stack
- **Backend**: Laravel 12.28.1
- **Frontend**: Livewire 3 + Volt (zmienione z Vue.js)
- **Database**: MySQL (zmienione z SQLite)
- **CSS**: Tailwind CSS
- **Auth**: Laravel Breeze

### Struktura Plików
```
app/
├── Models/
│   ├── Pet.php (rozszerzony)
│   ├── SitterProfile.php
│   ├── Booking.php
│   ├── MedicalRecord.php
│   ├── Vaccination.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── Order.php
│   └── Payment.php
├── Http/Controllers/
│   ├── PaymentController.php
│   ├── BookingController.php
│   ├── PetController.php
│   └── ConversationController.php
└── Services/
    ├── PaymentService.php
    └── NotificationService.php
```

## 📊 Modele Danych - Szczegóły

### Pet Model (30+ pola)
```php
// Podstawowe
'name', 'type', 'breed', 'birth_date', 'gender', 'weight'

// Wygląd
'color', 'markings', 'size', 'photo'

// Medyczne
'is_neutered', 'medical_conditions', 'medications', 'allergies'

// Behawioralne
'behavioral_notes', 'energy_level', 'socialization', 'training_level'

// Opieka
'feeding_instructions', 'exercise_requirements', 'grooming_notes'
```

### Relationships
```php
Pet::class
├── belongsTo(User) // owner
├── hasMany(MedicalRecord)
├── hasMany(Vaccination)
├── hasMany(EmergencyContact)
└── hasMany(Booking)

SitterProfile::class
├── belongsTo(User)
├── hasMany(Service)
├── hasMany(Availability)
└── hasMany(Review)
```

## 🔌 API Endpoints (25+)

### Pet Management
```
GET    /api/pets             - Lista zwierząt
POST   /api/pets             - Dodaj zwierzę
GET    /api/pets/{id}        - Szczegóły zwierzęcia
PUT    /api/pets/{id}        - Aktualizuj zwierzę
DELETE /api/pets/{id}        - Usuń zwierzę

// Medyczne
GET    /api/pets/{id}/medical-records
POST   /api/pets/{id}/medical-records
GET    /api/pets/{id}/vaccinations
POST   /api/pets/{id}/vaccinations
```

### Booking System
```
GET    /api/bookings         - Lista rezerwacji
POST   /api/bookings         - Nowa rezerwacja
PUT    /api/bookings/{id}/confirm
PUT    /api/bookings/{id}/cancel
GET    /api/bookings/{id}/status
```

### Payment Integration
```
POST   /api/payments/create
GET    /api/payments/{id}/status
POST   /api/payments/{id}/confirm
POST   /api/payments/{id}/refund
POST   /webhook/payu         - PayU webhook
```

## 💳 PayU Integration

### Konfiguracja
```php
// config/payu.php
'client_id' => env('PAYU_CLIENT_ID')
'client_secret' => env('PAYU_CLIENT_SECRET')
'sandbox' => env('PAYU_SANDBOX', true)
'merchant_pos_id' => env('PAYU_MERCHANT_POS_ID')
```

### Payment Flow
```php
// 1. Tworzenie zamówienia
$order = Order::createFromBooking($booking);

// 2. Płatność PayU
$payment = PaymentService::createPayment($order);

// 3. Redirect do PayU
return redirect($payment->redirectUri);

// 4. Webhook handling
PaymentService::handleWebhook($request);
```

## 💬 Chat System - Implementacja

### Real-time Updates
```php
// Broadcasting
Conversation::created → ConversationCreated
Message::created → MessageSent

// Listeners
MessageSent → UpdateUnreadCounts
MessageSent → SendEmailNotification
```

### Attachment Handling
```php
// Storage
'chat_attachments' => [
    'disk' => 'local',
    'max_size' => 10485760, // 10MB
    'allowed_types' => ['jpg', 'png', 'pdf', 'doc']
]
```

## 🔍 Search & Filtering

### Elasticsearch (opcjonalne)
```php
// Scout configuration dla zaawansowanego search
SitterProfile::search('doświadczony opiekun')
    ->where('city', 'Warszawa')
    ->where('price_min', '>=', 50)
    ->get();
```

### Database Queries
```php
// Geolocation search
SitterProfile::whereRaw(
    'ST_Distance_Sphere(
        POINT(longitude, latitude),
        POINT(?, ?)
    ) <= ?',
    [$userLon, $userLat, $radiusMeters]
);
```

## 📧 Notification System

### Mail Templates
```
emails/
├── booking/
│   ├── created.blade.php
│   ├── confirmed.blade.php
│   └── cancelled.blade.php
├── payment/
│   ├── successful.blade.php
│   └── failed.blade.php
└── reminder/
    ├── vaccination.blade.php
    └── medical-followup.blade.php
```

### Queue Jobs
```php
// app/Jobs/
SendBookingNotification::class
SendPaymentConfirmation::class
SendVaccinationReminder::class
ProcessPaymentWebhook::class
```

## 🧪 Testing Strategy

### Pokrycie (83%)
- **Backend**: 43 testy (PHPUnit)
- **Frontend**: 20 testów (Vitest)
- **E2E**: Planowane (Cypress)

### Test Categories
```php
// Feature Tests
BookingWorkflowTest::class
PaymentIntegrationTest::class
PetManagementTest::class

// Unit Tests
PaymentServiceTest::class
NotificationServiceTest::class
```

## 🚀 Deployment

### Requirements
- **PHP**: 8.3+
- **MySQL**: 8.0+
- **Redis**: 6+ (cache, sessions, queues)
- **Node.js**: 18+ (build tools)

### Production Stack
- **Server**: Laravel Forge + DigitalOcean
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Storage**: S3/DigitalOcean Spaces
- **CDN**: CloudFlare

**Więcej szczegółów w archiwum dokumentacji.**