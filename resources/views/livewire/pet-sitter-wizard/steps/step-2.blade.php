{{-- Krok 2: Doświadczenie - V4 Design --}}
<div x-data="{
    petExperience: @js($petExperience),
    yearsOfExperience: '{{ $yearsOfExperience }}',
    characterCount: {{ strlen($experienceDescription) }},
    get isDescriptionValid() { return this.characterCount >= 100; },
    get progressPercentage() { return Math.min((this.characterCount / 100) * 100, 100); }
}" x-init="console.log('Step 2 Alpine initialized:', {petExperience, yearsOfExperience, characterCount})">

    {{-- Hero Section z gradientem --}}
    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);" class="text-white px-4 py-8 sm:py-12 mb-6">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="text-5xl sm:text-6xl">⭐</div>
                <button @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="hidden lg:flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl transition-all">
                    <span class="text-2xl">🤖</span>
                    <span class="text-sm font-semibold" x-text="$wire.showAIPanel ? 'Zamknij panel' : 'Otwórz AI Assistant'"></span>
                </button>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 leading-tight">
                Jakie masz doświadczenie z zwierzętami?
            </h1>
            <p class="text-emerald-50 text-sm sm:text-base">
                Pomóż nam lepiej zrozumieć Twoje umiejętności i wiedzę
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
                            Pomożemy Ci opisać doświadczenie
                        </p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-4 sm:p-6 space-y-4">
                {{-- Wskazówki --}}
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600 font-bold text-sm">
                            1
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Rodzaje doświadczenia</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Wybierz wszystkie pasujące typy</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 text-purple-600 font-bold text-sm">
                            2
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Lata praktyki</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Podaj realną liczbę lat</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600 font-bold text-sm">
                            3
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">Szczegółowy opis</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Minimum 100 znaków</p>
                        </div>
                    </div>
                </div>

                {{-- Link do pełnego panelu --}}
                <button @click="$wire.showAIPanel = true"
                        type="button"
                        class="w-full text-sm text-emerald-600 hover:text-emerald-700 font-medium py-2 flex items-center justify-center space-x-1">
                    <span>📖 Zobacz więcej wskazówek</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        {{-- Pet Experience Types Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label class="font-semibold text-gray-900 text-sm sm:text-base mb-4 block">
                Rodzaje doświadczenia <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach([
                    'own_pets' => ['Własne zwierzęta', '🏠'],
                    'family_pets' => ['Zwierzęta rodziny/przyjaciół', '👨‍👩‍👧‍👦'],
                    'volunteering' => ['Wolontariat w schronisku', '❤️'],
                    'professional' => ['Praca zawodowa', '💼'],
                    'training' => ['Kursy/szkolenia', '📚'],
                    'veterinary' => ['Doświadczenie weterynaryjne', '⚕️'],
                ] as $value => $info)
                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02] @if(in_array($value, $petExperience)) border-emerald-500 bg-white text-gray-900 @else bg-white border-gray-200 hover:border-gray-300 text-gray-900 @endif"
                           wire:click.prevent="togglePetExperience('{{ $value }}')">
                        <input type="checkbox"
                               value="{{ $value }}"
                               class="sr-only">
                        <span class="text-2xl mr-3">{{ $info[1] }}</span>
                        <span class="flex-1 font-medium text-sm">{{ $info[0] }}</span>
                        @if(in_array($value, $petExperience))
                            <span class="text-emerald-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('petExperience')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Years of Experience Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label for="yearsOfExperience" class="font-semibold text-gray-900 text-sm sm:text-base mb-3 block">
                Ile lat doświadczenia masz z zwierzętami? <span class="text-red-500">*</span>
            </label>
            <select wire:model="yearsOfExperience"
                    @change="yearsOfExperience = $el.value"
                    id="yearsOfExperience"
                    :class="yearsOfExperience ? 'border-emerald-500' : 'border-gray-200 hover:border-gray-300'"
                    class="w-full px-4 py-4 text-sm sm:text-base font-medium border-2 rounded-xl bg-white text-gray-900 focus:outline-none transition-all cursor-pointer">
                <option value="" disabled selected class="text-gray-400">Wybierz zakres...</option>
                <option value="less_than_1" class="text-gray-900 py-2">Mniej niż 1 rok</option>
                <option value="1_to_3" class="text-gray-900 py-2">1-3 lata</option>
                <option value="3_to_5" class="text-gray-900 py-2">3-5 lat</option>
                <option value="5_to_10" class="text-gray-900 py-2">5-10 lat</option>
                <option value="more_than_10" class="text-gray-900 py-2">Więcej niż 10 lat</option>
            </select>
            @error('yearsOfExperience')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Experience Description Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="wizard-form-group">
                <div class="flex items-center justify-between mb-3">
                    <label for="experienceDescription" class="font-semibold text-gray-900 text-sm sm:text-base mb-0">
                        Opisz swoje doświadczenie <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs sm:text-sm transition-colors"
                          :class="isDescriptionValid ? 'text-emerald-600 font-semibold' : characterCount > 1000 ? 'text-red-600' : 'text-gray-400'"
                          x-text="`${characterCount}/1000`"></span>
                </div>

                <div class="relative">
                    <textarea
                        wire:model.blur="experienceDescription"
                        @input="characterCount = $el.value.length"
                        id="experienceDescription"
                        rows="6"
                        @focus="$el.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500')"
                        @blur="$el.classList.remove('ring-2', 'ring-emerald-500', 'border-emerald-500')"
                        :class="isDescriptionValid ? 'border-emerald-500' : ''"
                        class="w-full px-4 py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl resize-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="Np. Mam 5 lat doświadczenia w opiece nad psami różnych ras. Pracowałem w schronisku dla zwierząt, gdzie zdobyłem doświadczenie w karmieniu, spacerach oraz podstawowej opiece medycznej. Dodatkowo ukończyłem kurs behawiorysty psów..."
                        maxlength="1000"></textarea>

                    {{-- Character Counter with Animation --}}
                    <div class="absolute bottom-3 right-3 flex items-center space-x-2">
                        <span x-show="isDescriptionValid"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 scale-95"
                              x-transition:enter-end="opacity-100 scale-100"
                              class="text-emerald-600 text-sm">
                            ✓
                        </span>
                    </div>
                </div>

                @error('experienceDescription')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <span class="mr-1">⚠️</span>
                        {{ $message }}
                    </p>
                @enderror

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             :style="isDescriptionValid ? `background: linear-gradient(135deg, #10b981, #06b6d4); width: ${progressPercentage}%` : `background: ${characterCount > 0 ? '#d1d5db' : '#e5e7eb'}; width: ${progressPercentage}%`"></div>
                    </div>
                    <p class="text-xs sm:text-sm mt-2" :class="isDescriptionValid ? 'text-emerald-600' : 'text-gray-500'">
                        <span x-show="!isDescriptionValid && characterCount < 100">
                            Minimum 100 znaków (jeszcze <span x-text="100 - characterCount"></span>)
                        </span>
                        <span x-show="isDescriptionValid">✓ Świetnie! Twój opis spełnia wymagania</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Wskazówki pisania --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center text-sm sm:text-base">
                <span class="text-xl sm:text-2xl mr-2">💡</span>
                Co warto zawrzeć w opisie?
            </h3>

            <div class="space-y-3">
                <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <span class="text-lg sm:text-xl flex-shrink-0">🐕</span>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-xs sm:text-sm mb-1">Zwierzęta</h4>
                        <p class="text-xs text-gray-600">Z jakimi zwierzętami pracowałeś</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-xl border border-purple-100">
                    <span class="text-lg sm:text-xl flex-shrink-0">🎓</span>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-xs sm:text-sm mb-1">Szkolenia</h4>
                        <p class="text-xs text-gray-600">Ukończone kursy lub certyfikaty</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                    <span class="text-lg sm:text-xl flex-shrink-0">💪</span>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-xs sm:text-sm mb-1">Umiejętności</h4>
                        <p class="text-xs text-gray-600">Jakie trudne sytuacje rozwiązywałeś</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Experience Summary --}}
        <div x-show="petExperience.length > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl p-4 sm:p-6">
            <h4 class="font-bold text-emerald-900 mb-3 flex items-center">
                <span class="text-xl mr-2">✨</span>
                Twoje doświadczenie
            </h4>
            <div class="text-emerald-800 space-y-2 text-sm">
                <div class="flex items-center">
                    <span class="text-emerald-600 mr-2">✓</span>
                    <span x-text="`${petExperience.length} ${petExperience.length === 1 ? 'typ' : petExperience.length < 5 ? 'typy' : 'typów'} doświadczenia`"></span>
                </div>
                <div x-show="yearsOfExperience" class="flex items-center">
                    <span class="text-emerald-600 mr-2">✓</span>
                    <span>Lata doświadczenia określone</span>
                </div>
                <div x-show="isDescriptionValid" class="flex items-center">
                    <span class="text-emerald-600 mr-2">✓</span>
                    <span>Szczegółowy opis doświadczenia</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{--
    ✅ V4 DESIGN - MOBILE FIRST

    Zachowana cała logika z wizardStep2():
    - petExperience, yearsOfExperience, experienceDescription
    - characterCount, isDescriptionValid, progressPercentage
    - togglePetExperience(), isPetExperienceSelected()
    - updateYearsOfExperience(), updateExperienceDescription()
    - syncFromLivewire()
    - Wszystkie bindingi Alpine.js

    Nowy wygląd:
    - Gradient hero section (⭐)
    - Białe karty z rounded-2xl i shadow-lg
    - AI Assistant card inline
    - Checkbox tiles z hover effects
    - Mobile-first responsive
    - Progress bar i character counter
    - Experience summary z animacją
--}}
