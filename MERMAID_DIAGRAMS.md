# 🗺️ DIAGRAMY SYSTEMU PETHELP (Mermaid)

## 1. ENTITY RELATIONSHIP DIAGRAM

```mermaid
erDiagram
    users ||--o{ user_profiles : has_one
    users ||--o{ pets : owns
    users ||--o{ services : offers
    users ||--o{ bookings_owner : creates_as_owner
    users ||--o{ bookings_sitter : handles_as_sitter
    users ||--o{ events : organizes
    users ||--o{ advertisements : posts
    users ||--o{ subscriptions : has_subscription

    user_profiles ||--|| users : belongs_to

    pets ||--|| users : belongs_to
    pets ||--|| pet_types : has_type
    pets ||--o{ bookings : included_in

    services ||--|| users : belongs_to_sitter
    services ||--|| service_categories : belongs_to_category
    services ||--o{ bookings : receives
    services ||--o{ map_items : mapped_to

    bookings ||--|| users : belongs_to_owner
    bookings ||--|| users : belongs_to_sitter
    bookings ||--|| services : for_service
    bookings ||--|| pets : includes_pet
    bookings ||--o{ payments : has_payments
    bookings ||--o{ reviews : generates_reviews

    events ||--|| users : organized_by
    events ||--|| event_types : of_type
    events ||--o{ event_registrations : has_registrations
    events ||--o{ map_items : mapped_to

    advertisements ||--|| users : posted_by
    advertisements ||--|| advertisement_categories : in_category
    advertisements ||--o{ advertisement_images : has_images
    advertisements ||--o{ map_items : mapped_to

    map_items ||--|| users : belongs_to
    map_items }o--|| services : maps_service
    map_items }o--|| events : maps_event
    map_items }o--|| advertisements : maps_advertisement

    subscriptions ||--|| users : belongs_to
    subscriptions ||--|| subscription_plans : follows_plan

    reviews ||--|| users : written_by
    reviews ||--|| users : about_user
    reviews ||--|| bookings : for_booking
```

## 2. USER JOURNEY FLOWCHART

```mermaid
flowchart TD
    A[Landing Page] --> B[Search Services]
    B --> C{User Type?}

    C -->|Pet Owner| D[Filter by Location/Pet Type]
    C -->|Pet Sitter| E[Register as Sitter]

    D --> F[View Map Results]
    F --> G[Select Sitter Profile]
    G --> H[Choose Service]
    H --> I[Book Service]
    I --> J[Make Payment]
    J --> K[Track Booking]
    K --> L[Leave Review]

    E --> M[Complete Profile]
    M --> N[Identity Verification]
    N --> O[Create Services]
    O --> P[Set Availability]
    P --> Q[Receive Bookings]
    Q --> R[Confirm/Decline]
    R --> S[Provide Service]
    S --> T[Complete & Review]

    B --> U[Browse Events]
    U --> V[Register for Event]
    V --> W[Attend Event]

    B --> X[Marketplace]
    X --> Y[Post Advertisement]
    Y --> Z[Manage Listings]
```

## 3. SYSTEM ARCHITECTURE DIAGRAM

```mermaid
graph TB
    subgraph "Frontend Layer"
        A[Livewire Components]
        B[Alpine.js Interactions]
        C[Tailwind CSS Styling]
    end

    subgraph "Application Layer"
        D[Route Handlers]
        E[Livewire Controllers]
        F[API Controllers]
        G[Form Requests]
    end

    subgraph "Business Logic Layer"
        H[Service Classes]
        I[Repository Pattern]
        J[Event Listeners]
        K[Job Queues]
    end

    subgraph "Data Layer"
        L[Eloquent Models]
        M[Database Migrations]
        N[Model Relationships]
    end

    subgraph "External Services"
        O[PayU Payment Gateway]
        P[Nominatim Geocoding]
        Q[Email Service]
        R[File Storage]
    end

    subgraph "Infrastructure"
        S[(MySQL Database)]
        T[(Redis Cache)]
        U[Laravel Queues]
        V[File System]
    end

    A --> D
    B --> E
    C --> F
    D --> H
    E --> I
    F --> J
    G --> K
    H --> L
    I --> M
    J --> N
    K --> S
    L --> T
    M --> U
    N --> V
    H --> O
    I --> P
    J --> Q
    K --> R
```

