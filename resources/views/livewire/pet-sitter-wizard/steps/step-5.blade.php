{{-- Krok 7: Opowiedz o swoim domu - Architektura v3.0 + UI v4 ENHANCED WIDE --}}
<div class="max-w-4xl mx-auto px-4" x-data="wizardStep5()" x-init="init()">

    {{-- Header z gradient --}}
    <div class="text-center mb-8">
        <div class="inline-block mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-3xl">🏠</span>
            </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Opowiedz o swoim domu</h1>
        <p class="text-gray-600 text-lg">Klienci chcą wiedzieć, w jakim środowisku będą przebywały ich zwierzęta</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Główna sekcja - Formularze (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Typ mieszkania Card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <span class="text-xl">🏘️</span>
                        </div>
                        <div>
                            <label class="block text-base font-bold text-gray-900">
                                Typ mieszkania <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">Wybierz jedną opcję</p>
                        </div>
                    </div>
                    <button type="button"
                            @click="$wire.showAIPanel = !$wire.showAIPanel"
                            class="flex items-center text-xs cursor-pointer hover:scale-105 transition-transform duration-200 px-3 py-2 rounded-lg bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 hover:border-emerald-300">
                        <div class="w-5 h-5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center mr-2 shadow-sm">
                            <span class="text-white text-xs">💡</span>
                        </div>
                        <span class="font-semibold text-emerald-700" x-text="$wire.showAIPanel ? 'Ukryj' : 'Wskazówki'"></span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $homeTypes = [
                            'apartment' => ['icon' => '🏢', 'title' => 'Mieszkanie', 'desc' => 'W budynku wielorodzinnym'],
                            'house' => ['icon' => '🏠', 'title' => 'Dom jednorodzinny', 'desc' => 'Wolnostojący lub w zabudowie'],
                            'studio' => ['icon' => '🏡', 'title' => 'Kawalerka/Studio', 'desc' => 'Mały metraż'],
                            'townhouse' => ['icon' => '🏘️', 'title' => 'Dom szeregowy', 'desc' => 'W zabudowie szeregowej']
                        ];
                    @endphp

                    @foreach($homeTypes as $key => $home)
                        <label class="wizard-checkbox-tile"
                               :class="{ 'selected': (typeof homeType !== 'undefined') && homeType === '{{ $key }}' }"
                               @click.prevent="(typeof selectHomeType === 'function') && selectHomeType('{{ $key }}')">
                            <input type="radio"
                                   name="homeType"
                                   value="{{ $key }}"
                                   class="sr-only">
                            <div class="flex items-center w-full">
                                <span class="wizard-checkbox-emoji">{{ $home['icon'] }}</span>
                                <div class="flex-1 min-w-0">
                                    <span class="wizard-checkbox-text">{{ $home['title'] }}</span>
                                    <div class="wizard-checkbox-description">{{ $home['desc'] }}</div>
                                </div>
                                <svg class="wizard-checkbox-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('homeType')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Cechy mieszkania Card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <span class="text-xl">✨</span>
                    </div>
                    <div>
                        <label class="block text-base font-bold text-gray-900">
                            Cechy Twojego domu
                        </label>
                        <p class="text-xs text-gray-500 mt-0.5">Zaznacz wszystkie które pasują</p>
                    </div>
                </div>

                <div class="space-y-3">
                    {{-- Ogród/balkon --}}
                    <label class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.01] hover:shadow-md"
                           :class="{
                               'border-emerald-500 bg-emerald-50': (typeof hasGarden !== 'undefined') && hasGarden,
                               'border-gray-200': !(typeof hasGarden !== 'undefined') || !hasGarden
                           }"
                           @click.prevent="(typeof toggleGarden === 'function') && toggleGarden()">
                        <input type="checkbox" class="sr-only">
                        <div class="flex items-center w-full">
                            <div class="relative mr-3">
                                <div x-show="(typeof hasGarden !== 'undefined') && hasGarden" class="w-5 h-5 bg-emerald-600 rounded border border-emerald-600 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div x-show="!(typeof hasGarden !== 'undefined') || !hasGarden" class="w-5 h-5 border-2 border-gray-300 rounded"></div>
                            </div>
                            <div class="flex-1 select-none">
                                <div class="font-medium text-gray-900 text-sm">Ogród lub duży balkon</div>
                                <div class="text-xs text-gray-500 mt-0.5">Bezpieczne miejsce do zabawy dla zwierząt</div>
                            </div>
                            <div class="text-2xl">🌱</div>
                        </div>
                    </label>

                    {{-- Niepalący --}}
                    <label class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.01] hover:shadow-md"
                           :class="{
                               'border-green-500 bg-green-50': (typeof isSmoking !== 'undefined') && !isSmoking,
                               'border-gray-200': !(typeof isSmoking !== 'undefined') || isSmoking
                           }"
                           @click.prevent="(typeof toggleSmoking === 'function') && toggleSmoking()">
                        <input type="checkbox" class="sr-only">
                        <div class="flex items-center w-full">
                            <div class="relative mr-3">
                                <div x-show="(typeof isSmoking !== 'undefined') && !isSmoking" class="w-5 h-5 bg-green-600 rounded border border-green-600 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div x-show="!(typeof isSmoking !== 'undefined') || isSmoking" class="w-5 h-5 border-2 border-gray-300 rounded"></div>
                            </div>
                            <div class="flex-1 select-none">
                                <div class="font-medium text-gray-900 text-sm">Środowisko bez dymu</div>
                                <div class="text-xs text-gray-500 mt-0.5">Nie palę w domu (ważne dla zdrowia zwierząt)</div>
                            </div>
                            <div class="text-2xl">🚭</div>
                        </div>
                    </label>
                </div>

                @error('hasGarden')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror

                @error('isSmoking')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Inne zwierzęta Card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <span class="text-xl">🐾</span>
                        </div>
                        <div>
                            <label class="text-base font-bold text-gray-900">
                                Czy masz inne zwierzęta?
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">Ważne dla kompatybilności</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               class="sr-only peer"
                               :checked="(typeof hasOtherPets !== 'undefined') && hasOtherPets"
                               @change="(typeof toggleOtherPets === 'function') && toggleOtherPets()">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div x-show="(typeof hasOtherPets !== 'undefined') && hasOtherPets"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="space-y-3">
                    <p class="text-sm text-gray-600 mb-3">Jakie zwierzęta masz w domu? (Pomoże to w doborze odpowiednich gości)</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($this->formattedPetTypes as $key => $pet)
                            <label class="wizard-checkbox-tile small"
                                   :class="{ 'selected': (typeof isPetSelected === 'function') && isPetSelected('{{ $key }}') }"
                                   @click.prevent="(typeof togglePet === 'function') && togglePet('{{ $key }}')">
                                <input type="checkbox" value="{{ $key }}" class="sr-only">
                                <div class="flex flex-col items-center text-center">
                                    <span class="text-2xl mb-1">{{ $pet['icon'] }}</span>
                                    <span class="text-xs font-medium">{{ $pet['title'] }}</span>
                                    <svg class="wizard-checkbox-icon absolute top-1 right-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                @error('otherPets')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- Sticky Sidebar - Live Preview (1/3) --}}
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-xl border-2 border-blue-200 p-6 sticky top-4">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">✅</span>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900">
                        Twój dom
                    </h4>
                </div>

                {{-- Placeholder gdy nic nie wybrano --}}
                <div x-show="!(typeof homeType !== 'undefined') || !homeType"
                     class="text-center py-8">
                    <div class="text-6xl mb-4 opacity-50">🏠</div>
                    <p class="text-sm text-gray-600">
                        Wybierz typ mieszkania, aby zobaczyć podgląd
                    </p>
                </div>

                {{-- Live preview gdy są dane --}}
                <div x-show="(typeof homeType !== 'undefined') && homeType"
                     x-transition
                     class="space-y-3">
                    {{-- Typ mieszkania --}}
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg">🏘️</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Typ</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900" x-text="(typeof getHomeTypeLabel === 'function') ? getHomeTypeLabel() : ''"></span>
                    </div>

                    {{-- Ogród/balkon --}}
                    <div x-show="(typeof hasGarden !== 'undefined') && hasGarden"
                         x-transition
                         class="bg-white rounded-xl p-4 shadow-sm border border-emerald-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg">🌱</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">Ogród/balkon dostępny</span>
                        </div>
                    </div>

                    {{-- Bez dymu --}}
                    <div x-show="(typeof isSmoking !== 'undefined') && !isSmoking"
                         x-transition
                         class="bg-white rounded-xl p-4 shadow-sm border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg">🚭</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">Środowisko bez dymu</span>
                        </div>
                    </div>

                    {{-- Inne zwierzęta --}}
                    <div x-show="(typeof hasOtherPets !== 'undefined') && hasOtherPets && (typeof otherPets !== 'undefined') && otherPets.length > 0"
                         x-transition
                         class="bg-white rounded-xl p-4 shadow-sm border border-amber-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg">🐾</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Inne zwierzęta</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900" x-text="(typeof otherPets !== 'undefined') ? `${otherPets.length} typ${otherPets.length > 1 ? 'y' : ''}` : ''"></span>
                    </div>

                    {{-- Wskazówka --}}
                    <div class="mt-6 pt-6 border-t border-blue-200">
                        <p class="text-xs text-gray-600 italic flex items-start gap-2">
                            <span class="text-base">💡</span>
                            <span>Szczegółowe informacje o domu zwiększają zaufanie klientów i szansę na rezerwację</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT + UI v4 WIDE LAYOUT

    Komponent wizardStep7 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-7-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    UI v4 WIDE:
    - ✅ max-w-4xl mx-auto px-4 wrapper (szerszy układ)
    - ✅ Grid 3 kolumny: 2/3 formularze + 1/3 sidebar
    - ✅ Sticky sidebar z live preview
    - ✅ bg-white rounded-2xl shadow-lg cards
    - ✅ Przycisk AI Panel w nagłówku

    Import w app.js: import './components/wizard-step-7-v3';

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - homeType, hasGarden, isSmoking, hasOtherPets, otherPets
    - selectHomeType(), toggleGarden(), toggleSmoking()
    - toggleOtherPets(), togglePet(), isPetSelected()
    - getHomeTypeLabel(), getHomeSummary()
--}}
