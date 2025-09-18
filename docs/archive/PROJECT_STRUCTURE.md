# PetHelp - Kompletna Rozpiska Projektowa Laravel

## 🎯 Założenia Projektu

### Podstawowe Info
- **Nazwa:** PetHelp (Polski Rover)
- **Framework:** Laravel 11 + Inertia.js + Vue 3 + TypeScript
- **Cel:** Platforma łącząca właścicieli zwierząt z opiekunami
- **MVP Timeline:** 3-4 miesiące
- **UI/UX Inspiration:** [Rover.com](https://www.rover.com/) - clean, pet-focused design

### Core Features MVP
1. ✅ Rejestracja użytkowników (właściciele + opiekunowie)
2. ✅ Profile zwierząt i opiekunów z weryfikacją
3. ✅ Wyszukiwanie opiekunów (lokalizacja + dostępność)
4. ✅ System rezerwacji z płatnościami
5. ✅ Chat między użytkownikami
6. ✅ System recenzji i ocen
7. ✅ Panel administratora

---

## 🏗️ Tech Stack

```bash
Backend:
├── Laravel 11 (PHP 8.2+)
├── PostgreSQL 15+ 
├── Redis 7+
├── Meilisearch (search engine)
└── Stripe (payments)

Frontend:
├── Inertia.js + Vue 3 + TypeScript
├── TailwindCSS + HeadlessUI
├── Pinia (state management)
└── Vue3-Google-Map

Tools:
├── Laravel Horizon (queue monitoring)
├── Laravel Telescope (debugging)
├── Spatie Media Library (files)
└── Laravel Broadcasting (real-time)
```

---

## 📁 Struktura Projektu

```
pethelp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Admin/
│   │   │   ├── Api/
│   │   │   ├── BookingController.php
│   │   │   ├── SitterController.php
│   │   │   ├── SearchController.php
│   │   │   ├── MessageController.php
│   │   │   └── PaymentController.php
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   │   ├── StoreBookingRequest.php
│   │   │   ├── UpdateSitterProfileRequest.php
│   │   │   └── SearchSittersRequest.php
│   │   └── Resources/
│   │       ├── SitterResource.php
│   │       ├── BookingResource.php
│   │       └── PetResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── SitterProfile.php
│   │   ├── Pet.php
│   │   ├── Booking.php
│   │   ├── BookingMessage.php
│   │   ├── Review.php
│   │   ├── WalkSession.php
│   │   └── SitterAvailability.php
│   ├── Services/
│   │   ├── SitterSearchService.php
│   │   ├── BookingService.php
│   │   ├── PaymentService.php
│   │   ├── NotificationService.php
│   │   └── VerificationService.php
│   ├── Jobs/
│   │   ├── SendBookingNotification.php
│   │   ├── ProcessPayment.php
│   │   ├── VerifyUser.php
│   │   └── GenerateWalkReport.php
│   ├── Events/
│   │   ├── BookingCreated.php
│   │   ├── BookingStatusChanged.php
│   │   ├── NewMessage.php
│   │   └── WalkStarted.php
│   ├── Notifications/
│   │   ├── BookingConfirmed.php
│   │   ├── NewBookingRequest.php
│   │   └── PaymentReceived.php
│   └── Policies/
│       ├── BookingPolicy.php
│       ├── SitterProfilePolicy.php
│       └── PetPolicy.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_02_000000_create_sitter_profiles_table.php
│   │   ├── 2024_01_03_000000_create_pets_table.php
│   │   ├── 2024_01_04_000000_create_bookings_table.php
│   │   ├── 2024_01_05_000000_create_booking_pet_table.php
│   │   ├── 2024_01_06_000000_create_booking_messages_table.php
│   │   ├── 2024_01_07_000000_create_reviews_table.php
│   │   ├── 2024_01_08_000000_create_walk_sessions_table.php
│   │   ├── 2024_01_09_000000_create_sitter_availabilities_table.php
│   │   └── 2024_01_10_000000_create_media_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── SitterSeeder.php
│   │   └── RolePermissionSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── SitterProfileFactory.php
│       ├── PetFactory.php
│       └── BookingFactory.php
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── UI/
│   │   │   │   ├── Button.vue
│   │   │   │   ├── Input.vue
│   │   │   │   ├── Modal.vue
│   │   │   │   └── Avatar.vue
│   │   │   ├── Forms/
│   │   │   │   ├── SitterProfileForm.vue
│   │   │   │   ├── PetForm.vue
│   │   │   │   └── BookingForm.vue
│   │   │   ├── Cards/
│   │   │   │   ├── SitterCard.vue
│   │   │   │   ├── BookingCard.vue
│   │   │   │   └── PetCard.vue
│   │   │   ├── Map/
│   │   │   │   ├── SittersMap.vue
│   │   │   │   └── WalkTracker.vue
│   │   │   ├── Chat/
│   │   │   │   ├── ChatWindow.vue
│   │   │   │   └── MessageBubble.vue
│   │   │   └── Search/
│   │   │       ├── SearchFilters.vue
│   │   │       └── SearchResults.vue
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   ├── Register.vue
│   │   │   │   └── Verify.vue
│   │   │   ├── Dashboard/
│   │   │   │   ├── Owner.vue
│   │   │   │   └── Sitter.vue
│   │   │   ├── Search.vue
│   │   │   ├── SitterProfile.vue
│   │   │   ├── Booking/
│   │   │   │   ├── Create.vue
│   │   │   │   ├── Show.vue
│   │   │   │   └── Index.vue
│   │   │   ├── Messages/
│   │   │   │   └── Index.vue
│   │   │   ├── Profile/
│   │   │   │   ├── Edit.vue
│   │   │   │   └── Pets.vue
│   │   │   └── Admin/
│   │   │       ├── Dashboard.vue
│   │   │       ├── Users.vue
│   │   │       └── Bookings.vue
│   │   ├── Layouts/
│   │   │   ├── AppLayout.vue
│   │   │   ├── AuthLayout.vue
│   │   │   └── AdminLayout.vue
│   │   ├── Stores/
│   │   │   ├── auth.js
│   │   │   ├── booking.js
│   │   │   ├── chat.js
│   │   │   └── search.js
│   │   ├── Composables/
│   │   │   ├── useGeolocation.js
│   │   │   ├── useWebSocket.js
│   │   │   ├── usePayments.js
│   │   │   └── useNotifications.js
│   │   └── app.js
│   ├── views/
│   │   └── app.blade.php
│   └── css/
│       └── app.css
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── channels.php
│   └── console.php
├── config/
│   ├── services.php
│   ├── broadcasting.php
│   ├── scout.php
│   └── cashier.php
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Booking/
│   │   ├── Search/
│   │   └── Payment/
│   └── Unit/
│       ├── Models/
│       ├── Services/
│       └── Jobs/
└── storage/
    └── app/
        ├── public/
        │   ├── pets/
        │   ├── sitters/
        │   └── walk-maps/
        └── temp/
```

---

## 🗄️ Schema Bazy Danych

### 1. Tabela Users

```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE,
    email_verified_at TIMESTAMP,
    phone_verified_at TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    background_check_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    stripe_id VARCHAR(255),
    pm_type VARCHAR(255),
    pm_last_four VARCHAR(4),
    trial_ends_at TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    last_seen_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);
CREATE INDEX idx_users_background_check ON users(background_check_status);
```

### 2. Tabela Sitter Profiles

```sql
CREATE TABLE sitter_profiles (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    bio TEXT,
    experience_years INTEGER DEFAULT 0,
    max_pets INTEGER DEFAULT 1,
    home_type ENUM('apartment', 'house', 'farm') DEFAULT 'apartment',
    has_yard BOOLEAN DEFAULT false,
    emergency_transport BOOLEAN DEFAULT false,
    hourly_rate DECIMAL(8,2) NOT NULL,
    overnight_rate DECIMAL(8,2),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    address TEXT,
    city VARCHAR(255),
    postal_code VARCHAR(10),
    available_services JSONB DEFAULT '[]',
    preferred_pet_sizes JSONB DEFAULT '[]',
    is_active BOOLEAN DEFAULT true,
    verified_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id)
);

CREATE INDEX idx_sitter_location ON sitter_profiles USING GIST(ST_Point(longitude, latitude));
CREATE INDEX idx_sitter_city ON sitter_profiles(city);
CREATE INDEX idx_sitter_active ON sitter_profiles(is_active);
CREATE INDEX idx_sitter_verified ON sitter_profiles(verified_at);
CREATE INDEX idx_sitter_services ON sitter_profiles USING GIN(available_services);
```

### 3. Tabela Pets

```sql
CREATE TABLE pets (
    id BIGSERIAL PRIMARY KEY,
    owner_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    species ENUM('dog', 'cat', 'bird', 'rabbit', 'other') NOT NULL,
    breed VARCHAR(255),
    age_years INTEGER,
    age_months INTEGER,
    weight_kg DECIMAL(5,2),
    size ENUM('small', 'medium', 'large', 'extra_large'),
    personality_traits JSONB DEFAULT '[]',
    medical_conditions JSONB DEFAULT '[]',
    special_instructions TEXT,
    vaccination_status JSONB DEFAULT '{}',
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_pets_owner ON pets(owner_id);
CREATE INDEX idx_pets_species ON pets(species);
CREATE INDEX idx_pets_size ON pets(size);
```

### 4. Tabela Bookings

```sql
CREATE TABLE bookings (
    id BIGSERIAL PRIMARY KEY,
    owner_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    sitter_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    service_type ENUM('pet_sitting', 'dog_walking', 'pet_visits', 'overnight_care') NOT NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'disputed') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    emergency_contact TEXT,
    cancelled_at TIMESTAMP,
    cancellation_reason TEXT,
    stripe_payment_intent_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_bookings_owner ON bookings(owner_id);
CREATE INDEX idx_bookings_sitter ON bookings(sitter_id);
CREATE INDEX idx_bookings_dates ON bookings(start_date, end_date);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_service_type ON bookings(service_type);
```

### 5. Tabela Booking_Pet (Many-to-Many)

```sql
CREATE TABLE booking_pet (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT REFERENCES bookings(id) ON DELETE CASCADE,
    pet_id BIGINT REFERENCES pets(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(booking_id, pet_id)
);
```

### 6. Pozostałe Tabele

```sql
-- Messages
CREATE TABLE booking_messages (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT REFERENCES bookings(id) ON DELETE CASCADE,
    sender_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    recipient_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    content TEXT NOT NULL,
    message_type ENUM('text', 'image', 'location') DEFAULT 'text',
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews
CREATE TABLE reviews (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT REFERENCES bookings(id) ON DELETE CASCADE,
    reviewer_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    reviewee_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    rating INTEGER CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(booking_id, reviewer_id)
);

-- Walk Sessions
CREATE TABLE walk_sessions (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT REFERENCES bookings(id) ON DELETE CASCADE,
    sitter_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP,
    route_data JSONB,
    total_distance DECIMAL(8,3),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sitter Availability
CREATE TABLE sitter_availabilities (
    id BIGSERIAL PRIMARY KEY,
    sitter_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    is_available BOOLEAN DEFAULT true,
    morning_available BOOLEAN DEFAULT true,
    afternoon_available BOOLEAN DEFAULT true,
    evening_available BOOLEAN DEFAULT true,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(sitter_id, date)
);
```

---

## 🎨 Frontend Structure (Vue 3 + Inertia)

### Main App Configuration

```javascript
// resources/js/app.js
import './bootstrap'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import { createPinia } from 'pinia'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'PetHelp'
const pinia = createPinia()

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(pinia)
            .mount(el)
    },
    progress: {
        color: '#4F46E5',
    },
})
```

### TypeScript Configuration

```json
// tsconfig.json
{
  "compilerOptions": {
    "target": "ES2020",
    "useDefineForClassFields": true,
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "skipLibCheck": true,
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "preserve",
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true,
    "baseUrl": ".",
    "paths": {
      "@/*": ["resources/js/*"],
      "@/Components/*": ["resources/js/Components/*"],
      "@/Pages/*": ["resources/js/Pages/*"],
      "@/Layouts/*": ["resources/js/Layouts/*"],
      "@/Stores/*": ["resources/js/Stores/*"]
    }
  },
  "include": ["resources/js/**/*"],
  "references": [{ "path": "./tsconfig.node.json" }]
}
```

---

## 🚀 Konfiguracja Środowiska

### Composer Dependencies

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/breeze": "^2.0",
        "laravel/cashier": "^15.0",
        "laravel/horizon": "^5.0",
        "laravel/scout": "^10.0",
        "laravel/telescope": "^5.0",
        "spatie/laravel-media-library": "^11.0",
        "spatie/laravel-permission": "^6.0",
        "meilisearch/meilisearch-php": "^1.0",
        "pusher/pusher-php-server": "^7.0",
        "intervention/image": "^3.0"
    },
    "require-dev": {
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.0",
        "mockery/mockery": "^1.0",
        "nunomaduro/collision": "^8.0",
        "phpunit/phpunit": "^11.0"
    }
}
```

### Package.json

```json
{
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite dev"
    },
    "devDependencies": {
        "@inertiajs/vue3": "^1.0.0",
        "@tailwindcss/forms": "^0.5.0",
        "@tailwindcss/typography": "^0.5.0",
        "@types/node": "^20.0.0",
        "@vitejs/plugin-vue": "^5.0.0",
        "autoprefixer": "^10.0.0",
        "axios": "^1.0.0",
        "laravel-vite-plugin": "^1.0.0",
        "postcss": "^8.0.0",
        "tailwindcss": "^3.0.0",
        "typescript": "^5.0.0",
        "vite": "^5.0.0",
        "vue": "^3.0.0"
    },
    "dependencies": {
        "@headlessui/vue": "^1.7.0",
        "@heroicons/vue": "^2.0.0",
        "@inertiajs/vue3": "^1.0.0",
        "pinia": "^2.0.0",
        "vue3-google-map": "^0.18.0",
        "@vueuse/core": "^10.0.0"
    }
}
```

### Environment Variables

```bash
# .env
APP_NAME="PetHelp"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pethelp
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@pethelp.pl"
MAIL_FROM_NAME="${APP_NAME}"

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=pln

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=

# Pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=eu

# Google Maps
GOOGLE_MAPS_API_KEY=

# File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 📋 Plan Implementacji (Krok po Kroku)

### ✅ Week 1-2: Setup & Authentication - COMPLETED
```bash
# ✅ Day 1-2: Project Setup
composer create-project laravel/laravel pethelp
cd pethelp
composer require laravel/breeze
php artisan breeze:install vue --typescript
npm install

# ✅ Day 3-4: Database Setup
- ✅ Create migrations (Users, SitterProfiles, Pets, Bookings)
- ✅ Setup MySQL (changed from PostgreSQL due to driver availability)
- ✅ Seed initial data

# ✅ Day 5-7: Basic Auth & User Management
- ✅ Extend registration for owner/sitter roles
- ✅ Add phone and address fields
- ✅ Basic profile setup with role-based functionality
```

### 📊 CURRENT STATUS - MVP Foundation Complete
```
✅ Laravel 11 + Inertia.js + Vue 3 + TypeScript setup
✅ MySQL database with complete schema
✅ User authentication with role system (owner/sitter/both)
✅ Core models: User, SitterProfile, Pet, Booking
✅ Database migrations with proper relationships
✅ Sample data seeders for development

READY FOR NEXT PHASE: Frontend Components & API Development
```

### 🎯 IMMEDIATE NEXT STEPS
```bash
# Week 3: Core Frontend Components
- [ ] Dashboard pages for owners and sitters
- [ ] Pet management forms and listings
- [ ] Sitter profile creation and editing
- [ ] Basic search and listing pages

# Week 4: API & Search Implementation
- [ ] API controllers for bookings
- [ ] Search functionality for sitters
- [ ] Booking request system
- [ ] Basic messaging between users
```