## 4. BOOKING PROCESS FLOW

```mermaid
stateDiagram-v2
    [*] --> SearchServices
    SearchServices --> ViewSitterProfile
    ViewSitterProfile --> SelectService
    SelectService --> FillBookingForm
    FillBookingForm --> ReviewBooking
    ReviewBooking --> SubmitBooking

    SubmitBooking --> PendingApproval
    PendingApproval --> Confirmed : Sitter Accepts
    PendingApproval --> Cancelled : Sitter Declines
    PendingApproval --> Cancelled : Owner Cancels

    Confirmed --> PaymentRequired
    PaymentRequired --> PaymentProcessing
    PaymentProcessing --> PaymentCompleted : Success
    PaymentProcessing --> PaymentFailed : Failed
    PaymentFailed --> PaymentRequired

    PaymentCompleted --> InProgress
    InProgress --> Completed : Service Ends
    InProgress --> Cancelled : Emergency Cancel

    Completed --> ReviewPhase
    ReviewPhase --> Reviewed
    Reviewed --> [*]

    Cancelled --> [*]
```

## 5. API ARCHITECTURE DIAGRAM

```mermaid
graph LR
    subgraph "Client Applications"
        A[Web Browser]
        B[Mobile App]
        C[Third Party Apps]
    end

    subgraph "API Gateway"
        D[Rate Limiting]
        E[Authentication]
        F[Request Validation]
    end

    subgraph "API Endpoints"
        G[/api/search]
        H[/api/locations]
        I[/api/bookings]
        J[/api/payments]
        K[/api/js-logs]
    end

    subgraph "Service Layer"
        L[SearchService]
        M[LocationService]
        N[BookingService]
        O[PaymentService]
        P[LoggingService]
    end

    subgraph "Data Sources"
        Q[(Database)]
        R[(Cache)]
        S[External APIs]
    end

    A --> D
    B --> E
    C --> F
    D --> G
    E --> H
    F --> I
    G --> L
    H --> M
    I --> N
    J --> O
    K --> P
    L --> Q
    M --> R
    N --> S
```

## 6. SEARCH SYSTEM FLOW

```mermaid
graph TD
    A[Search Request] --> B{Content Type?}

    B -->|pet_sitter| C[ServiceSearchService]
    B -->|event| D[EventSearchService]
    B -->|adoption| E[AdvertisementSearchService]
    B -->|other| F[MapItemSearchService]

    C --> G[Check Cache]
    G -->|Hit| H[Return Cached Results]
    G -->|Miss| I[Query Database]

    I --> J[Apply Filters]
    J --> K[Apply Sorting]
    K --> L[Apply Pagination]
    L --> M[Format Results]
    M --> N[Cache Results]
    N --> O[Return Response]

    subgraph "Filters Applied"
        P[Location Filter]
        Q[Pet Type Filter]
        R[Price Range Filter]
        S[Category Filter]
        T[Rating Filter]
        U[Availability Filter]
    end

    J --> P
    J --> Q
    J --> R
    J --> S
    J --> T
    J --> U
```

## 7. PAYMENT PROCESS FLOW

