# Struktura Bazy Danych PetHelp

## Schemat Relacyjny

### 1. USERS (Użytkownicy)
```sql
users:
- id (PK)
- name (varchar)
- email (unique)
- email_verified_at
- password
- role (enum: 'owner', 'caregiver', 'admin')
- phone (varchar, nullable)
- avatar (varchar, nullable)
- is_verified (boolean, default: false)
- is_active (boolean, default: true)
- created_at
- updated_at
```

### 2. USER_PROFILES (Profile użytkowników)
```sql
user_profiles:
- id (PK)
- user_id (FK -> users.id)
- first_name (varchar)
- last_name (varchar)
- bio (text, nullable)
- date_of_birth (date, nullable)
- gender (enum: 'male', 'female', 'other', nullable)
- address (varchar, nullable)
- city (varchar, nullable)
- postal_code (varchar, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- experience_years (integer, nullable) // dla opiekunów
- hourly_rate (decimal, nullable) // dla opiekunów
- availability_radius (integer, nullable) // km dla opiekunów
- emergency_contact (varchar, nullable)
- created_at
- updated_at
```

### 3. PETS (Zwierzęta)
```sql
pets:
- id (PK)
- owner_id (FK -> users.id)
- name (varchar)
- type (enum: 'dog', 'cat', 'bird', 'fish', 'rabbit', 'other')
- breed (varchar, nullable)
- age (integer)
- weight (decimal, nullable)
- size (enum: 'small', 'medium', 'large', 'extra_large')
- gender (enum: 'male', 'female')
- is_neutered (boolean, default: false)
- health_notes (text, nullable)
- special_needs (text, nullable)
- vaccination_status (text, nullable)
- avatar (varchar, nullable)
- is_active (boolean, default: true)
- created_at
- updated_at
```

### 4. SERVICE_CATEGORIES (Kategorie usług)
```sql
service_categories:
- id (PK)
- name (varchar)
- description (text, nullable)
- icon (varchar, nullable)
- is_active (boolean, default: true)
- sort_order (integer, default: 0)
- created_at
- updated_at
```

### 5. SERVICES (Usługi opiekunów)
```sql
services:
- id (PK)
- caregiver_id (FK -> users.id)
- category_id (FK -> service_categories.id)
- title (varchar)
- description (text)
- price_per_hour (decimal, nullable)
- price_per_day (decimal, nullable)
- price_per_visit (decimal, nullable)
- min_duration (integer) // minuty
- max_duration (integer, nullable) // minuty
- pet_types (json) // ['dog', 'cat'] - jakie zwierzęta
- max_pets_at_once (integer, default: 1)
- location_type (enum: 'caregiver_home', 'owner_home', 'both')
- is_available (boolean, default: true)
- is_featured (boolean, default: false)
- created_at
- updated_at
```

### 6. AVAILABILITY (Dostępność opiekunów)
```sql
availability:
- id (PK)
- caregiver_id (FK -> users.id)
- day_of_week (enum: 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
- start_time (time)
- end_time (time)
- is_available (boolean, default: true)
- created_at
- updated_at
```

### 7. BOOKINGS (Rezerwacje)
```sql
bookings:
- id (PK)
- owner_id (FK -> users.id)
- caregiver_id (FK -> users.id)
- service_id (FK -> services.id)
- pet_id (FK -> pets.id)
- start_date (datetime)
- end_date (datetime)
- status (enum: 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rejected')
- location_type (enum: 'caregiver_home', 'owner_home')
- address (varchar, nullable)
- special_instructions (text, nullable)
- total_amount (decimal)
- commission_amount (decimal)
- caregiver_amount (decimal)
- emergency_contact (varchar, nullable)
- notes (text, nullable)
- cancelled_by (FK -> users.id, nullable)
- cancellation_reason (text, nullable)
- cancelled_at (timestamp, nullable)
- confirmed_at (timestamp, nullable)
- started_at (timestamp, nullable)
- completed_at (timestamp, nullable)
- created_at
- updated_at
```

### 8. PAYMENTS (Płatności)
```sql
payments:
- id (PK)
- booking_id (FK -> bookings.id)
- payer_id (FK -> users.id)
- payee_id (FK -> users.id)
- payment_method (enum: 'payu', 'cash', 'transfer')
- status (enum: 'pending', 'processing', 'completed', 'failed', 'refunded')
- amount (decimal)
- commission (decimal)
- net_amount (decimal)
- currency (varchar, default: 'PLN')
- external_payment_id (varchar, nullable) // PayU transaction ID
- external_status (varchar, nullable)
- paid_at (timestamp, nullable)
- refunded_at (timestamp, nullable)
- created_at
- updated_at
```

### 9. REVIEWS (Oceny i opinie)
```sql
reviews:
- id (PK)
- booking_id (FK -> bookings.id)
- reviewer_id (FK -> users.id)
- reviewed_id (FK -> users.id)
- rating (integer) // 1-5
- title (varchar, nullable)
- comment (text, nullable)
- is_visible (boolean, default: true)
- response (text, nullable) // odpowiedź na opinię
- responded_at (timestamp, nullable)
- created_at
- updated_at
```