### Week 3-4: Core Models & Search
```bash
# Day 8-10: Models & Relationships
- Create all models
- Setup media library
- Add model factories & seeders

# Day 11-14: Search Implementation
- Setup Meilisearch
- Implement search service
- Add basic frontend search
```

### Week 5-6: Booking System
```bash
# Day 15-18: Booking Logic
- Create booking service
- Implement availability system
- Add booking forms

# Day 19-21: Payment Integration
- Setup Stripe
- Add payment flows
- Implement escrow system
```

### Week 7-8: Communication & Polish
```bash
# Day 22-25: Messaging System
- Real-time chat
- Push notifications
- Email notifications

# Day 26-28: Reviews & Polish
- Review system
- Admin panel
- Testing & debugging
```

### Week 9-12: Advanced Features & Launch
```bash
# Day 29-35: Advanced Features
- Walk tracking
- Photo updates
- Advanced search filters

# Day 36-42: Testing & Launch Prep
- Comprehensive testing
- Performance optimization
- Production deployment
```

---

## 🔧 Development Commands

### Initial Setup

```bash
# Clone and setup
git clone <repo>
cd pethelp
composer install
npm install
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Storage
php artisan storage:link

# Queue & Broadcasting
php artisan horizon
php artisan websockets:serve

# Development
npm run dev
php artisan serve
```

