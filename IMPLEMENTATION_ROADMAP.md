# 🚀 Plan Implementacji - Inteligentna Wyszukiwarka PetHelp

## 📋 Przegląd Projektu

**Czas realizacji**: 8 tygodni
**Zespół**: Backend (1), Frontend (1), AI/ML (0.5 - konsultacje)
**Budżet szacunkowy**: 80-120 godzin pracy programistycznej

---

## 🎯 Kamienie Milowe

| Tydzień | Milestone | Deliverables |
|---------|-----------|--------------|
| 1-2 | Fundament & Baza Danych | Migracje, Modele, Seedy |
| 3-4 | Search Engine Backend | API endpoints, Logic, Tests |
| 4-5 | Frontend Podstawy | Layout, Komponenty, Mapa |
| 5-6 | Integracja & AI | Auto-sugestie, Smart filters |
| 6-7 | Wydarzenia & Usługi | Multi-search, Layers |
| 7-8 | Optymalizacja & Launch | Performance, Tests, Deploy |

---

## 📅 Szczegółowy Harmonogram

### 🔷 Faza 1: Fundament (Tydzień 1-2)

#### Tydzień 1: Database & Models

**Day 1-2: Migracje**
```bash
# Komendy do wykonania
php artisan make:migration create_user_search_preferences_table
php artisan make:migration create_search_analytics_table
php artisan make:migration create_events_table
php artisan make:migration create_local_services_table
php artisan make:migration add_geolocation_indexes_to_users
```

**Pliki do utworzenia**:
- `database/migrations/2025_xx_xx_create_user_search_preferences_table.php`
- `database/migrations/2025_xx_xx_create_search_analytics_table.php`
- `database/migrations/2025_xx_xx_create_events_table.php`
- `database/migrations/2025_xx_xx_create_local_services_table.php`

**Day 3-4: Eloquent Models**
```bash
php artisan make:model UserSearchPreference
php artisan make:model SearchAnalytic
php artisan make:model Event
php artisan make:model LocalService
```

**Pliki do utworzenia**:
- `app/Models/UserSearchPreference.php`
- `app/Models/SearchAnalytic.php`
- `app/Models/Event.php`
- `app/Models/LocalService.php`

**Day 5: Factories & Seeders**
```bash
php artisan make:factory EventFactory
php artisan make:factory LocalServiceFactory
php artisan make:seeder EventSeeder
php artisan make:seeder LocalServiceSeeder
```

#### Tydzień 2: Service Classes Struktura

**Day 1-2: Search Service Layer**
```
app/Services/Search/
├── SmartSearchEngine.php          # Główna klasa orchestrating
├── FilterProcessor.php            # Przetwarzanie filtrów
├── GeolocationService.php         # Operacje geograficzne
├── AvailabilityCalculator.php     # Kalkulacja dostępności
└── ScoringAlgorithm.php           # Algorytm rankingu
```

**Day 3-4: API Routes & Controllers**
```bash
php artisan make:controller Api/SearchController
php artisan make:controller Api/SuggestionController
php artisan make:controller Api/FilterController
```

**Routes (`routes/api.php`)**:
```php
// Search API
Route::prefix('search')->group(function () {
    Route::get('/unified', [SearchController::class, 'unified']);
    Route::post('/suggestions', [SuggestionController::class, 'get']);
    Route::get('/filters', [FilterController::class, 'available']);
    Route::get('/availability', [SearchController::class, 'checkAvailability']);
});
```

**Day 5: Unit Tests Setup**
```bash
php artisan make:test SearchEngineTest --unit
php artisan make:test FilterProcessorTest --unit
php artisan make:test GeolocationServiceTest --unit
```

---

### 🔷 Faza 2: Backend Search Engine (Tydzień 3-4)

#### Tydzień 3: Core Logic

**Day 1-2: SmartSearchEngine Implementation**

