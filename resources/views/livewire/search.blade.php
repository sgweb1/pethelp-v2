<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-4">Znajdź idealnego opiekuna dla swojego pupila</h1>
            <p class="text-white/90 text-lg">Przeszukuj setki zweryfikowanych opiekunów w Twojej okolicy</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
            <!-- Quick Search Bar -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Szukaj opiekuna lub usługi</label>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search_term"
                    placeholder="Wpisz nazwę opiekuna, usługę lub słowo kluczowe..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Location Input -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokalizacja</label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="location"
                            placeholder="Wpisz miasto lub kod pocztowy..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        <button
                            wire:click="detectLocation"
                            class="absolute right-3 top-3 text-gray-400 hover:text-primary-600 transition-colors duration-200"
                            title="Wykryj moją lokalizację"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>
                    @if($location_detected)
                        <p class="text-sm text-success-600 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Lokalizacja wykryta
                        </p>
                    @endif
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategoria usługi</label>
                    <select
                        wire:model.live="category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Wszystkie kategorie</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pet Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typ zwierzęcia</label>
                    <select
                        wire:model.live="pet_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Wszystkie</option>
                        <option value="dog">🐕 Pies</option>
                        <option value="cat">🐱 Kot</option>
                        <option value="bird">🐦 Ptak</option>
                        <option value="rabbit">🐰 Królik</option>
                        <option value="rodent">🐹 Gryzoń</option>
                        <option value="fish">🐠 Ryba</option>
                        <option value="reptile">🦎 Gad</option>
                        <option value="other">🐾 Inne</option>
                    </select>
                </div>
            </div>

            <!-- Filters Toggle & Search Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-4">
                    <x-ui.button
                        wire:click="$toggle('show_filters')"
                        variant="outline"
                        size="sm"
                        class="relative"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        {{ $show_filters ? 'Ukryj filtry' : 'Więcej filtrów' }}
                        @if($this->active_filters_count > 0)
                            <span class="absolute -top-2 -right-2 bg-danger-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $this->active_filters_count }}
                            </span>
                        @endif
                    </x-ui.button>

                    @if($show_map)
                        <x-ui.button
                            wire:click="$toggle('show_map')"
                            variant="secondary"
                            size="sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Lista
                        </x-ui.button>
                    @else
                        <x-ui.button
                            wire:click="$toggle('show_map')"
                            variant="secondary"
                            size="sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Mapa
                        </x-ui.button>
                    @endif

                    <x-ui.button
                        wire:click="clearFilters"
                        variant="ghost"
                        size="sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Wyczyść
                    </x-ui.button>

                    @auth
                        <x-ui.button
                            wire:click="saveSearch"
                            variant="ghost"
                            size="sm"
                            title="Zapisz wyszukiwanie"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Zapisz
                        </x-ui.button>
                    @endauth
                </div>

                <div class="text-sm text-gray-600">
                    Znaleziono <span class="font-semibold">{{ $this->results_count }}</span> opiekunów
                </div>
            </div>

            <!-- Advanced Filters -->
            @if($show_filters)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Zaawansowane filtry</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Pet Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rozmiar zwierzęcia</label>
                            <select
                                wire:model.live="pet_size"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Wszystkie</option>
                                <option value="small">Małe (do 10kg)</option>
                                <option value="medium">Średnie (10-25kg)</option>
                                <option value="large">Duże (25kg+)</option>
                            </select>
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rodzaj opieki</label>
                            <select
                                wire:model.live="service_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Wszystkie</option>
                                <option value="home_service">U klienta</option>
                                <option value="sitter_home">U opiekuna</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cena (zł)</label>
                            <div class="flex space-x-2">
                                <input
                                    type="number"
                                    wire:model.live.debounce.500ms="min_price"
                                    placeholder="Od"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                <input
                                    type="number"
                                    wire:model.live.debounce.500ms="max_price"
                                    placeholder="Do"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="price_type" value="hour" class="text-indigo-600">
                                    <span class="ml-2 text-sm">za godzinę</span>
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="radio" wire:model.live="price_type" value="day" class="text-indigo-600">
                                    <span class="ml-2 text-sm">za dzień</span>
                                </label>
                            </div>
                        </div>

                        <!-- Min Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. ocena</label>
                            <select
                                wire:model.live="min_rating"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Wszystkie</option>
                                <option value="3">⭐⭐⭐ 3+ gwiazdki</option>
                                <option value="4">⭐⭐⭐⭐ 4+ gwiazdki</option>
                                <option value="4.5">⭐⭐⭐⭐⭐ 4.5+ gwiazdki</option>
                            </select>
                        </div>
                    </div>

                    <!-- Second Row of Advanced Filters -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Available Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data dostępności</label>
                            <input
                                type="date"
                                wire:model.live="available_date"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Od godziny</label>
                            <input
                                type="time"
                                wire:model.live="start_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Do godziny</label>
                            <input
                                type="time"
                                wire:model.live="end_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <!-- Max Pets -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maksymalnie zwierząt</label>
                            <select
                                wire:model.live="max_pets"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Wszystkie</option>
                                <option value="1">1 zwierzę</option>
                                <option value="2">2 zwierzęta</option>
                                <option value="3">3 zwierzęta</option>
                                <option value="5">5+ zwierząt</option>
                            </select>
                        </div>
                    </div>

                    <!-- Third Row - Checkboxes and Additional Filters -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Experience Years -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. lat doświadczenia</label>
                            <select
                                wire:model.live="experience_years"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Wszystkie</option>
                                <option value="1">1+ rok</option>
                                <option value="3">3+ lata</option>
                                <option value="5">5+ lat</option>
                                <option value="10">10+ lat</option>
                            </select>
                        </div>

                        <!-- Checkboxes Column 1 -->
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="verified_only"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">✓ Tylko zweryfikowani</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="instant_booking"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">⚡ Natychmiastowa rezerwacja</span>
                            </label>
                        </div>

                        <!-- Checkboxes Column 2 -->
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="has_insurance"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">🛡️ Ma ubezpieczenie</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="flexible_cancellation"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">🔄 Elastyczne anulowanie</span>
                            </label>
                        </div>

                        <!-- Empty space for visual balance -->
                        <div></div>
                    </div>

                    <!-- Fourth Row - Radius and Sort -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Radius -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Promień wyszukiwania: {{ $radius }} km
                            </label>
                            <input
                                type="range"
                                wire:model.live="radius"
                                min="1"
                                max="50"
                                class="w-full"
                            >
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sortuj według</label>
                            <select
                                wire:model.live="sort_by"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="relevance">Trafność</option>
                                <option value="distance">Odległość</option>
                                <option value="price_low">Cena: od najniższej</option>
                                <option value="price_high">Cena: od najwyższej</option>
                                <option value="rating">Najwyżej oceniane</option>
                                <option value="experience">Doświadczenie</option>
                                <option value="most_booked">Najczęściej rezerwowane</option>
                                <option value="newest">Najnowsze</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Map View (placeholder) -->
        @if($show_map)
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
                <div class="h-96 bg-gray-100 rounded-lg" id="map-container">
                    <div id="search-map" class="w-full h-full rounded-lg"></div>
                </div>
            </div>
        @endif

        <!-- Results -->
        @if($this->results_count > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($this->services as $service)
                    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!-- Service Image/Avatar -->
                        <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                            <div class="text-center text-white">
                                <div class="text-4xl mb-2">{{ $service->category->icon ?? '🐾' }}</div>
                                <div class="text-lg font-semibold">{{ $service->sitter->name }}</div>
                            </div>
                        </div>

                        <!-- Service Content -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $service->title }}</h3>
                                @if($service->average_rating > 0)
                                    <div class="flex items-center text-sm text-yellow-600 ml-2">
                                        <span>⭐</span>
                                        <span class="ml-1">{{ $service->average_rating }}</span>
                                        <span class="text-xs text-gray-500">({{ $service->reviews_count }})</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Sitter badges -->
                            <div class="flex flex-wrap gap-1 mb-3">
                                @if($service->sitter->profile?->is_verified)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">✓ Zweryfikowany</span>
                                @endif
                                @if($service->sitter->profile?->instant_booking)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">⚡ Natychmiastowa rezerwacja</span>
                                @endif
                                @if($service->sitter->profile?->has_insurance)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">🛡️ Ubezpieczony</span>
                                @endif
                                @if($service->sitter->profile?->experience_years >= 5)
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">🏆 Doświadczony</span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $service->description }}</p>

                            @if($service->sitter->profile?->experience_years)
                                <p class="text-xs text-gray-500 mb-2">{{ $service->sitter->profile->experience_display }}</p>
                            @endif

                            <!-- Service Types -->
                            @if($service->service_types)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($service->service_types as $type)
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">{{ $type }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Pet Types -->
                            @if($service->pet_types)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($service->pet_types as $petType)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                            {{ ucfirst($petType) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Price and Location -->
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                <span>{{ $service->display_price }}</span>
                                @if($service->sitter->locations->first())
                                    <span>📍 {{ $service->sitter->locations->first()->city }}</span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('sitter.show', $service->sitter) }}" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors text-center">
                                Zobacz profil
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $this->services->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-6 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nie znaleziono opiekunów</h3>
                <p class="text-gray-600 mb-6">Spróbuj zmienić kryteria wyszukiwania lub rozszerzyć obszar poszukiwań.</p>
                <x-ui.button
                    wire:click="clearFilters"
                    variant="primary"
                    size="lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Wyczyść filtry
                </x-ui.button>
            </div>
        @endif
    </div>

    @push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    @endpush

    @push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    <!-- Location Detection and Map Script -->
    <script>
        let map = null;
        let markers = [];

        document.addEventListener('livewire:init', () => {
            // Initialize map when map view is shown
            Livewire.on('map-toggled', () => {
                setTimeout(() => initializeMap(), 100);
            });

            // Also try to initialize map after DOM updates
            document.addEventListener('livewire:updated', () => {
                if (document.getElementById('search-map') && !map) {
                    setTimeout(() => initializeMap(), 100);
                }
            });

            // Location detection
            Livewire.on('detect-location', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Use reverse geocoding to get address
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`)
                                .then(response => response.json())
                                .then(data => {
                                    const address = data.display_name ?
                                        data.address?.city || data.address?.town || data.address?.village || 'Twoja lokalizacja'
                                        : 'Twoja lokalizacja';

                                    @this.call('setLocation', lat, lng, address);
                                })
                                .catch(() => {
                                    @this.call('setLocation', lat, lng, 'Twoja lokalizacja');
                                });
                        },
                        function(error) {
                            alert('Nie udało się wykryć lokalizacji. Sprawdź uprawnienia przeglądarki.');
                        }
                    );
                } else {
                    alert('Geolokalizacja nie jest obsługiwana przez Twoją przeglądarkę.');
                }
            });
        });

        function initializeMap() {
            const mapContainer = document.getElementById('search-map');
            if (!mapContainer || map) return;

            // Initialize map centered on Poland
            map = L.map('search-map').setView([52.2297, 21.0122], 6);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add markers for services
            @if($this->services && count($this->services) > 0)
                @foreach($this->services as $service)
                    @if($service->sitter && $service->sitter->locations->count() > 0)
                        @foreach($service->sitter->locations as $location)
                            @if($location->latitude && $location->longitude)
                                const marker{{ $service->id }}_{{ $location->id }} = L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
                                    .addTo(map)
                                    .bindPopup(`
                                        <div class="text-center">
                                            <h3 class="font-semibold">{{ addslashes($service->sitter->name) }}</h3>
                                            <p class="text-sm">{{ addslashes($service->title) }}</p>
                                            <p class="text-xs text-gray-600">{{ addslashes($service->display_price) }}</p>
                                        </div>
                                    `);
                                markers.push(marker{{ $service->id }}_{{ $location->id }});
                            @endif
                        @endforeach
                    @endif
                @endforeach

                // Fit map to markers if any exist
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            @endif

            // Add user location marker if available
            @if($latitude && $longitude)
                const userMarker = L.marker([{{ $latitude }}, {{ $longitude }}], {
                    icon: L.icon({
                        iconUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTAiIGZpbGw9IiMzQjgyRjYiLz4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iNCIgZmlsbD0id2hpdGUiLz4KPC9zdmc+',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(map)
                .bindPopup('Twoja lokalizacja');

                map.setView([{{ $latitude }}, {{ $longitude }}], 12);
            @endif
        }

        // Clean up map when component is destroyed
        document.addEventListener('livewire:navigating', () => {
            if (map) {
                map.remove();
                map = null;
                markers = [];
            }
        });

        // Search saved notification
        document.addEventListener('livewire:init', () => {
            Livewire.on('search-saved', () => {
                // Show a simple notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = '💾 Wyszukiwanie zostało zapisane!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        });
    </script>
    @endpush