### Daily Development

```bash
# Backend
php artisan migrate:fresh --seed  # Reset DB
php artisan tinker                # REPL
php artisan queue:work             # Process jobs
php artisan scout:flush           # Clear search index

# Frontend  
npm run dev                       # Hot reload
npm run build                     # Production build
npm run type-check               # TypeScript check

# Testing
php artisan test                  # Run tests
php artisan test --coverage      # With coverage
```

---

## 📊 Monitoring & Analytics

### Key Metrics to Track

```php
// app/Analytics/PetHelpMetrics.php
class PetHelpMetrics 
{
    public function trackUserRegistration($user, $role) { }
    public function trackSearchPerformed($params) { }
    public function trackBookingCreated($booking) { }
    public function trackPaymentCompleted($payment) { }
    public function trackSitterVerified($sitter) { }
}
```

### Performance Monitoring

```php
// config/telescope.php - Production monitoring
'watchers' => [
    'requests' => true,
    'queries' => true,
    'cache' => true,
    'jobs' => true,
    'mail' => true,
]
```

---

## 🚀 Deployment Strategy

### Production Stack
- **Server:** DigitalOcean Droplet (4GB RAM, 2 vCPUs)
- **Database:** Managed PostgreSQL
- **Cache:** Managed Redis
- **Storage:** DigitalOcean Spaces (S3-compatible)
- **CDN:** CloudFlare
- **Monitoring:** Laravel Forge + Envoyer

### CI/CD Pipeline
```yaml
# .github/workflows/deploy.yml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
      - name: Build assets
        run: npm ci && npm run build
      - name: Deploy to production
        run: # Deployment commands
```

---

**Ta rozpiska daje Ci kompletny roadmap do implementacji. Każdy plik ma swoje miejsce i cel. Możesz zacząć od setup'u projektu i iść krok po kroku według harmonogramu.**

**Chcesz żebym przygotował konkretne pliki do implementacji (np. konkretne migracje, modele, czy komponenty Vue)?** 🚀