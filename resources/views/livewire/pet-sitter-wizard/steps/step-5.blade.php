{{-- Krok 5: Adres i promień obsługi - Architektura v3.0 + Enhanced UI v4 --}}
<div class="max-w-2xl mx-auto px-4"
     x-data="wizardStep5()"
     x-init="console.log('Step 5 Alpine loaded', { mapInitialized, serviceRadius })">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Gdzie oferujesz swoje usługi?</h1>
        <p class="text-gray-600 text-lg">Określ swój adres i promień, w którym będziesz świadczyć usługi</p>
    </div>

    <div class="space-y-6">
        {{-- Hidden Address Field --}}
        <div class="hidden">
            <input
                type="hidden"
                id="address"
                :value="(typeof currentAddress !== 'undefined') ? currentAddress : ''">
        </div>

        {{-- Quick Tips Card --}}
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl border-2 border-blue-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💡</span>
                    <h3 class="text-sm font-semibold text-gray-900">Wskazówki dotyczące lokalizacji</h3>
                </div>
                <button type="button"
                        @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="flex items-center text-xs cursor-pointer hover:scale-105 transition-transform duration-200 px-3 py-1.5 rounded-lg hover:bg-white/50">
                    <div class="w-5 h-5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center mr-2">
                        <span class="text-white text-xs">💡</span>
                    </div>
                    <span class="font-medium text-gray-700" x-text="$wire.showAIPanel ? 'Ukryj wskazówki' : 'Więcej wskazówek'"></span>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-3 hover:bg-white transition-colors">
                    <div class="flex items-center text-sm text-gray-700">
                        <span class="text-xl mr-2">🎯</span>
                        <span>Promień 5-10 km = optymalny zasięg</span>
                    </div>
                </div>
                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-3 hover:bg-white transition-colors">
                    <div class="flex items-center text-sm text-gray-700">
                        <span class="text-xl mr-2">🏙️</span>
                        <span>Centrum miasta = więcej klientów</span>
                    </div>
                </div>
                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-3 hover:bg-white transition-colors">
                    <div class="flex items-center text-sm text-gray-700">
                        <span class="text-xl mr-2">🚗</span>
                        <span>Uwzględnij czas dojazdu</span>
                    </div>
                </div>
                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-3 hover:bg-white transition-colors">
                    <div class="flex items-center text-sm text-gray-700">
                        <span class="text-xl mr-2">🌳</span>
                        <span>Sprawdź dostęp do parków</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Radius Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <label for="serviceRadius" class="block text-sm font-semibold text-gray-900">
                    Promień obsługi <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Przeciągnij suwak</span>
                    <span class="text-lg">👉</span>
                </div>
            </div>

            {{-- Radius Display with Gradient --}}
            <div class="mb-12">
                <div class="relative overflow-hidden rounded-2xl" style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);">
                    <div class="relative z-10 text-center py-6 px-4">
                        <div class="text-4xl font-bold text-white mb-2" x-text="(typeof radiusLabel !== 'undefined') ? radiusLabel : '0 km'"></div>
                        <div class="text-emerald-50 text-sm font-medium">
                            <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius < 10">Zasięg lokalny - szybkie dojazdy</span>
                            <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 10 && serviceRadius < 20">Zasięg miejski - zbalansowany</span>
                            <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 20 && serviceRadius < 40">Zasięg rozszerzony - więcej klientów</span>
                            <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 40">Zasięg maksymalny - cała aglomeracja</span>
                        </div>
                    </div>
                    {{-- Animated background circles --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                </div>
            </div>

            {{-- Radius Slider with Icons --}}
            <div class="relative mb-2">
                {{-- Icons along the slider --}}
                <div class="absolute -top-8 left-0 right-0 flex justify-between pointer-events-none">
                    <span class="text-2xl" :class="(typeof serviceRadius !== 'undefined') && serviceRadius < 15 ? 'opacity-100 scale-110' : 'opacity-30'" style="transition: all 0.3s;">🏠</span>
                    <span class="text-2xl" :class="(typeof serviceRadius !== 'undefined') && serviceRadius >= 15 && serviceRadius < 30 ? 'opacity-100 scale-110' : 'opacity-30'" style="transition: all 0.3s;">🏙️</span>
                    <span class="text-2xl" :class="(typeof serviceRadius !== 'undefined') && serviceRadius >= 30 && serviceRadius < 45 ? 'opacity-100 scale-110' : 'opacity-30'" style="transition: all 0.3s;">🌆</span>
                    <span class="text-2xl" :class="(typeof serviceRadius !== 'undefined') && serviceRadius >= 45 ? 'opacity-100 scale-110' : 'opacity-30'" style="transition: all 0.3s;">🗺️</span>
                </div>

                <input
                    type="range"
                    id="serviceRadius"
                    :value="(typeof serviceRadius !== 'undefined') ? serviceRadius : 5"
                    @input="updateRadius(parseInt($event.target.value))"
                    min="0"
                    max="60"
                    step="1"
                    class="w-full h-3 bg-gradient-to-r from-emerald-200 via-teal-200 to-cyan-200 rounded-lg appearance-none cursor-pointer slider">

                {{-- Radius Labels --}}
                <div class="flex justify-between text-xs font-medium text-gray-600 mt-3">
                    <span class="flex flex-col items-center">
                        <span class="font-bold">0 km</span>
                        <span class="text-gray-400 text-xs">Start</span>
                    </span>
                    <span class="flex flex-col items-center">
                        <span class="font-bold">15 km</span>
                        <span class="text-gray-400 text-xs">Lokalny</span>
                    </span>
                    <span class="flex flex-col items-center">
                        <span class="font-bold">30 km</span>
                        <span class="text-gray-400 text-xs">Miejski</span>
                    </span>
                    <span class="flex flex-col items-center">
                        <span class="font-bold">45 km</span>
                        <span class="text-gray-400 text-xs">Rozszerzony</span>
                    </span>
                    <span class="flex flex-col items-center">
                        <span class="font-bold">60 km</span>
                        <span class="text-gray-400 text-xs">Max</span>
                    </span>
                </div>
            </div>

            @error('serviceRadius')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror

            {{-- Radius Impact Info --}}
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-3 border border-emerald-200">
                    <div class="text-xs text-emerald-700 mb-1">Szacowana liczba klientów:</div>
                    <div class="text-lg font-bold text-emerald-600">
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius < 10">200-500</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 10 && serviceRadius < 20">500-1,200</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 20 && serviceRadius < 40">1,200-3,000</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 40">3,000+</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 border border-blue-200">
                    <div class="text-xs text-blue-700 mb-1">Średni czas dojazdu:</div>
                    <div class="text-lg font-bold text-blue-600">
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius < 10">5-10 min</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 10 && serviceRadius < 20">10-20 min</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 20 && serviceRadius < 40">20-35 min</span>
                        <span x-show="(typeof serviceRadius !== 'undefined') && serviceRadius >= 40">35-50 min</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Area Map Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-1 flex items-center">
                            <span class="text-xl mr-2">🗺️</span>
                            Podgląd obszaru obsługi
                        </label>
                        <p class="text-xs text-gray-500">
                            Przeciągnij marker na mapie, aby ustawić dokładny adres
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500">
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span>Twoja lokalizacja</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map Container with Enhanced Border --}}
            <div class="relative rounded-xl overflow-hidden border-4 border-gradient shadow-xl"
                 style="border-image: linear-gradient(135deg, #10b981, #06b6d4) 1;">
                <div id="wizard-step5-map"
                     class="w-full h-full"
                     x-ref="mapContainer"
                     x-init="console.log('Map container init, currentStep:', $wire.currentStep); setTimeout(() => { console.log('Calling initializeMap'); initializeMap(); }, 200)"
                     wire:ignore
                     style="height: 400px; min-height: 400px;">
                </div>

                {{-- Map Loading Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/90 to-cyan-500/90 flex items-center justify-center"
                     x-show="!mapInitialized"
                     x-transition>
                    <div class="flex flex-col items-center gap-3 text-white">
                        <svg class="animate-spin h-12 w-12" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-semibold text-lg">Ładowanie mapy...</span>
                        <span class="text-sm text-emerald-50">Przygotowujemy podgląd Twojego obszaru</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Location Summary Card --}}
        <div x-show="(typeof isStepValid === 'function') && isStepValid()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             @address-updated.window="$nextTick(() => { console.log('📍 Address updated in summary box:', currentAddress) })"
             class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-200 shadow-lg p-4 sm:p-6">
            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center mr-3">
                    <span class="text-white text-lg">✓</span>
                </span>
                Twoja lokalizacja jest ustawiona
            </h4>
            <div class="space-y-3">
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-emerald-200">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">📍</span>
                        <div class="flex-1">
                            <div class="text-xs font-semibold text-gray-500 mb-1">Adres usługowy:</div>
                            <div class="text-sm text-gray-900 font-medium whitespace-pre-line" x-text="(typeof currentAddress !== 'undefined') ? currentAddress : ''"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-emerald-200">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">📏</span>
                        <div class="flex-1">
                            <div class="text-xs font-semibold text-gray-500 mb-1">Promień obsługi:</div>
                            <div class="text-sm font-bold" style="background: linear-gradient(135deg, #10b981, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                <span x-text="(typeof radiusLabel !== 'undefined') ? radiusLabel : '0 km'"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🎯</span>
                            <div>
                                <div class="text-xs font-semibold text-gray-500">Status:</div>
                                <div class="text-sm font-semibold text-emerald-600">Gotowe do publikacji!</div>
                            </div>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center animate-pulse">
                            <span class="text-white text-xl">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles for Range Slider --}}
