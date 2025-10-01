<div x-data="{
    currentAddress: @js($address ?? ''),
    serviceRadius: @js($serviceRadius ?? 5),
    radiusLabel: @js($serviceRadius ?? 5) + ' km',
    currentSuggestions: [],
    selectedIndex: -1,

    // Inicjalizacja komponentu
    init() {
        console.log('🗺️ Krok 5 zainicjalizowany');
    },

    // Aktualizacja promienia obsługi
    updateRadius(newRadius) {
        this.serviceRadius = newRadius;
        this.radiusLabel = newRadius + ' km';
        this.updateLivewire('serviceRadius', newRadius);
    },

    // Wyszukiwanie adresów
    async searchAddress(query) {
        if (query.length < 3) {
            this.currentSuggestions = [];
            return;
        }

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Poland')}&format=json&limit=5`);
            const data = await response.json();

            this.currentSuggestions = data.map(item => ({
                display_name: item.display_name,
                lat: parseFloat(item.lat),
                lon: parseFloat(item.lon)
            }));

            this.selectedIndex = -1;
        } catch (error) {
            console.error('Błąd podczas wyszukiwania adresu:', error);
            this.currentSuggestions = [];
        }
    },

    // Wybór adresu z sugestii
    selectAddress(suggestion) {
        this.currentAddress = suggestion.display_name;
        this.currentSuggestions = [];
        this.selectedIndex = -1;

        // Aktualizuj Livewire
        this.updateLivewire('address', this.currentAddress);
        this.updateLivewire('latitude', suggestion.lat);
        this.updateLivewire('longitude', suggestion.lon);
    },

    // Nawigacja w sugestiach za pomocą klawiszy
    handleKeyNavigation(event) {
        if (this.currentSuggestions.length === 0) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.currentSuggestions.length - 1);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                break;
            case 'Enter':
                event.preventDefault();
                if (this.selectedIndex >= 0) {
                    this.selectAddress(this.currentSuggestions[this.selectedIndex]);
                }
                break;
            case 'Escape':
                this.currentSuggestions = [];
                this.selectedIndex = -1;
                break;
        }
    },

    // Aktualizacja Livewire
    updateLivewire(property, value) {
        if (window.Livewire && this.$wire) {
            this.$wire.set(property, value, false);
        }
    }
}">

{{-- Header --}}
<div class="text-center mb-6 sm:mb-8">
    <div class="text-6xl mb-4">📍</div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Gdzie będziesz świadczyć usługi?</h1>
    <p class="text-gray-600 text-lg">Ustaw swoją lokalizację i promień działania</p>
</div>

<div class="space-y-6">
    {{-- Wyszukiwanie adresu --}}
    <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-3">
            Twój adres lub lokalizacja *
        </label>
        <div class="relative">
            <input
                x-model="currentAddress"
                @input.debounce.300ms="searchAddress($event.target.value)"
                @keydown="handleKeyNavigation($event)"
                type="text"
                id="address"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                placeholder="Wprowadź adres, miasto lub kod pocztowy..."
                autocomplete="off">

            {{-- Sugestie adresów --}}
            <div x-show="currentSuggestions.length > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                <template x-for="(suggestion, index) in currentSuggestions" :key="suggestion.display_name">
                    <div @click="selectAddress(suggestion)"
                         :class="{'bg-emerald-50 border-emerald-200': selectedIndex === index}"
                         class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors">
                        <div class="text-sm font-medium text-gray-900" x-text="suggestion.display_name.split(',')[0]"></div>
                        <div class="text-xs text-gray-500" x-text="suggestion.display_name"></div>
                    </div>
                </template>
            </div>
        </div>

        @error('address')
            <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    {{-- Placeholder dla mapy - uproszczona wersja --}}
    <div class="bg-gray-100 rounded-xl p-8 text-center">
        <div class="text-4xl mb-4">🗺️</div>
        <p class="text-gray-600">Mapa zostanie dodana w przyszłej aktualizacji</p>
        <p class="text-sm text-gray-500 mt-2">Na razie wprowadź adres powyżej</p>
    </div>

    {{-- Promień obsługi --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">
            Promień obsługi: <span x-text="radiusLabel" class="font-semibold text-emerald-600"></span>
        </label>
        <div class="px-3">
            <input
                type="range"
                min="1"
                max="50"
                x-model="serviceRadius"
                @input="updateRadius($event.target.value)"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>1 km</span>
                <span>25 km</span>
                <span>50 km</span>
            </div>
        </div>

        @error('serviceRadius')
            <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    {{-- Analiza rynku dla lokalizacji --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
            <span class="text-2xl mr-3">📊</span>
            Analiza rynku dla Twojej lokalizacji
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Popyt na usługi --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Popyt na usługi</h4>
                    <span class="text-green-600 font-semibold">Wysoki</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                </div>
                <p class="text-xs text-gray-600 mt-2">Wysoki popyt na opiekę nad zwierzętami w Twojej okolicy</p>
            </div>

            {{-- Konkurencja --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Konkurencja</h4>
                    <span class="text-orange-600 font-semibold">Średnia</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: 60%"></div>
                </div>
                <p class="text-xs text-gray-600 mt-2">Umiarkowana liczba opiekunów w okolicy</p>
            </div>

            {{-- Średnie ceny --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Średnie ceny</h4>
                    <span class="text-blue-600 font-semibold">45-65 zł</span>
                </div>
                <div class="text-xs text-gray-600">
                    <div>• Spacer: 25-35 zł</div>
                    <div>• Opieka dzienna: 45-65 zł</div>
                    <div>• Opieka nocna: 80-120 zł</div>
                </div>
            </div>

            {{-- Potencjalne zarobki --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Potencjalne zarobki</h4>
                    <span class="text-emerald-600 font-semibold">800-2500 zł</span>
                </div>
                <div class="text-xs text-gray-600">
                    <div>• Part-time: 800-1500 zł/mies.</div>
                    <div>• Full-time: 1500-2500 zł/mies.</div>
                </div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-blue-100 rounded-lg">
            <p class="text-sm text-blue-800">
                💡 <strong>Wskazówka:</strong> Większy promień obsługi zwiększa liczbę potencjalnych klientów, ale pamiętaj o kosztach dojazdu.
            </p>
        </div>
    </div>

    {{-- Wskazówki dotyczące lokalizacji --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="text-yellow-500 mr-3">💡</div>
            <div>
                <h4 class="font-medium text-yellow-900 mb-1">Wskazówki dotyczące lokalizacji</h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Większy promień = więcej potencjalnych klientów</li>
                    <li>• Uwzględnij czas dojazdu w swoich cenach</li>
                    <li>• Możesz później zmienić swój promień obsługi</li>
                    <li>• Klienci często szukają opiekuna blisko siebie</li>
                </ul>
            </div>
        </div>
    </div>
</div>

</div>