```mermaid
sequenceDiagram
    participant U as User
    participant P as PetHelp
    participant PayU as PayU Gateway
    participant B as Bank

    U->>P: Select service & book
    P->>P: Create booking record
    P->>PayU: Create payment order
    PayU->>P: Return payment URL
    P->>U: Redirect to payment

    U->>PayU: Enter payment details
    PayU->>B: Process payment
    B->>PayU: Payment result

    alt Payment Success
        PayU->>P: Success notification (webhook)
        P->>P: Update booking status
        P->>U: Redirect to success page
        P->>U: Send confirmation email
    else Payment Failed
        PayU->>P: Failure notification
        P->>P: Keep booking pending
        P->>U: Redirect to failure page
        P->>U: Show retry option
    end
```

## 8. REAL-TIME NOTIFICATIONS FLOW

```mermaid
graph TD
    A[Event Occurs] --> B{Event Type?}

    B -->|Booking Created| C[New Booking Notification]
    B -->|Booking Confirmed| D[Confirmation Notification]
    B -->|Payment Success| E[Payment Notification]
    B -->|Review Posted| F[Review Notification]
    B -->|Message Received| G[Chat Notification]

    C --> H[Determine Recipients]
    D --> H
    E --> H
    F --> H
    G --> H

    H --> I[Queue Notification Jobs]
    I --> J[Process Notifications]

    J --> K[In-App Notification]
    J --> L[Email Notification]
    J --> M[SMS Notification]
    J --> N[Push Notification]

    K --> O[Store in Database]
    L --> P[Send via SMTP]
    M --> Q[Send via SMS Gateway]
    N --> R[Send via FCM/APNS]
```

## 9. CACHING STRATEGY DIAGRAM

```mermaid
graph TB
    A[User Request] --> B{Cache Layer 1}
    B -->|Hit| C[Return from Browser Cache]
    B -->|Miss| D{Cache Layer 2}

    D -->|Hit| E[Return from Application Cache]
    D -->|Miss| F{Cache Layer 3}

    F -->|Hit| G[Return from Redis Cache]
    F -->|Miss| H[Query Database]

    H --> I[Process Data]
    I --> J[Store in Redis]
    J --> K[Store in App Cache]
    K --> L[Return to User]

    subgraph "Cache Types"
        M[Search Results - 5min TTL]
        N[Map Data - 15min TTL]
        O[User Profiles - 30min TTL]
        P[Service Categories - 24h TTL]
        Q[Static Assets - 1 year TTL]
    end

    E --> M
    G --> N
    G --> O
    G --> P
    C --> Q
```

## 10. SECURITY LAYERS DIAGRAM

```mermaid
graph TD
    A[Incoming Request] --> B[HTTPS/TLS Layer]
    B --> C[Rate Limiting]
    C --> D[CSRF Protection]
    D --> E[Input Validation]
    E --> F[Authentication]
    F --> G[Authorization]
    G --> H[Application Logic]

    subgraph "Security Measures"
        I[SQL Injection Prevention]
        J[XSS Protection]
        K[File Upload Validation]
        L[Session Security]
        M[API Token Management]
        N[Audit Logging]
    end

    H --> I
    H --> J
    H --> K
    H --> L
    H --> M
    H --> N

    subgraph "Data Protection"
        O[Encryption at Rest]
        P[Encryption in Transit]
        Q[Personal Data Anonymization]
        R[GDPR Compliance]
    end

    I --> O
    J --> P
    K --> Q
    L --> R
```

---

## Jak Używać Tych Diagramów:

### 1. **GitHub/GitLab Integration**
Diagramy Mermaid są automatycznie renderowane w README.md i innych plikach Markdown na platformach Git.

### 2. **VS Code Extensions**
- Mermaid Preview
- Mermaid Markdown Syntax Highlighting

### 3. **Online Tools**
- https://mermaid.live/ - live editor
- https://mermaidjs.github.io/ - dokumentacja

### 4. **Export Options**
Diagramy można eksportować jako:
- SVG (skalowalne)
- PNG (statyczne)
- PDF (dokumentacja)

### 5. **Documentation Integration**
Idealne do:
- Dokumentacji projektowej
- Prezentacji dla stakeholderów
- Onboarding nowych developerów
- System design reviews