`app/Services/Search/SmartSearchEngine.php`:
```php
<?php

namespace App\Services\Search;

class SmartSearchEngine
{
    public function __construct(
        private FilterProcessor $filterProcessor,
        private GeolocationService $geolocationService,
        private ScoringAlgorithm $scoringAlgorithm,
    ) {}

    /**
     * Główna metoda wyszukiwania
     */
    public function search(array $params): SearchResult
    {
        // 1. Parse & validate parameters
        $validated = $this->validateParams($params);

        // 2. Get base query
        $query = $this->buildBaseQuery($validated);

        // 3. Apply filters
        $query = $this->filterProcessor->apply($query, $validated['filters']);

        // 4. Apply geolocation
        if ($validated['location']) {
            $query = $this->geolocationService->filterByDistance(
                $query,
                $validated['location']['lat'],
                $validated['location']['lng'],
                $validated['location']['radius']
            );
        }

        // 5. Apply scoring & sorting
        $results = $this->scoringAlgorithm->rankResults($query, $validated);

        // 6. Load relationships
        $results->load(['services', 'reviews', 'availability']);

        // 7. Track analytics
        $this->trackSearch($params, $results);

        return new SearchResult($results, $validated);
    }
}
```

**Day 3-4: FilterProcessor & GeolocationService**

`app/Services/Search/FilterProcessor.php`:
```php
<?php

namespace App\Services\Search;

class FilterProcessor
{
    public function apply($query, array $filters)
    {
        foreach ($filters as $filterName => $filterValue) {
            $method = 'apply' . ucfirst($filterName) . 'Filter';

            if (method_exists($this, $method)) {
                $query = $this->$method($query, $filterValue);
            }
        }

        return $query;
    }

    protected function applyPetTypeFilter($query, $petType)
    {
        return $query->whereHas('services', function ($q) use ($petType) {
            $q->whereJsonContains('pet_types', $petType);
        });
    }

    protected function applyRatingFilter($query, $minRating)
    {
        return $query->withAvg('reviews', 'rating')
            ->having('reviews_avg_rating', '>=', $minRating);
    }

    // ... więcej filtrów
}
```

**Day 5: ScoringAlgorithm**

`app/Services/Search/ScoringAlgorithm.php`:
```php
<?php

namespace App\Services\Search;

class ScoringAlgorithm
{
    /**
     * Ranking score - wagi czynników
     */
    private const WEIGHTS = [
        'rating' => 0.30,        // 30% - ocena
        'distance' => 0.25,      // 25% - odległość
        'availability' => 0.20,  // 20% - dostępność
        'response_rate' => 0.15, // 15% - szybkość odpowiedzi
        'price_match' => 0.10,   // 10% - dopasowanie cenowe
    ];

    public function rankResults($query, array $context)
    {
        $results = $query->get();

        // Calculate score for each result
        $results->each(function ($sitter) use ($context) {
            $sitter->match_score = $this->calculateScore($sitter, $context);
        });

        // Sort by score DESC
        return $results->sortByDesc('match_score');
    }

    private function calculateScore($sitter, $context): float
    {
        $score = 0;

        // Rating score (0-1)
        $score += ($sitter->average_rating / 5) * self::WEIGHTS['rating'];

        // Distance score (closer = better)
        $maxDistance = $context['location']['radius'] ?? 10;
        $distanceScore = max(0, 1 - ($sitter->distance / $maxDistance));
        $score += $distanceScore * self::WEIGHTS['distance'];

        // Availability score
        $availabilityScore = $this->calculateAvailabilityScore($sitter, $context);
        $score += $availabilityScore * self::WEIGHTS['availability'];

        // Response rate
        $score += $sitter->response_rate * self::WEIGHTS['response_rate'];

        // Price match
        $priceScore = $this->calculatePriceMatchScore($sitter, $context);
        $score += $priceScore * self::WEIGHTS['price_match'];

        return round($score, 3);
    }
}
```

#### Tydzień 4: API Implementation

**Day 1-2: SearchController**

`app/Http/Controllers/Api/SearchController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Services\Search\SmartSearchEngine;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SmartSearchEngine $searchEngine
    ) {}

    /**
     * GET /api/search/unified
     */
    public function unified(Request $request)
    {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
            'pet_type' => 'nullable|string',
            'service_type' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'radius' => 'nullable|integer|min:1|max:100',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'max_price' => 'nullable|numeric',
            'availability' => 'nullable|string',
            'sort_by' => 'nullable|string|in:best_match,rating,price,distance',
            'page' => 'nullable|integer|min:1',
        ]);

        $results = $this->searchEngine->search($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'sitters' => $results->sitters,
                'meta' => [
                    'total' => $results->total,
                    'per_page' => $results->perPage,
                    'current_page' => $results->currentPage,
                    'filters_applied' => $results->appliedFilters,
                    'search_time_ms' => $results->searchTime,
                ],
            ],
        ]);
    }
}
```