### 10. NOTIFICATIONS (Powiadomienia)
```sql
notifications:
- id (PK)
- user_id (FK -> users.id)
- type (enum: 'booking_request', 'booking_confirmed', 'booking_cancelled', 'payment_received', 'review_received', 'system')
- title (varchar)
- message (text)
- data (json, nullable) // dodatkowe dane
- is_read (boolean, default: false)
- is_email_sent (boolean, default: false)
- read_at (timestamp, nullable)
- created_at
- updated_at
```

### 11. LOCATIONS (Lokalizacje)
```sql
locations:
- id (PK)
- user_id (FK -> users.id)
- name (varchar)
- address (varchar)
- city (varchar)
- postal_code (varchar)
- latitude (decimal)
- longitude (decimal)
- is_default (boolean, default: false)
- created_at
- updated_at
```

### 12. REPORTS (Zgłoszenia)
```sql
reports:
- id (PK)
- reporter_id (FK -> users.id)
- reported_id (FK -> users.id)
- booking_id (FK -> bookings.id, nullable)
- type (enum: 'inappropriate_behavior', 'poor_service', 'safety_concern', 'other')
- description (text)
- status (enum: 'pending', 'investigating', 'resolved', 'rejected')
- admin_notes (text, nullable)
- resolved_by (FK -> users.id, nullable)
- resolved_at (timestamp, nullable)
- created_at
- updated_at
```

### 13. ADMIN_ACTIONS (Akcje administracyjne)
```sql
admin_actions:
- id (PK)
- admin_id (FK -> users.id)
- target_user_id (FK -> users.id)
- action_type (enum: 'warning', 'suspension', 'ban', 'verification', 'feature')
- reason (text)
- duration (integer, nullable) // dni suspensji
- expires_at (timestamp, nullable)
- is_active (boolean, default: true)
- created_at
- updated_at
```

### 14. FAVORITES (Ulubieni opiekunowie)
```sql
favorites:
- id (PK)
- owner_id (FK -> users.id)
- caregiver_id (FK -> users.id)
- created_at
- updated_at

UNIQUE(owner_id, caregiver_id)
```

### 15. MEDIA (Pliki medialne)
```sql
media:
- id (PK)
- model_type (varchar) // App\Models\User, App\Models\Pet
- model_id (bigint)
- collection_name (varchar) // 'avatar', 'gallery'
- name (varchar)
- file_name (varchar)
- mime_type (varchar)
- disk (varchar)
- size (bigint)
- created_at
- updated_at
```

## Indeksy Bazodanowe

### Indeksy dla wydajności:
```sql
-- Users
INDEX(email)
INDEX(role)
INDEX(is_active)

-- User Profiles
INDEX(city)
INDEX(latitude, longitude) // geospatial

-- Pets
INDEX(owner_id)
INDEX(type)

-- Services
INDEX(caregiver_id)
INDEX(category_id)
INDEX(is_available)
INDEX(pet_types) // JSON index

-- Bookings
INDEX(owner_id)
INDEX(caregiver_id)
INDEX(status)
INDEX(start_date)
INDEX(end_date)

-- Reviews
INDEX(reviewed_id)
INDEX(rating)
INDEX(is_visible)

-- Notifications
INDEX(user_id)
INDEX(is_read)
INDEX(type)
```

## Związki (Relationships)

### User Model:
- hasOne(UserProfile)
- hasMany(Pet) // jako owner
- hasMany(Service) // jako caregiver
- hasMany(BookingAsOwner)
- hasMany(BookingAsCaregiver)
- hasMany(Review) // otrzymane
- hasMany(Notification)

### Pet Model:
- belongsTo(User) // owner
- hasMany(Booking)

### Service Model:
- belongsTo(User) // caregiver
- belongsTo(ServiceCategory)
- hasMany(Booking)

### Booking Model:
- belongsTo(User) // owner
- belongsTo(User) // caregiver
- belongsTo(Service)
- belongsTo(Pet)
- hasOne(Payment)
- hasMany(Review)

## Seeders i Factory

### Dane przykładowe:
1. **Admin user** - superadmin@pethelp.pl
2. **Service categories** - Spacery, Opieka, Wizyta weterynaryjna
3. **Test users** - 10 właścicieli, 10 opiekunów
4. **Test pets** - 20 zwierząt różnych typów
5. **Test services** - 15 usług od opiekunów
6. **Test bookings** - 25 rezerwacji w różnych statusach
7. **Test reviews** - 20 opinii

### Factory definitions:
- UserFactory (role-specific)
- PetFactory (realistic pet data)
- ServiceFactory (varied services)
- BookingFactory (realistic booking scenarios)

**Status: GOTOWE DO IMPLEMENTACJI** ✅