<style>
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 28px;
    width: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #06b6d4);
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2), 0 0 0 4px rgba(16, 185, 129, 0.2);
    transition: all 0.2s ease;
}

.slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3), 0 0 0 6px rgba(16, 185, 129, 0.3);
}

.slider::-moz-range-thumb {
    height: 28px;
    width: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #06b6d4);
    cursor: pointer;
    border: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2), 0 0 0 4px rgba(16, 185, 129, 0.2);
    transition: all 0.2s ease;
}

.slider::-moz-range-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3), 0 0 0 6px rgba(16, 185, 129, 0.3);
}

.slider::-webkit-slider-track {
    background: linear-gradient(to right, #d1fae5, #a7f3d0, #5eead4, #67e8f9);
    border-radius: 8px;
    height: 12px;
}

.slider::-moz-range-track {
    background: linear-gradient(to right, #d1fae5, #a7f3d0, #5eead4, #67e8f9);
    border-radius: 8px;
    height: 12px;
}

/* Custom Map Marker Styles */
.custom-div-icon {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

/* Gradient border animation */
@keyframes gradient-border {
    0% { border-image-source: linear-gradient(135deg, #10b981, #06b6d4); }
    50% { border-image-source: linear-gradient(135deg, #06b6d4, #10b981); }
    100% { border-image-source: linear-gradient(135deg, #10b981, #06b6d4); }
}
</style>

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT + Enhanced UI v4

    Komponent wizardStep5 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-5-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    Enhanced UI v4:
    - ✅ Gradient header dla głównego display promienia
    - ✅ Ikony wzdłuż suwaka pokazujące zasięg
    - ✅ Dynamiczne karty ze statystykami (liczba klientów, czas dojazdu)
    - ✅ Ulepszona karta podsumowania z gradientami
    - ✅ Enhanced slider z gradientowym thumbem i trackiem
    - ✅ Karta wskazówek Quick Tips na górze
    - ✅ Animacje i transitions
    - ✅ Spójny design z krokami 7, 10, 11

    Import w app.js: import './components/wizard-step-5-v3';

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - currentAddress, serviceRadius, radiusLabel
    - currentSuggestions, selectedIndex, suggestionsVisible
    - updateRadius(), searchAddress(), selectAddress()
    - updateAddress(), handleKeydown(), hideSuggestions()
    - getAddressInputClasses(), getSuggestionClasses()
    - formatSuggestion(), isStepValid()
--}}