**Day 3-4: SuggestionController (AI)**

`app/Http/Controllers/Api/SuggestionController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Services\AI\SuggestionEngine;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function __construct(
        private SuggestionEngine $suggestionEngine
    ) {}

    /**
     * POST /api/search/suggestions
     */
    public function get(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->suggestionEngine->generate(
            $query,
            $request->user(),
            $request->input('context', [])
        );

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }
}
```

**Day 5: Integration Tests**
```bash
php artisan make:test SearchApiTest
php artisan make:test SuggestionApiTest
```

---

### 🔷 Faza 3: Frontend Podstawy (Tydzień 4-5)

#### Tydzień 4: Layout & Components

**Day 1: Blade Layout**

`resources/views/search/index.blade.php`:
```blade
<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Search Header -->
        <livewire:search.search-bar />

        <!-- Main Content: Map + List -->
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Map (60%) -->
                <div class="lg:col-span-3">
                    <livewire:search.map-view />
                </div>

                <!-- Results List (40%) -->
                <div class="lg:col-span-2">
                    <livewire:search.results-list />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

**Day 2-3: Livewire Components**

```bash
php artisan make:livewire Search/SearchBar
php artisan make:livewire Search/MapView
php artisan make:livewire Search/ResultsList
php artisan make:livewire Search/FilterPanel
```

**`app/Livewire/Search/SearchBar.php`**:
```php
<?php

namespace App\Livewire\Search;

use Livewire\Component;

class SearchBar extends Component
{
    public $query = '';
    public $suggestions = [];
    public $showSuggestions = false;

    protected $listeners = ['filtersUpdated' => 'handleFiltersUpdate'];

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->fetchSuggestions();
        } else {
            $this->suggestions = [];
        }
    }

    public function fetchSuggestions()
    {
        // Call API for suggestions
        $this->suggestions = Http::post('/api/search/suggestions', [
            'query' => $this->query,
        ])->json('suggestions', []);

        $this->showSuggestions = true;
    }

    public function search()
    {
        $this->dispatch('searchExecuted', [
            'query' => $this->query,
        ]);
    }

    public function render()
    {
        return view('livewire.search.search-bar');
    }
}
```

**Day 4-5: Map Integration (Leaflet)**

`resources/views/livewire/search/map-view.blade.php`:
```blade
<div wire:ignore>
    <div id="search-map" class="h-full w-full rounded-xl"></div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const map = L.map('search-map').setView([52.2297, 21.0122], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Listen for search results
    Livewire.on('searchResults', (data) => {
        updateMapMarkers(data.sitters);
    });

    function updateMapMarkers(sitters) {
        // Clear existing markers
        map.eachLayer(layer => {
            if (layer instanceof L.Marker) {
                map.removeLayer(layer);
            }
        });

        // Add new markers
        sitters.forEach(sitter => {
            const marker = L.marker([sitter.latitude, sitter.longitude])
                .bindPopup(createPopupContent(sitter))
                .addTo(map);
        });
    }
});
</script>
@endpush
```

---

### 🔷 Faza 4: Integracja & AI (Tydzień 5-6)

#### Tydzień 5: AI Suggestions

**Day 1-2: SuggestionEngine Service**

`app/Services/AI/SuggestionEngine.php`:
```php
<?php

namespace App\Services\AI;

use OpenAI\Laravel\Facades\OpenAI;

class SuggestionEngine
{
    public function generate(string $query, ?User $user, array $context): array
    {
        // 1. Get user preferences
        $preferences = $user?->searchPreferences ?? [];

        // 2. Analyze query with NLP
        $parsed = $this->parseQuery($query);

        // 3. Get relevant suggestions
        $suggestions = [
            'ai' => $this->getAISuggestions($query, $preferences),
            'recent' => $this->getRecentSearches($user),
            'popular' => $this->getPopularSearches($context),
        ];

        return $suggestions;
    }

    private function getAISuggestions(string $query, array $preferences): array
    {
        $prompt = "Użytkownik szuka opiekuna dla zwierząt. Zapytanie: '{$query}'.
                   Preferencje użytkownika: " . json_encode($preferences) . ".
                   Zasugeruj 3 kompletne zapytania wyszukiwania.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Jesteś asystentem wyszukiwarki opiekunów zwierząt.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return $this->parseSuggestions($response->choices[0]->message->content);
    }
}
```

**Day 3-4: Smart Filters**

`app/Livewire/Search/FilterPanel.php`:
```php
<?php

