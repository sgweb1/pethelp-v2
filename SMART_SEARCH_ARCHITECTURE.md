# 🔍 Inteligentna Wyszukiwarka PetHelp - Architektura & Plan

## 📋 Spis treści
1. [Wizja & Cel](#wizja--cel)
2. [Kluczowe Funkcjonalności](#kluczowe-funkcjonalności)
3. [Architektura Systemu](#architektura-systemu)
4. [Interfejs Użytkownika](#interfejs-użytkownika)
5. [Technologie](#technologie)
6. [Plan Implementacji](#plan-implementacji)

---

## 🎯 Wizja & Cel

### Główny Cel
Stworzyć **najinteligentniejszą wyszukiwarkę opiekunów zwierząt** w Polsce, która:
- ✨ Przewiduje intencje użytkownika
- 🧠 Uczy się z zachowań użytkowników
- 🎯 Proponuje najlepsze dopasowania
- 🗺️ Łączy dane geograficzne z preferencjami
- ⚡ Działa błyskawicznie i intuicyjnie

### Unikalne Cechy
1. **Kontekstowe sugestie** - AI przewiduje, czego szukasz
2. **Hybrydowy widok** - mapa + lista synchronizowane w czasie rzeczywistym
3. **Inteligentne filtry** - adaptują się do lokalizacji i dostępności
4. **Multi-search** - jednoczesne wyszukiwanie opiekunów, eventów i usług
5. **Pamięć preferencji** - zapamiętuje Twoje wybory

---

## 🚀 Kluczowe Funkcjonalności

### 1. Inteligentny Pasek Wyszukiwania

#### A. Auto-sugestie z AI
```
Użytkownik wpisuje: "spacer pies war"
↓
System sugeruje:
🔍 "spacer z psem Warszawa Mokotów"
🐕 Wyświetl opiekunów psów w Warszawie
📍 Pokaż spacery w promieniu 5km
📅 Wydarzenia dla psów w tym tygodniu
```

#### B. Natural Language Processing (NLP)
- Rozumie zapytania w języku naturalnym
- "Potrzebuję kogoś do wyprowadzania labradora w weekend"
- Automatycznie wyciąga: typ zwierzęcia, usługa, czas

#### C. Multi-kryterium
```javascript
// Przykład zapytania
{
  text: "opieka nad kotem Kraków",
  petType: "kot",
  location: {
    city: "Kraków",
    radius: 10
  },
  serviceType: "opieka noclegowa",
  implicit: {
    timing: "najbliższy weekend", // wykryte z kontekstu
    verified: true // preferencja użytkownika
  }
}
```

### 2. Zaawansowane Filtry

#### A. Podstawowe Filtry
- **Typ zwierzęcia**: Pies, Kot, Gryzoń, Ptak, Inne
- **Rodzaj usługi**: Spacer, Opieka noclegowa, Wizyta domowa, Szkolenie
- **Zasięg geograficzny**: 1km, 5km, 10km, 25km, Całe miasto
- **Dostępność**: Dzisiaj, Jutro, Ten weekend, Przyszły tydzień
- **Cena**: Zakres cenowy (slider)
- **Ocena**: Min. 4⭐, Min. 4.5⭐, Tylko 5⭐

#### B. Zaawansowane Filtry (rozwijalne)
- **Doświadczenie**: Lata doświadczenia, certyfikaty, referencje
- **Specjalizacja**: Rasy specyficzne, psy służbowe, zwierzęta egzotyczne
- **Dodatkowe usługi**: Podawanie leków, transport, grooming
- **Języki**: Polski, Angielski, Niemiecki, inne
- **Ubezpieczenie**: Posiada ubezpieczenie OC
- **Zweryfikowany**: Weryfikacja tożsamości, sprawdzone referencje
- **Wyposażenie**: Własny transport, ogródek, doświadczenie z agresywnymi

#### C. Inteligentne Filtry Kontekstowe
```javascript
// Adaptacja do kontekstu
if (user.location === "Warszawa" && time === "weekend") {
  filters.suggest([
    "Dostępni w weekend (+50 opiekunów)",
    "W Twojej dzielnicy (Mokotów)",
    "Akceptują duże psy (+30 opiekunów)"
  ]);
}
```

### 3. Hybrydowy Widok Mapa + Lista

#### A. Synchronizacja w Czasie Rzeczywistym
```
Akcja na mapie → Natychmiastowa aktualizacja listy
Akcja na liście → Natychmiastowa aktualizacja mapy

Przykłady:
- Przesunięcie mapy → Lista pokazuje wyniki z widocznego obszaru
- Filtr "Min. 4.5⭐" → Mapa pokazuje tylko najlepiej ocenionych
- Kliknięcie pinezki → Lista scrolluje do tego opiekuna
- Hover na karcie → Highlight pinezki na mapie
```

#### B. Widoki
1. **Mapa główna** (lewa/górna strona - 60% ekranu)
2. **Lista wyników** (prawa/dolna strona - 40% ekranu)
3. **Tryb pełnoekranowy** - dowolny widok na pełnym ekranie
4. **Tryb mobilny** - przełączanie zakładkami

#### C. Interaktywna Mapa

**Warstwy (ToggleLayers):**
- 🐾 **Opiekunowie** (pinezki z avatar)
- 🎉 **Wydarzenia** (ikony eventów)
- 🏥 **Usługi lokalne** (weterynarze, sklepy zoologiczne)
- 🏞️ **Parki dla psów** (strefy wyprowadzania)

**Funkcje mapy:**
```javascript
// Clustering
- Grupowanie bliskich wyników
- Kliknięcie clustera → zoom do grupy
- Pokazywanie liczby wyników w clusterze

// Heatmapa dostępności
- Kolor: zielony = dużo dostępnych, czerwony = mało
- Podpowiedzi: "Przesuń się 2km na północ - więcej dostępnych"

// Rysowanie obszaru
- Narysuj polygon/okrąg na mapie
- Wyszukaj tylko w tym obszarze
```

**Pinezki na mapie:**
```html
<!-- Przykładowa pinezka opiekuna -->
<div class="map-pin verified premium">
  <img src="avatar.jpg" class="w-10 h-10 rounded-full">
  <div class="rating-badge">4.9⭐</div>
  <div class="price-badge">35zł/h</div>
  <div class="availability">Dziś dostępny</div>
</div>
```

### 4. Inteligentna Lista Wyników

#### A. Sortowanie
- **Najlepsze dopasowanie** (AI scoring - domyślne)
- Najwyższa ocena
- Najniższa cena
- Najbliższa odległość
- Najszybsza dostępność
- Najczęściej rezerwowani

#### B. Karta Opiekuna w Liście
```html
<div class="sitter-card hover:shadow-lg transition">
  <!-- Główne info -->
  <div class="header">
    <img class="avatar" />
    <div class="name-rating">
      <h3>Anna Kowalska</h3>
      <div class="rating">4.9⭐ (142 opinie)</div>
    </div>
    <div class="badges">
      <badge>Zweryfikowany</badge>
      <badge>SuperSitter</badge>
    </div>
  </div>

  <!-- Quick stats -->
  <div class="stats">
    <stat>📍 1.2 km</stat>
    <stat>💰 35-50 zł/h</stat>
    <stat>🎯 98% akceptacji</stat>
  </div>

  <!-- Specjalizacje -->
  <div class="specializations">
    <chip>Duże psy</chip>
    <chip>Szkolenia</chip>
    <chip>Podawanie leków</chip>
  </div>

  <!-- Dostępność -->
  <div class="availability">
    Dostępna: <strong>Dziś 14:00-18:00</strong>
  </div>

  <!-- Akcje -->
  <div class="actions">
    <button primary>Zarezerwuj teraz</button>
    <button secondary>Zobacz profil</button>
    <button icon>💬</button>
    <button icon>❤️</button>
  </div>
</div>
```

#### C. Infinite Scroll + Paginacja
- Ładowanie kolejnych 20 wyników podczas scrollowania
- "Pokazano 20 z 156 wyników"
- Opcja przejścia do konkretnej strony

### 5. Multi-Search (Opiekunowie + Wydarzenia + Usługi)

#### A. Zakładki Wyników
```
[ 🐾 Opiekunowie (142) ] [ 🎉 Wydarzenia (8) ] [ 🏥 Usługi (12) ]
```

#### B. Wydarzenia na Mapie
```javascript
// Przykładowa pinezka eventu
{
  type: "event",
  title: "Spotkanie psów rasy Labrador",
  date: "Sobota 15:00",
  location: "Park Łazienkowski",
  attendees: 24,
  icon: "🎉"
}
```

#### C. Usługi Lokalne
```javascript
// Weterynarze, sklepy zoologiczne, groomerzy
{
  type: "service",
  category: "weterynarz",
  name: "Przychodnia Weterynaryjn 'Azyl'",
  rating: 4.7,
  distance: "800m",
  open: true // czy otwarte teraz
}
```

---

## 🏗️ Architektura Systemu

### 1. Backend Architecture

```
┌─────────────────────────────────────────┐
│          API Endpoints                  │
├─────────────────────────────────────────┤
│                                         │
│  GET  /api/search/unified               │
│  POST /api/search/suggestions           │
│  GET  /api/search/filters               │
│  GET  /api/search/availability          │
│  POST /api/search/save-preferences      │
│                                         │
└─────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────┐
│     Search Service Layer                │
├─────────────────────────────────────────┤
│                                         │
│  • SmartSearchEngine                    │
│  • FilterProcessor                      │
│  • SuggestionEngine (AI)                │
│  • GeolocationService                   │
│  • AvailabilityCalculator               │
│  • ScoringAlgorithm (ML)                │
│                                         │
└─────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────┐
│         Data Sources                    │
├─────────────────────────────────────────┤
│                                         │
│  • Users (Sitters)                      │
│  • Services                             │
│  • Availabilities                       │
│  • Reviews & Ratings                    │
│  • Locations & Coordinates              │
│  • Events                               │
│  • Local Services                       │
│                                         │
└─────────────────────────────────────────┘
```

### 2. Frontend Architecture

```
┌─────────────────────────────────────────┐
│      Search UI Component                │
│  (Livewire/Alpine.js/Vue.js)            │
├─────────────────────────────────────────┤
│                                         │
│  ┌──────────────────────────────────┐   │
│  │   SearchBar (auto-suggest)       │   │
│  └──────────────────────────────────┘   │
│  ┌──────────────────────────────────┐   │
│  │   FilterPanel (collapsible)      │   │
│  └──────────────────────────────────┘   │
│  ┌──────────────┬───────────────────┐   │
│  │              │                   │   │
│  │  MapView     │   ResultsList     │   │
│  │  (60%)       │   (40%)           │   │
│  │              │                   │   │
│  │  • Leaflet   │   • Infinite      │   │
│  │  • Clusters  │     Scroll        │   │
│  │  • Layers    │   • Cards         │   │
│  │              │   • Filters       │   │
│  │              │                   │   │
│  └──────────────┴───────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 3. Database Schema Extensions

```sql
-- Tabela z zapisanymi preferencjami użytkowników
CREATE TABLE user_search_preferences (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    pet_types JSON,              -- ['dog', 'cat']
    preferred_radius INT,         -- w km
    price_range JSON,             -- {min: 20, max: 80}
    min_rating DECIMAL(2,1),
    preferred_services JSON,
    saved_locations JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabela z historiami wyszukiwań (dla ML)
CREATE TABLE search_analytics (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NULLABLE,
    session_id VARCHAR(255),
    query_text TEXT,
    filters_applied JSON,
    results_count INT,
    clicked_results JSON,        -- które wyniki user kliknął
    booked_result_id BIGINT NULLABLE,
    location_lat DECIMAL(10, 8),
    location_lng DECIMAL(11, 8),
    created_at TIMESTAMP
);

-- Wydarzenia
CREATE TABLE events (
    id BIGINT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    event_type VARCHAR(50),      -- meetup, training, adoption
    pet_types JSON,
    location_name VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    event_date DATETIME,
    max_attendees INT,
    current_attendees INT,
    organizer_id BIGINT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Usługi lokalne (weterynarze, sklepy, itp.)
CREATE TABLE local_services (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    service_type VARCHAR(50),    -- veterinary, petshop, grooming
    description TEXT,
    address VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    website VARCHAR(255),
    opening_hours JSON,
    rating DECIMAL(2,1),
    reviews_count INT,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🎨 Interfejs Użytkownika

### Layout Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  NAVBAR (PetHelp Logo | Szukaj | Zostań opiekunem | Profil)    │
└─────────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────────┐
│  SEARCH BAR                                                     │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │ 🔍 "Znajdź opiekuna dla..."  [Filters ▼] [Save Search]   │ │
│  └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│  QUICK FILTERS (Pills)                                          │
│  [ 🐕 Psy ]  [ 🐈 Koty ]  [ 📍 W pobliżu ]  [ ⭐ Najlepsi ]    │
│  [ 💰 Do 50zł/h ]  [ 📅 Dziś dostępni ]  [ ✓ Zweryfikowani ]  │
└─────────────────────────────────────────────────────────────────┘
┌──────────────────────────────┬──────────────────────────────────┐
│                              │                                  │
│         MAPA (60%)           │      LISTA WYNIKÓW (40%)         │
│  ┌────────────────────────┐  │  ┌────────────────────────────┐ │
│  │                        │  │  │ [Sort: Najlepsze ▼]        │ │
│  │    🗺️ Interactive     │  │  │ 142 wyników                │ │
│  │       Leaflet Map      │  │  │                            │ │
│  │                        │  │  │ [Sitter Card 1]            │ │
│  │  • Pinezki opiekunów   │  │  │ [Sitter Card 2]            │ │
│  │  • Clustery            │  │  │ [Sitter Card 3]            │ │
│  │  • Wydarzenia          │  │  │ ...                        │ │
│  │  • Usługi lokalne      │  │  │ [Infinite Scroll]          │ │
│  │                        │  │  │                            │ │
│  │  [Toggle Layers ▼]     │  │  │                            │ │
│  │  ☑ Opiekunowie         │  │  │                            │ │
│  │  ☐ Wydarzenia          │  │  │                            │ │
│  │  ☐ Usługi              │  │  └────────────────────────────┘ │
│  │                        │  │                                  │
│  │  [📍 Moja lokalizacja] │  │  [ ⬅️ Prev ] [ 1 2 3 ] [Next ➡️]│
│  │  [🔲 Rysuj obszar]     │  │                                  │
│  └────────────────────────┘  │                                  │
│                              │                                  │
└──────────────────────────────┴──────────────────────────────────┘
```

### Responsive Mobile View

```
┌─────────────────────────┐
│  NAVBAR (mobile)        │
└─────────────────────────┘
┌─────────────────────────┐
│  SEARCH BAR             │
│  🔍 [Filters]           │
└─────────────────────────┘
┌─────────────────────────┐
│  TABS                   │
│  [ 🗺️ Mapa ] [ 📋 Lista ]│
└─────────────────────────┘
┌─────────────────────────┐
│                         │
│   Active Tab Content    │
│   (Fullscreen)          │
│                         │
│   - Swipe to switch     │
│   - Bottom sheet        │
│     filters             │
│                         │
└─────────────────────────┘
```

---

## 💻 Technologie

### Backend
- **Laravel 12** - Framework główny
- **Eloquent ORM** - Zapytania do bazy
- **Laravel Scout** - Full-text search (opcjonalnie Algolia/Meilisearch)
- **Geocoder** - Geolokalizacja i adresy
- **Redis** - Cache dla wyników wyszukiwania
- **Queue Jobs** - Asynchroniczne przetwarzanie

### Frontend
- **Livewire 3** - Interaktywność real-time
- **Alpine.js 3** - Lekkia reaktywność UI
- **Leaflet.js** - Mapy interaktywne
- **Leaflet.markercluster** - Grupowanie pinezek
- **Tailwind CSS 4** - Styling
- **Vue.js 3** (opcjonalnie) - Dla bardziej złożonych komponentów

### AI/ML
- **Laravel Prompts + OpenAI** - NLP dla zapytań
- **Recommendation Engine** - Scoring algorytm
- **Laravel Pennant** - Feature flags dla testów A/B

### Mapa
- **Leaflet.js** - Open-source maps
- **OpenStreetMap** - Dane map
- **Mapbox** (opcjonalnie) - Dla lepszych stylów
- **GeoJSON** - Format danych geograficznych

---

## 📅 Plan Implementacji

### Faza 1: Fundament (Tydzień 1-2)
- ✅ Migracje bazy danych (search_analytics, events, local_services)
- ✅ Podstawowe modele Eloquent
- ✅ API endpoints struktura
- ✅ Routing + middleware

### Faza 2: Backend Search Engine (Tydzień 2-3)
- ✅ SmartSearchEngine - główna klasa
- ✅ FilterProcessor - przetwarzanie filtrów
- ✅ GeolocationService - operacje geo
- ✅ AvailabilityCalculator - dostępność
- ✅ Scoring Algorithm v1

### Faza 3: Frontend Podstawy (Tydzień 3-4)
- ✅ Layout struktury (mapa + lista)
- ✅ SearchBar component (Livewire)
- ✅ FilterPanel component
- ✅ Basic Leaflet integration
- ✅ ResultsList component

### Faza 4: Integracja Mapy (Tydzień 4-5)
- ✅ Pinezki na mapie (opiekunowie)
- ✅ Clustering
- ✅ Sync mapa ↔️ lista
- ✅ Toggle layers (wydarzenia, usługi)
- ✅ Rysowanie obszaru

### Faza 5: Funkcje AI (Tydzień 5-6)
- ✅ Auto-sugestie (AI)
- ✅ NLP query parsing
- ✅ Preferencje użytkownika
- ✅ Search analytics tracking

### Faza 6: Wydarzenia & Usługi (Tydzień 6-7)
- ✅ Dodanie eventów do mapy
- ✅ Local services integration
- ✅ Multi-search tabs
- ✅ Filtrowanie po typach

### Faza 7: Optymalizacja & Testy (Tydzień 7-8)
- ✅ Performance optimization
- ✅ Cache strategy
- ✅ Mobile responsiveness
- ✅ Browser testing
- ✅ Load testing

### Faza 8: Launch (Tydzień 8)
- ✅ Final QA
- ✅ Documentation
- ✅ Deploy to production
- ✅ Monitoring setup

---

## 🎯 Metryki Sukcesu

### KPIs
- **Czas do pierwszego wyniku**: < 500ms
- **Search-to-booking conversion**: > 15%
- **User satisfaction**: > 4.5/5
- **Mobile usage**: > 60%
- **Return search rate**: > 40%

### Analityka
- Click-through rate na wynikach
- Najpopularniejsze filtry
- Średni czas sesji wyszukiwania
- Geograficzne hot-spoty
- Najpopularniejsze zapytania

---

## 🚀 Innowacje & Przewagi Konkurencyjne

1. **AI-Powered Matching** - Nie tylko szukasz, system Cię dopasowuje
2. **Real-time Availability** - Zawsze aktualna dostępność
3. **Context-Aware** - Wie, czego potrzebujesz zanim to wpiszesz
4. **Unified Search** - Opiekunowie + Wydarzenia + Usługi w jednym miejscu
5. **Visual Discovery** - Odkrywaj opiekunów przeglądając mapę
6. **Smart Notifications** - "Nowy opiekun w Twojej okolicy!"
7. **Price Intelligence** - Pokazuje średnie ceny w Twojej okolicy
8. **Verification Badges** - Zaufanie już na pierwszy rzut oka

---

## 🔐 Bezpieczeństwo & Prywatność

- Rate limiting dla API (100 req/min)
- Anonimizacja danych analitycznych
- GDPR compliance
- Opcjonalne zapisywanie historii (user opt-in)
- Encryption dla wrażliwych danych

---

## 📱 Progressive Web App (PWA)

- Offline mode - cache ostatnich wyników
- Geolocation API - automatyczna lokalizacja
- Push notifications - nowi opiekunowie
- Add to Home Screen
- Fast loading (<3s initial load)

---

**Autor**: Claude AI Assistant
**Data**: 2025-10-03
**Wersja**: 1.0
