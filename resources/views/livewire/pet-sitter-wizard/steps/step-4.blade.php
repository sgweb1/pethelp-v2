{{-- Krok 6: Dostępność - Architektura v3.0 --}}
<div class="max-w-2xl mx-auto px-4" x-data="wizardStep4()" x-init="init(); console.log('🔍 Alpine x-init called for step 6'); console.log('🔍 Component methods:', { isDayEnabled: typeof isDayEnabled, getDayClasses: typeof getDayClasses, toggleHintsPanel: typeof toggleHintsPanel }); console.log('🔍 Window functions:', { wizardStep6: typeof window.wizardStep6 });">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Kiedy jesteś dostępny?</h1>
        <p class="text-gray-600 text-lg">Określ swój typowy harmonogram i preferencje czasowe</p>
    </div>

    <div class="space-y-6">
        {{-- Szybkie szablony harmonogramów --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-200 p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <span class="text-2xl mr-3">⚡</span>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Szybki wybór harmonogramu</h3>
                    <p class="text-xs text-gray-600">Kliknij aby zastosować gotowy szablon</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {{-- Pełny etat --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('full_time')"
                        class="group p-3 bg-white hover:bg-emerald-50 border-2 border-gray-200 hover:border-emerald-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">💼</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Pełny etat</div>
                    <div class="text-xs text-gray-500">Pn-Pt 9:00-17:00</div>
                </button>

                {{-- Part-time --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('part_time')"
                        class="group p-3 bg-white hover:bg-blue-50 border-2 border-gray-200 hover:border-blue-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">⏰</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Part-time</div>
                    <div class="text-xs text-gray-500">Pn,Śr,Pt 10:00-14:00</div>
                </button>

                {{-- Weekendy --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('weekends')"
                        class="group p-3 bg-white hover:bg-purple-50 border-2 border-gray-200 hover:border-purple-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">🎉</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Weekendy</div>
                    <div class="text-xs text-gray-500">Sb-Nd 10:00-16:00</div>
                </button>

                {{-- Poranny --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('morning')"
                        class="group p-3 bg-white hover:bg-amber-50 border-2 border-gray-200 hover:border-amber-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">🌅</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Poranny</div>
                    <div class="text-xs text-gray-500">Codziennie 7:00-12:00</div>
                </button>

                {{-- Wieczorny --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('evening')"
                        class="group p-3 bg-white hover:bg-orange-50 border-2 border-gray-200 hover:border-orange-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">🌆</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Wieczorny</div>
                    <div class="text-xs text-gray-500">Codziennie 15:00-20:00</div>
                </button>

                {{-- Nocny --}}
                <button type="button"
                        @click="(typeof applyScheduleTemplate === 'function') && applyScheduleTemplate('night')"
                        class="group p-3 bg-white hover:bg-indigo-50 border-2 border-gray-200 hover:border-indigo-500 rounded-xl transition-all hover:scale-[1.02]">
                    <div class="text-2xl mb-2">🌙</div>
                    <div class="text-xs font-semibold text-gray-900 mb-1">Nocny</div>
                    <div class="text-xs text-gray-500">Codziennie 20:00-08:00</div>
                </button>
            </div>

            <div class="mt-3 text-xs text-gray-600 bg-white/50 rounded-lg p-2 flex items-start">
                <span class="mr-2">💡</span>
                <span>Po wybraniu szablonu możesz dostosować godziny dla każdego dnia indywidualnie</span>
            </div>
        </div>

        {{-- Harmonogram tygodniowy Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <label class="block text-sm font-semibold text-gray-900">
                    Dostępność w tygodniu <span class="text-red-500">*</span>
                </label>
                <button type="button"
                        @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="flex items-center text-xs cursor-pointer hover:scale-105 transition-transform duration-200 px-3 py-1.5 rounded-lg hover:bg-emerald-50">
                    <div class="w-5 h-5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center mr-2">
                        <span class="text-white text-xs">💡</span>
                    </div>
                    <span class="font-medium text-gray-700" x-text="$wire.showAIPanel ? 'Ukryj wskazówki' : 'Wskazówki AI'"></span>
                </button>
            </div>

            <div class="space-y-3">
        @php
            $days = [
                'monday' => 'Poniedziałek',
                'tuesday' => 'Wtorek',
                'wednesday' => 'Środa',
                'thursday' => 'Czwartek',
                'friday' => 'Piątek',
                'saturday' => 'Sobota',
                'sunday' => 'Niedziela'
            ];
        @endphp

                @foreach($days as $key => $day)
                    <div class="flex items-center justify-between p-3 border-2 rounded-xl transition-all"
                         :class="(typeof getDayClasses === 'function') ? getDayClasses('{{ $key }}') : 'border-gray-200'">
                        <div class="flex items-center flex-1">
                            <label class="flex items-center cursor-pointer" @click="(typeof toggleDay === 'function') && toggleDay('{{ $key }}')">
                                <div class="relative">
                                    <div x-show="(typeof isDayEnabled === 'function') && isDayEnabled('{{ $key }}')"
                                         class="w-5 h-5 bg-emerald-600 rounded border border-emerald-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div x-show="!(typeof isDayEnabled === 'function') || !isDayEnabled('{{ $key }}')"
                                         class="w-5 h-5 border-2 border-gray-300 rounded"></div>
                                </div>
                                <span class="ml-3 font-medium text-gray-900 text-sm">{{ $day }}</span>
                            </label>
                        </div>

                        <div x-show="(typeof isDayEnabled === 'function') && isDayEnabled('{{ $key }}')"
                             x-transition
                             class="flex items-center space-x-2"
                             role="group"
                             aria-labelledby="time-range-{{ $key }}">
                            <label for="start-time-{{ $key }}" class="sr-only">Godzina rozpoczęcia dla {{ $day }}</label>
                            <input
                                id="start-time-{{ $key }}"
                                type="time"
                                :value="(typeof getDayTime === 'function') ? getDayTime('{{ $key }}', 'start') : ''"
                                @change="(typeof updateTime === 'function') && updateTime('{{ $key }}', 'start', $event.target.value)"
                                class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                aria-describedby="time-error-{{ $key }}"
                                aria-label="Godzina rozpoczęcia dla {{ $day }}"
                            >
                            <span class="text-gray-400 text-sm" aria-hidden="true">-</span>
                            <label for="end-time-{{ $key }}" class="sr-only">Godzina zakończenia dla {{ $day }}</label>
                            <input
                                id="end-time-{{ $key }}"
                                type="time"
                                :value="(typeof getDayTime === 'function') ? getDayTime('{{ $key }}', 'end') : ''"
                                @change="(typeof updateTime === 'function') && updateTime('{{ $key }}', 'end', $event.target.value)"
                                class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                aria-describedby="time-error-{{ $key }}"
                                aria-label="Godzina zakończenia dla {{ $day }}"
                            >

                            {{-- Error feedback dla walidacji --}}
                            <div id="time-error-{{ $key }}" class="text-red-600 text-xs ml-2" x-show="false" aria-live="polite">
                                <!-- Miejsce na błędy walidacji czasów -->
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('weeklyAvailability')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Dodatkowe opcje Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label class="block text-sm font-semibold text-gray-900 mb-4">
                Preferencje elastyczności
            </label>

            <div class="space-y-3">
                {{-- Elastyczny harmonogram --}}
                <div class="p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02]"
                     :class="(typeof getFlexibleClasses === 'function') ? getFlexibleClasses() : 'border-gray-200'"
                     @click="(typeof toggleFlexible === 'function') && toggleFlexible()">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-5 h-5 border-2 rounded mr-3 flex items-center justify-center"
                                 :class="{ 'bg-emerald-600 border-emerald-600': (typeof flexibleSchedule !== 'undefined') && flexibleSchedule, 'border-gray-300': (typeof flexibleSchedule === 'undefined') || !flexibleSchedule }">
                                <svg x-show="(typeof flexibleSchedule !== 'undefined') && flexibleSchedule" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Elastyczny harmonogram</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Mogę dostosować godziny do potrzeb klienta</p>
                            </div>
                        </div>
                        <span class="text-2xl">⏰</span>
                    </div>
                </div>

                {{-- Dostępność w nagłych przypadkach --}}
                <div class="p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02]"
                     :class="(typeof getEmergencyClasses === 'function') ? getEmergencyClasses() : 'border-gray-200'"
                     @click="(typeof toggleEmergency === 'function') && toggleEmergency()">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-5 h-5 border-2 rounded mr-3 flex items-center justify-center"
                                 :class="{ 'bg-emerald-600 border-emerald-600': (typeof emergencyAvailable !== 'undefined') && emergencyAvailable, 'border-gray-300': (typeof emergencyAvailable === 'undefined') || !emergencyAvailable }">
                                <svg x-show="(typeof emergencyAvailable !== 'undefined') && emergencyAvailable" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">Dostępny w nagłych przypadkach</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Mogę pomóc w sytuacjach awaryjnych</p>
                            </div>
                        </div>
                        <span class="text-2xl">🚨</span>
                    </div>
                </div>
            </div>

            @error('flexibleSchedule')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror

            @error('emergencyAvailable')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</div> {{-- Koniec głównego wrappera z max-w-2xl --}}

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT

    Komponent wizardStep6 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-6-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    Import w app.js: import './components/wizard-step-6-v3';

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - weeklyAvailability, flexibleSchedule, emergencyAvailable
    - toggleDay(), updateTime(), getDayTime()
    - isDayEnabled(), getDayClasses()
    - toggleFlexible(), toggleEmergency()
    - getFlexibleClasses(), getEmergencyClasses()
--}}