namespace App\Livewire\Search;

use Livewire\Component;

class FilterPanel extends Component
{
    public $filters = [
        'pet_type' => null,
        'service_type' => null,
        'radius' => 10,
        'min_rating' => null,
        'max_price' => null,
        'availability' => null,
    ];

    public $availableFilters = [];
    public $activeFilters = [];

    public function mount()
    {
        $this->loadAvailableFilters();
    }

    public function updatedFilters()
    {
        $this->dispatch('filtersChanged', $this->getActiveFilters());
    }

    public function loadAvailableFilters()
    {
        // Load dynamic filters based on search context
        $this->availableFilters = Http::get('/api/search/filters', [
            'context' => $this->getContext(),
        ])->json();
    }

    public function render()
    {
        return view('livewire.search.filter-panel');
    }
}
```

**Day 5: User Preferences**

```bash
php artisan make:controller Api/UserPreferenceController
```

`app/Http/Controllers/Api/UserPreferenceController.php`:
```php
public function saveSearchPreferences(Request $request)
{
    $validated = $request->validate([
        'pet_types' => 'array',
        'preferred_radius' => 'integer',
        'price_range' => 'array',
        'min_rating' => 'numeric',
    ]);

    $request->user()->searchPreferences()->updateOrCreate(
        ['user_id' => $request->user()->id],
        $validated
    );

    return response()->json(['success' => true]);
}
```

#### Tydzień 6: Search Analytics

**Day 1-2: Analytics Tracking**

`app/Services/Search/SearchAnalytics.php`:
```php
<?php

namespace App\Services\Search;

use App\Models\SearchAnalytic;

class SearchAnalytics
{
    public function track(array $searchParams, $results, ?User $user)
    {
        SearchAnalytic::create([
            'user_id' => $user?->id,
            'session_id' => session()->getId(),
            'query_text' => $searchParams['query'] ?? null,
            'filters_applied' => $searchParams,
            'results_count' => $results->count(),
            'location_lat' => $searchParams['lat'] ?? null,
            'location_lng' => $searchParams['lng'] ?? null,
        ]);
    }

    public function trackClick($searchId, $sitterId)
    {
        $analytic = SearchAnalytic::find($searchId);
        $analytic->clicked_results = array_merge(
            $analytic->clicked_results ?? [],
            [$sitterId]
        );
        $analytic->save();
    }
}
```

**Day 3-5: Performance Optimization**
- Cache strategy (Redis)
- Query optimization
- Eager loading
- Index optimization

---

### 🔷 Faza 5: Wydarzenia & Usługi (Tydzień 6-7)

#### Tydzień 6: Events Integration

**Day 1-2: Event Model & API**

```bash
php artisan make:controller Api/EventController --resource
```

**Day 3-4: Map Layers**

`resources/js/components/map-layers.js`:
```javascript
class MapLayerManager {
    constructor(map) {
        this.map = map;
        this.layers = {
            sitters: L.layerGroup(),
            events: L.layerGroup(),
            services: L.layerGroup(),
        };

        // Add all layers to map
        Object.values(this.layers).forEach(layer => layer.addTo(map));
    }

    toggleLayer(layerName, visible) {
        if (visible) {
            this.layers[layerName].addTo(this.map);
        } else {
            this.map.removeLayer(this.layers[layerName]);
        }
    }

    updateSitters(sitters) {
        this.layers.sitters.clearLayers();
        sitters.forEach(sitter => {
            L.marker([sitter.lat, sitter.lng])
                .bindPopup(this.createSitterPopup(sitter))
                .addTo(this.layers.sitters);
        });
    }

    updateEvents(events) {
        this.layers.events.clearLayers();
        events.forEach(event => {
            L.marker([event.lat, event.lng], {
                icon: this.createEventIcon()
            })
                .bindPopup(this.createEventPopup(event))
                .addTo(this.layers.events);
        });
    }
}
```

**Day 5: Multi-Search Tabs**

`app/Livewire/Search/SearchTabs.php`:
```php
<?php

namespace App\Livewire\Search;

use Livewire\Component;

