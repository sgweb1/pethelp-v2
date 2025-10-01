{{-- Krok 10: Cennik - Architektura v3.0 + UI v4 REDESIGNED --}}
<div class="max-w-4xl mx-auto px-4" x-data="wizardStep10()" x-init="init()"
     @notify.window="
         showNotification = true;
         notificationTitle = $event.detail.title;
         notificationMessage = $event.detail.message;
         notificationType = $event.detail.type;
         setTimeout(() => { showNotification = false; }, 4000);
     ">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Wybierz usługi i ustaw ceny</h1>
        <p class="text-gray-600 text-lg">Zaznacz które usługi chcesz oferować i określ swój cennik</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Główna sekcja z usługami (2/3 szerokości) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Lista usług z checkboxami --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-2">🛠️</span>
                        Twoje usługi
                    </h3>
                    <button type="button"
                            @click="(typeof setRecommendedPrices === 'function') && setRecommendedPrices()"
                            class="text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center">
                        <span class="mr-1">✨</span>
                        Ustaw sugerowane
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="([serviceKey, service], index) in ((typeof serviceDefinitions !== 'undefined') ? Object.entries(serviceDefinitions) : [])" :key="`service-${index}`">
                        <div class="border-2 rounded-xl p-4 transition-all"
                             :class="{
                                 'border-emerald-500 bg-emerald-50': (typeof isServiceSelected === 'function') && isServiceSelected(serviceKey),
                                 'border-gray-200 bg-white hover:border-gray-300': (typeof isServiceSelected !== 'function') || !isServiceSelected(serviceKey)
                             }">
                            <div class="flex items-start gap-4">
                                {{-- Checkbox --}}
                                <div class="flex-shrink-0 pt-1">
                                    <label class="cursor-pointer">
                                        <input type="checkbox"
                                               :checked="(typeof isServiceSelected === 'function') && isServiceSelected(serviceKey)"
                                               @change="(typeof toggleServiceSelection === 'function') && toggleServiceSelection(serviceKey)"
                                               class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                    </label>
                                </div>

                                {{-- Ikona i nazwa usługi --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-2xl" x-text="service.icon || '📋'"></span>
                                        <h4 class="font-semibold text-gray-900" x-text="service.title"></h4>
                                    </div>

                                    {{-- Sugerowana cena z analizy konkurencji --}}
                                    <p class="text-xs text-gray-500 mb-2">
                                        Średnia cena w okolicy:
                                        <span class="font-medium text-gray-700" x-text="(typeof getSuggestedPrice === 'function') ? getSuggestedPrice(serviceKey) : ''"></span>
                                        <span x-text="service.unit"></span>
                                    </p>

                                    {{-- Pole ceny --}}
                                    <div class="flex items-center gap-2"
                                         x-show="(typeof isServiceSelected === 'function') && isServiceSelected(serviceKey)"
                                         x-transition>
                                        <input type="number"
                                               :value="(typeof servicePricing !== 'undefined' && servicePricing[serviceKey]) ? servicePricing[serviceKey] : ''"
                                               @input.debounce.500ms="(typeof updateServicePrice === 'function') && updateServicePrice(serviceKey, $event.target.value)"
                                               :placeholder="(typeof getSuggestedPrice === 'function') ? getSuggestedPrice(serviceKey).toString() : ''"
                                               min="1"
                                               step="1"
                                               class="w-24 px-3 py-2 border-2 border-emerald-500 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <span class="text-sm text-gray-600 font-medium" x-text="service.unit"></span>

                                        {{-- Wskaźnik vs średnia --}}
                                        <span x-show="(typeof servicePricing !== 'undefined') && servicePricing[serviceKey] && (typeof getSuggestedPrice === 'function')"
                                              class="text-xs px-2 py-1 rounded-full font-medium"
                                              :class="{
                                                  'bg-amber-100 text-amber-700': servicePricing[serviceKey] < getSuggestedPrice(serviceKey),
                                                  'bg-emerald-100 text-emerald-700': servicePricing[serviceKey] > getSuggestedPrice(serviceKey),
                                                  'bg-blue-100 text-blue-700': servicePricing[serviceKey] == getSuggestedPrice(serviceKey)
                                              }"
                                              x-text="servicePricing[serviceKey] < getSuggestedPrice(serviceKey) ? '↓ Poniżej średniej' : (servicePricing[serviceKey] > getSuggestedPrice(serviceKey) ? '↑ Powyżej średniej' : '≈ Średnia')">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Strategia cenowa (opcjonalna sekcja) --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-xl mr-2">🎯</span>
                    Strategia cenowa (opcjonalnie)
                </h3>
                <div class="grid grid-cols-3 gap-3">
                    <template x-for="([key, strategy], index) in ((typeof pricingStrategies !== 'undefined') ? Object.entries(pricingStrategies) : [])" :key="`strategy-${index}`">
                        <label class="relative flex flex-col p-3 border-2 rounded-lg cursor-pointer transition-all hover:shadow-md"
                               :class="(typeof getStrategyCardClasses === 'function') ? getStrategyCardClasses(key) : ''"
                               @click="(typeof updatePricingStrategy === 'function') && updatePricingStrategy(key)">
                            <input type="radio" :value="key" class="sr-only">
                            <div class="text-center">
                                <div class="text-2xl mb-1" x-text="strategy.icon"></div>
                                <div class="text-xs font-medium text-gray-900 mb-1" x-text="strategy.title"></div>
                                <div class="text-xs text-gray-500" x-text="strategy.desc"></div>
                            </div>
                            <div class="absolute top-2 right-2">
                                <div x-show="(typeof isStrategySelected === 'function') && isStrategySelected(key)"
                                     x-transition
                                     class="w-4 h-4 bg-purple-600 rounded-full border-2 border-purple-600 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>
        </div>

        {{-- Sidebar z kalkulatorem (1/3 szerokości) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kalkulator zarobków --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl p-6 text-white sticky top-4">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <span class="text-2xl mr-2">💰</span>
                    Twoje zarobki
                </h3>

                {{-- Liczba aktywnych usług --}}
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 mb-4">
                    <div class="text-sm opacity-90 mb-1">Aktywne usługi</div>
                    <div class="text-3xl font-bold"
                         x-text="(typeof getActiveServicesCount === 'function') ? getActiveServicesCount() : 0"></div>
                </div>

                {{-- Miesięczne zarobki --}}
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 mb-4">
                    <div class="text-sm opacity-90 mb-1">Szacowane zarobki</div>
                    <div class="text-xs opacity-75 mb-2">(przy niskiej aktywności)</div>
                    <div class="text-3xl font-bold"
                         x-text="((typeof estimatedMonthlyEarnings !== 'undefined' && estimatedMonthlyEarnings.total) ? Math.round(estimatedMonthlyEarnings.total) : 0) + ' PLN'"></div>
                    <div class="text-xs opacity-75 mt-1">miesięcznie</div>
                </div>

                {{-- Szczegóły zarobków --}}
                <div x-show="(typeof estimatedMonthlyEarnings !== 'undefined') && estimatedMonthlyEarnings.hasEarnings"
                     class="space-y-2 pt-4 border-t border-white/20">
                    <div class="text-xs font-semibold opacity-90 mb-2">Szczegóły:</div>
                    <template x-for="[serviceKey, earning] in ((typeof estimatedMonthlyEarnings !== 'undefined' && estimatedMonthlyEarnings.earnings) ? Object.entries(estimatedMonthlyEarnings.earnings) : [])" :key="serviceKey">
                        <div class="flex justify-between items-center text-sm">
                            <span class="opacity-90" x-text="(typeof earning !== 'undefined' && earning.label) ? earning.label : ''"></span>
                            <span class="font-bold" x-text="(typeof earning !== 'undefined' && earning.amount) ? Math.round(earning.amount) + ' PLN' : '0 PLN'"></span>
                        </div>
                    </template>
                </div>

                {{-- Info --}}
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-xs opacity-75 italic">
                        💡 Rzeczywiste zarobki mogą być znacznie wyższe przy większej liczbie zleceń!
                    </p>
                </div>
            </div>

            {{-- Quick tip --}}
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-2">
                    <span class="text-xl">💡</span>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 mb-1">Wskazówka</h4>
                        <p class="text-xs text-blue-700">
                            Więcej usług = więcej możliwości zarobku. Dodaj przynajmniej 3-4 usługi, aby zmaksymalizować przychody.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Zwiększ swoje zarobki - rekomendacje niewybranych usług --}}
    <div x-show="(typeof getSuggestedServices === 'function') && getSuggestedServices().length > 0"
         class="mt-8 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl shadow-lg p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                <span class="text-2xl">🚀</span>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Zwiększ swoje zarobki!</h3>
                <p class="text-sm text-gray-600">Dodaj poniższe usługi, aby zarabiać więcej</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="(suggestion, index) in ((typeof getSuggestedServices === 'function') ? getSuggestedServices() : [])" :key="`suggestion-${index}`">
                <div class="bg-white border-2 border-amber-300 rounded-xl p-4 hover:shadow-lg transition-all cursor-pointer group"
                     @click="(typeof quickAddService === 'function') && quickAddService(suggestion.serviceKey)">
                    <div class="flex items-start justify-between mb-2">
                        <div class="font-semibold text-gray-900 text-sm" x-text="suggestion.label"></div>
                        <button class="text-emerald-600 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 mb-2">
                        <span x-show="suggestion.details?.hours" x-text="(suggestion.details?.hours || 0) + ' h/miesiąc'"></span>
                        <span x-show="suggestion.details?.nights" x-text="(suggestion.details?.nights || 0) + ' nocy/miesiąc'"></span>
                        <span x-show="suggestion.details?.trips" x-text="(suggestion.details?.trips || 0) + ' wyjazdów'"></span>
                        <span x-show="suggestion.details?.visits" x-text="(suggestion.details?.visits || 0) + ' wizyty'"></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-gray-600">Potencjał:</span>
                        <span class="text-lg font-bold text-emerald-600" x-text="'+' + Math.round(suggestion.potentialEarning) + ' PLN'"></span>
                    </div>
                </div>
            </template>
        </div>

        <div class="mt-4 text-center">
            <p class="text-xs text-gray-600 italic">
                Kliknij na usługę, aby ją dodać i automatycznie ustawić sugerowaną cenę
            </p>
        </div>
    </div>

    {{-- Błędy walidacji --}}
    @error('servicePricing')
        <p class="mt-4 text-sm text-red-600 flex items-center">
            <span data-svg-icon="exclamation" data-svg-size="16x16" data-svg-classes="text-red-600 mr-1"></span>
            {{ $message }}
        </p>
    @enderror

    {{-- Toast Notification --}}
    <div x-show="(typeof showNotification !== 'undefined') && showNotification"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-4 right-4 z-50 max-w-sm">
        <div class="bg-white rounded-lg shadow-2xl border-2 p-4"
             :class="{
                 'border-emerald-500': notificationType === 'success',
                 'border-blue-500': notificationType === 'info',
                 'border-amber-500': notificationType === 'warning',
                 'border-red-500': notificationType === 'error'
             }">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         :class="{
                             'bg-emerald-100': notificationType === 'success',
                             'bg-blue-100': notificationType === 'info',
                             'bg-amber-100': notificationType === 'warning',
                             'bg-red-100': notificationType === 'error'
                         }">
                        <span class="text-2xl"
                              x-text="notificationType === 'success' ? '✅' : (notificationType === 'info' ? 'ℹ️' : (notificationType === 'warning' ? '⚠️' : '❌'))"></span>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-bold text-gray-900" x-text="notificationTitle"></h3>
                    <p class="mt-1 text-sm text-gray-600" x-text="notificationMessage"></p>
                </div>
                <button @click="showNotification = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT - REDESIGNED

    Komponent wizardStep10 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-10-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    Import w app.js: import './components/wizard-step-10-v3';

    REDESIGN v4:
    - ✅ Interaktywne checkboxy dla wyboru usług
    - ✅ Live kalkulator zarobków w sidebarze
    - ✅ Wizualne wskaźniki cen vs średnia rynkowa
    - ✅ Sekcja rekomendacji niewybranych usług z potencjałem zarobkowym
    - ✅ Responsive 3-column layout (usługi + kalkulator + wskazówki)

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - pricingStrategy, servicePricing, pricingStrategies
    - serviceDefinitions, priceMultiplier, estimatedMonthlyEarnings
    - updatePricingStrategy(), updateServicePrice()
    - toggleServiceSelection() - NEW
    - quickAddService() - NEW
    - getActiveServicesCount() - NEW
    - setRecommendedPrices(), resetPricing()
    - getSuggestedPrice(), getServicePrice(), calculateAveragePrice()
    - getStrategyCardClasses(), isStrategySelected(), getStrategyInfo()
    - formatPriceWithUnit(), isPricingComplete(), getPricingStatusMessage()
--}}
