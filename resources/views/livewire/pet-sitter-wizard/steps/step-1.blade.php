{{-- Krok 1: Motywacja - V4 Design --}}
<div x-data="wizardStep1()" x-init="init()"
     @ai-suggestion-applied.window="syncFromLivewire(); console.log('🎯 AI suggestion applied, syncing...', $event.detail)">

    {{-- Hero Section z gradientem --}}
    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);" class="text-white px-4 py-8 sm:py-12 mb-6">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="text-5xl sm:text-6xl">👋</div>
                <button @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="hidden lg:flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl transition-all">
                    <span class="text-2xl">🤖</span>
                    <span class="text-sm font-semibold" x-text="$wire.showAIPanel ? 'Zamknij panel' : 'Otwórz AI Assistant'"></span>
                </button>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 leading-tight">
                Cześć! Dlaczego chcesz zostać pet sitterem?
            </h1>
            <p class="text-emerald-50 text-sm sm:text-base">
                Poznajmy Cię lepiej - opowiedz nam o swojej motywacji
            </p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 space-y-6 pb-8">
        {{-- AI Assistant Card (Inline Introduction) --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            {{-- Header z gradientem --}}
            <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);" class="p-4 sm:p-6 text-white">
                <div class="flex items-start space-x-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 text-2xl">
                        🤖
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold mb-1">AI Assistant</h2>
                        <p class="text-emerald-50 text-xs sm:text-sm">
                            Pomożemy Ci stworzyć przekonujący opis
                        </p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-4 sm:p-6 space-y-4">
                {{-- Wskazówki --}}
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600 font-bold text-sm">
                            1
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Proces rejestracji</h4>
                            <p class="text-xs sm:text-sm text-gray-600">12 prostych kroków, każdy zajmie tylko chwilę</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0 text-teal-600 font-bold text-sm">
                            2
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Szacowany czas</h4>
                            <p class="text-xs sm:text-sm text-gray-600">15-20 minut na ukończenie całej rejestracji</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-cyan-100 flex items-center justify-center flex-shrink-0 text-cyan-600 font-bold text-sm">
                            3
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Co będzie potrzebne</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Zdjęcie profilowe, podstawowe dane i opis doświadczenia</p>
                        </div>
                    </div>
                </div>

                {{-- AI Generate Button --}}
                <button
                    type="button"
                    @click="$wire.generateMotivationWithAI()"
                    class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-semibold py-3 px-6 rounded-xl transition-all transform hover:scale-105 flex items-center justify-center space-x-2 text-sm sm:text-base">
                    <span>✨ Wygeneruj opis z AI</span>
                </button>

                {{-- Link do pełnego panelu --}}
                <button @click="$wire.showAIPanel = true"
                        type="button"
                        class="w-full text-sm text-emerald-600 hover:text-emerald-700 font-medium py-2 flex items-center justify-center space-x-1">
                    <span>📖 Zobacz więcej wskazówek</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="wizard-form-group">
                <div class="flex items-center justify-between mb-3">
                    <label for="motivation" class="font-semibold text-gray-900 text-sm sm:text-base mb-0">
                        Twoja motywacja <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs sm:text-sm transition-colors"
                          :class="isValid ? 'text-emerald-600 font-semibold' : characterCount > 500 ? 'text-red-600' : 'text-gray-400'"
                          x-text="`${characterCount}/500`"></span>
                </div>

                <div class="relative">
                    <textarea
                        :value="motivation"
                        @input.debounce.500ms="updateMotivation($event.target.value)"
                        id="motivation"
                        rows="6"
                        @focus="$el.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500')"
                        @blur="$el.classList.remove('ring-2', 'ring-emerald-500', 'border-emerald-500')"
                        :class="isValid ? 'border-emerald-500' : ''"
                        class="w-full px-4 py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl resize-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="Np. Kocham zwierzęta od dziecka i chciałabym pomóc właścicielom, którzy potrzebują wsparcia w opiece nad swoimi pupilami..."
                        maxlength="500"></textarea>

                    {{-- Character Counter with Animation --}}
                    <div class="absolute bottom-3 right-3 flex items-center space-x-2">
                        <span x-show="isValid"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 scale-95"
                              x-transition:enter-end="opacity-100 scale-100"
                              class="text-emerald-600">
                            <span x-html="window.SafeSVGIcons?.checkMark || '✓'" class="text-emerald-600 text-sm"></span>
                        </span>
                    </div>
                </div>

                @error('motivation')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span x-html="window.SafeSVGIcons?.exclamation || '!'" class="w-4 h-4 mr-1 flex-shrink-0 text-red-600"></span>
                        {{ $message }}
                    </p>
                @enderror

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             :style="isValid ? `background: linear-gradient(135deg, #10b981, #06b6d4); width: ${progressPercentage}%` : `background: ${characterCount > 0 ? '#d1d5db' : '#e5e7eb'}; width: ${progressPercentage}%`"></div>
                    </div>
                    <p class="text-xs sm:text-sm mt-2" :class="isValid ? 'text-emerald-600' : 'text-gray-500'">
                        <span x-show="!isValid && characterCount < 100">
                            Minimum 100 znaków (jeszcze <span x-text="100 - characterCount"></span>)
                        </span>
                        <span x-show="isValid">✓ Świetnie! Twój opis spełnia wymagania</span>
                    </p>
                </div>
            </div>
        </div>


        {{-- Przykłady inspirujące --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center text-sm sm:text-base">
                <span class="text-xl sm:text-2xl mr-2">⭐</span>
                Przykłady inspirujących opisów
            </h3>

            <div class="space-y-3">
                {{-- Przykład 1 --}}
                <button
                    type="button"
                    @click="updateMotivation('Od dziecka otaczałam się zwierzętami i doskonale rozumiem ich potrzeby. Chciałabym pomagać właścicielom, którzy z różnych powodów nie mogą zapewnić opieki swoim pupilom, jednocześnie rozwijając swoją pasję.')"
                    class="w-full text-left p-4 bg-gradient-to-br from-emerald-50 to-teal-50 hover:from-emerald-100 hover:to-teal-100 border-2 border-emerald-200 rounded-xl transition-all transform hover:scale-[1.02] group">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm group-hover:scale-110 transition-transform">
                            1
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-700 line-clamp-2">
                                "Od dziecka otaczałam się zwierzętami i doskonale rozumiem ich potrzeby..."
                            </p>
                            <span class="text-xs text-emerald-600 font-medium mt-1 inline-block">👆 Kliknij aby użyć</span>
                        </div>
                    </div>
                </button>

                {{-- Przykład 2 --}}
                <button
                    type="button"
                    @click="updateMotivation('Mam doświadczenie w pracy z różnymi rasami psów i kotów. Widzę, jak ważna jest odpowiednia opieka dla dobrostanu zwierząt, dlatego chcę oferować profesjonalne usługi pet sittingu w mojej okolicy.')"
                    class="w-full text-left p-4 bg-gradient-to-br from-blue-50 to-cyan-50 hover:from-blue-100 hover:to-cyan-100 border-2 border-blue-200 rounded-xl transition-all transform hover:scale-[1.02] group">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm group-hover:scale-110 transition-transform">
                            2
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-700 line-clamp-2">
                                "Mam doświadczenie w pracy z różnymi rasami psów i kotów. Widzę jak ważna jest..."
                            </p>
                            <span class="text-xs text-blue-600 font-medium mt-1 inline-block">👆 Kliknij aby użyć</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

{{--
    ✅ V4 DESIGN - MOBILE FIRST

    Zachowana cała logika z wizardStep1():
    - motivation, characterCount, isValid, progress
    - updateMotivation(), syncFromLivewire()
    - Wire calls: $wire.generateMotivationWithAI(), $wire.showAIPanel
    - Wszystkie bindingi Alpine.js (@click, x-text, :class, x-show, etc.)

    Nowy wygląd:
    - Gradient hero section
    - Białe karty z rounded-2xl i shadow-lg
    - AI Assistant card inline
    - Mobile-first responsive (text-xs sm:text-sm)
    - Większe pady i spacing
    - Hover effects i animations
--}}