class SearchTabs extends Component
{
    public $activeTab = 'sitters'; // sitters, events, services

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function render()
    {
        return view('livewire.search.search-tabs');
    }
}
```

#### Tydzień 7: Local Services

**Day 1-3: Services CRUD**
- LocalService model
- API endpoints
- Map integration

**Day 4-5: Final Integration**
- Połączenie wszystkich warstw
- Synchronizacja mapa ↔️ lista
- Tests

---

### 🔷 Faza 6: Optymalizacja & Launch (Tydzień 7-8)

#### Tydzień 7: Performance & Testing

**Day 1-2: Optimization**
- Database indexes
- Query optimization
- Cache implementation (Redis)
- API rate limiting

**Day 3-4: Testing**
```bash
# Feature tests
php artisan make:test Search/SearchFlowTest
php artisan make:test Search/FilteringTest
php artisan make:test Search/MapIntegrationTest

# Run all tests
php artisan test --parallel
```

**Day 5: Mobile Responsiveness**
- Responsive design
- Touch interactions
- Mobile-specific optimizations

#### Tydzień 8: Launch Preparation

**Day 1-2: Documentation**
- API documentation (Swagger)
- User guide
- Admin documentation

**Day 3: Final QA**
- Cross-browser testing
- Load testing
- Security audit

**Day 4: Deploy to Staging**
```bash
# Build assets
npm run build

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Deploy
php artisan migrate --force
php artisan db:seed --class=EventSeeder
```

**Day 5: Production Deploy & Monitoring**
- Production deployment
- Monitoring setup (Laravel Telescope, Sentry)
- Analytics tracking (Google Analytics, Hotjar)

---

## 🧪 Testing Strategy

### Unit Tests
- SmartSearchEngine
- FilterProcessor
- GeolocationService
- ScoringAlgorithm

### Feature Tests
- Search flow (query → results)
- Filtering workflow
- Map interactions
- Multi-search (tabs)

### Integration Tests
- API endpoints
- Database queries
- External services (Maps API)

### E2E Tests (Dusk)
```bash
php artisan make:test Browser/SearchJourneyTest
```

---

## 📊 Monitoring & Analytics

### Metryki do śledzenia:
1. **Performance**
   - Search response time (target: <500ms)
   - API endpoint latency
   - Map load time

2. **Usage**
   - Searches per day
   - Popular filters
   - Click-through rate
   - Conversion rate (search → booking)

3. **Technical**
   - Error rate
   - Cache hit rate
   - Database query time

### Tools:
- Laravel Telescope (local dev)
- Laravel Horizon (queue monitoring)
- Sentry (error tracking)
- Google Analytics
- Hotjar (heatmaps)

---

## 🚧 Ryzyka & Mitigation

| Ryzyko | Prawdopodobieństwo | Wpływ | Mitigation |
|--------|-------------------|-------|------------|
| Performance issues z dużą liczbą wyników | Medium | High | Cache, pagination, indexes |
| AI suggestions wolne | Medium | Medium | Cache responses, async processing |
| Map rendering lag | Low | Medium | Clustering, lazy loading |
| Mobile compatibility issues | Medium | High | Progressive enhancement, responsive design |

---

## 🎓 Wymagane Umiejętności

### Backend Developer:
- Laravel 12 (expert)
- Eloquent ORM
- MySQL optimization
- Redis caching
- API development
- Testing (PHPUnit, Pest)

### Frontend Developer:
- Livewire 3
- Alpine.js
- Tailwind CSS
- Leaflet.js
- JavaScript ES6+
- Responsive design

---

## 📦 Pakiety do zainstalowania

```bash
# Search & Geolocation
composer require laravel/scout
composer require algolia/algoliasearch-client-php
composer require geocoder-php/geocoder

# AI & NLP
composer require openai-php/laravel

# Maps
npm install leaflet
npm install leaflet.markercluster

# Testing
composer require --dev laravel/dusk
```

---

## 🚀 Quick Start Commands

```bash
# 1. Setup database
php artisan migrate:fresh --seed

# 2. Install dependencies
composer install
npm install

# 3. Build assets
npm run dev

# 4. Run tests
php artisan test

# 5. Start development server
php artisan serve
```

---

## 📚 Dokumentacja Zewnętrzna

- [Laravel Scout Docs](https://laravel.com/docs/11.x/scout)
- [Leaflet.js Docs](https://leafletjs.com/)
- [OpenAI API Docs](https://platform.openai.com/docs)
- [Livewire 3 Docs](https://livewire.laravel.com/docs/quickstart)

---

**Przygotował**: Claude AI Assistant
**Data**: 2025-10-03
**Wersja**: 1.0
