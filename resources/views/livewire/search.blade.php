<div class="min-h-screen bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <!-- Hero-style Header -->
    <div class="relative py-8 lg:py-12">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="searchpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#searchpattern)"/>
            </svg>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4">
            <!-- Breadcrumb with hero styling -->
            <nav class="text-sm text-white/80 mb-6">
                <a href="/" class="hover:text-white transition-colors">Strona główna</a>
                <span class="mx-2">•</span>
                <span class="text-white font-medium">Wyszukiwanie opiekunów</span>
            </nav>

            <!-- Category Selection Hero -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                    Znajdź <span class="text-yellow-300">co potrzebujesz</span>
                </h1>
                <p class="text-lg text-white/90 max-w-2xl mx-auto">
                    Wybierz kategorię i znajdź najlepsze opcje w Twojej okolicy
                </p>
            </div>

            <!-- Interactive Category Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Pet Sitters -->
                <div
                    wire:click="selectCategory('pet_sitter')"
                    class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl"
                    :class="{ 'bg-yellow-400/20 border-yellow-400/40': '{{ $contentType }}' === 'pet_sitter' }"
                >
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🐕‍🦺</div>
                    <h3 class="text-white font-semibold mb-2">Pet Sitters</h3>
                    <p class="text-white/70 text-sm">Opiekunowie zwierząt</p>
                    <div class="mt-3 text-xs text-yellow-300 font-medium">
                        {{ $this->getCategoryCount('pet_sitter') }} dostępnych
                    </div>
                </div>

                <!-- Professional Services -->
                <div
                    wire:click="selectCategory('service')"
                    class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl"
                    :class="{ 'bg-yellow-400/20 border-yellow-400/40': '{{ $contentType }}' === 'service' }"
                >
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🔧</div>
                    <h3 class="text-white font-semibold mb-2">Usługi</h3>
                    <p class="text-white/70 text-sm">Profesjonalne usługi</p>
                    <div class="mt-3 text-xs text-yellow-300 font-medium">
                        {{ $this->getCategoryCount('service') }} dostępnych
                    </div>
                </div>

                <!-- Events -->
                <div
                    wire:click="selectCategory('event_public')"
                    class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl"
                    :class="{ 'bg-yellow-400/20 border-yellow-400/40': '{{ $contentType }}' === 'event_public' }"
                >
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎉</div>
                    <h3 class="text-white font-semibold mb-2">Wydarzenia</h3>
                    <p class="text-white/70 text-sm">Spotkania i eventy</p>
                    <div class="mt-3 text-xs text-yellow-300 font-medium">
                        {{ $this->getCategoryCount('event_public') }} dostępnych
                    </div>
                </div>

                <!-- Advertisements -->
                <div
                    wire:click="selectCategory('advertisement')"
                    class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl"
                    :class="{ 'bg-yellow-400/20 border-yellow-400/40': '{{ $contentType }}' === 'advertisement' }"
                >
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">📢</div>
                    <h3 class="text-white font-semibold mb-2">Ogłoszenia</h3>
                    <p class="text-white/70 text-sm">Sprzedaż i adopcja</p>
                    <div class="mt-3 text-xs text-yellow-300 font-medium">
                        {{ $this->getCategoryCount('advertisement') }} dostępnych
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Main Three Column Container -->
    <div class="bg-white dark:bg-gray-900 rounded-t-3xl relative z-20 -mt-8 min-h-screen">
        <!-- Container Inner Content -->
        <div class="pt-8 pb-8 min-h-screen relative">
            <!-- Three Column Layout -->
            <div class="w-full px-4">
                <div class="flex gap-6">

                    <!-- Left Column: Hero-Inspired Pet Sitter Search -->
                    <div class="hidden lg:block w-80 flex-shrink-0">
                        <div class="w-80 space-y-6 max-h-[calc(100vh-6rem)] z-30">



                            <!-- Pet Type Quick Selector -->
                            <div class="glass-card rounded-2xl border border-white/20 backdrop-blur-md bg-white/95 dark:bg-gray-800/95 shadow-xl p-5">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Typ zwierzęcia
                                </h4>
                                <div x-data="{
                                    showAllPetTypes: false,
                                    originalPetTypes: [
                                        { value: 'dog', icon: '🐕', label: 'Psy', gradient: 'from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20', border: 'border-orange-200 dark:border-orange-700', ring: 'ring-orange-400' },
                                        { value: 'cat', icon: '🐱', label: 'Koty', gradient: 'from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20', border: 'border-purple-200 dark:border-purple-700', ring: 'ring-purple-400' },
                                        { value: 'bird', icon: '🐦', label: 'Ptaki', gradient: 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20', border: 'border-blue-200 dark:border-blue-700', ring: 'ring-blue-400' },
                                        { value: 'other', icon: '🐾', label: 'Inne', gradient: 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20', border: 'border-green-200 dark:border-green-700', ring: 'ring-green-400' },
                                        { value: 'rabbit', icon: '🐰', label: 'Króliki', gradient: 'from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20', border: 'border-pink-200 dark:border-pink-700', ring: 'ring-pink-400' }
                                    ],
                                    get petTypes() {
                                        const selected = '{{ $petType ?? '' }}';
                                        if (!selected) return this.originalPetTypes;

                                        const selectedItem = this.originalPetTypes.find(item => item.value === selected);
                                        const otherItems = this.originalPetTypes.filter(item => item.value !== selected);

                                        return selectedItem ? [selectedItem, ...otherItems] : this.originalPetTypes;
                                    },
                                    selectPetType(value) {
                                        $wire.selectPetType(value);
                                        this.showAllPetTypes = false;
                                    }
                                }" class="mb-4">
                                    <!-- Visible pet types (first 2 or selected + 1) -->
                                    <div class="space-y-2">
                                        <template x-for="(petType, index) in (showAllPetTypes ? petTypes : petTypes.slice(0, 2))" :key="petType.value">
                                            <button
                                                @click="selectPetType(petType.value)"
                                                class="w-full flex items-center gap-3 p-3 rounded-xl transition-all duration-200 hover:scale-105 hover:shadow-md"
                                                :class="[
                                                    'bg-gradient-to-r ' + petType.gradient,
                                                    'border ' + petType.border,
                                                    '{{ $petType ?? '' }}' === petType.value ? 'ring-2 ' + petType.ring : ''
                                                ]"
                                            >
                                                <span class="text-xl" x-text="petType.icon"></span>
                                                <span class="font-medium text-sm text-gray-700 dark:text-gray-300" x-text="petType.label"></span>
                                                <span x-show="'{{ $petType ?? '' }}' === petType.value" class="ml-auto text-green-500">✓</span>
                                            </button>
                                        </template>

                                        <!-- Show more/less button -->
                                        <button
                                            @click="showAllPetTypes = !showAllPetTypes"
                                            class="w-full flex items-center justify-center gap-2 p-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        >
                                            <span x-text="showAllPetTypes ? 'Mniej' : 'Więcej'"></span>
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showAllPetTypes }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Care Type Selection -->
                                <div class="space-y-3 mb-4">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1 -18 0 9 9 0 0 1 18 0z"></path>
                                        </svg>
                                        Typ opieki
                                    </h5>
                                    <div x-data="{
                                        showAllCareTypes: false,
                                        selectCareType(id) {
                                            $wire.setCareType(id);
                                            this.showAllCareTypes = false;
                                        }
                                    }" class="space-y-2">
                                        <!-- Visible care types (first 3) -->
                                        @php
                                            $visibleCareTypes = $this->careTypes->take(3);
                                            $hiddenCareTypes = $this->careTypes->skip(3);
                                        @endphp

                                        @foreach($visibleCareTypes as $careType)
                                            <button
                                                @click="selectCareType('{{ $careType->id }}')"
                                                wire:key="care-type-{{ $careType->id }}"
                                                class="w-full flex items-center gap-3 p-3 border-2 rounded-xl transition-all duration-200 hover:scale-105 hover:shadow-md"
                                                :class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': '{{ $filters['category_id'] ?? '' }}' === '{{ $careType->id }}', 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500': '{{ $filters['category_id'] ?? '' }}' !== '{{ $careType->id }}' }"
                                            >
                                                <span class="text-lg">{{ $careType->icon }}</span>
                                                <div class="flex-1 text-left">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $careType->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $careType->description }}</div>
                                                </div>
                                                @if($filters['category_id'] ?? '' == $careType->id)
                                                    <span class="text-green-500">✓</span>
                                                @endif
                                            </button>
                                        @endforeach

                                        <!-- Hidden care types (show when expanded) -->
                                        <div x-show="showAllCareTypes" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                                            @foreach($hiddenCareTypes as $careType)
                                                <button
                                                    @click="selectCareType('{{ $careType->id }}')"
                                                    wire:key="care-type-{{ $careType->id }}"
                                                    class="w-full flex items-center gap-3 p-3 border-2 rounded-xl transition-all duration-200 hover:scale-105 hover:shadow-md"
                                                    :class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': '{{ $filters['category_id'] ?? '' }}' === '{{ $careType->id }}', 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500': '{{ $filters['category_id'] ?? '' }}' !== '{{ $careType->id }}' }"
                                                >
                                                    <span class="text-lg">{{ $careType->icon }}</span>
                                                    <div class="flex-1 text-left">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $careType->name }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $careType->description }}</div>
                                                    </div>
                                                    @if($filters['category_id'] ?? '' == $careType->id)
                                                        <span class="text-green-500">✓</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>

                                        <!-- Show more/less button (only if there are hidden types) -->
                                        @if($hiddenCareTypes->count() > 0)
                                            <button
                                                @click="showAllCareTypes = !showAllCareTypes"
                                                class="w-full flex items-center justify-center gap-2 p-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                <span x-text="showAllCareTypes ? 'Mniej' : 'Więcej ({{ $hiddenCareTypes->count() }})'"></span>
                                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showAllCareTypes }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Location Search -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Lokalizacja
                                    </h5>
                                    <x-address-search
                                        wireModel="filters.location"
                                        placeholder="Wpisz miasto lub dzielnicę..."
                                        label=""
                                        class="w-full"
                                        :showCurrentLocation="true"
                                    />
                                </div>

                                <!-- Advanced Filters Section -->
                                <div class="space-y-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                        </svg>
                                        Dodatkowe filtry
                                    </h5>

                                    <!-- Pet Size -->
                                    <div class="space-y-2">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Rozmiar zwierzęcia</label>
                                        <select wire:model.live="pet_size" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Wszystkie rozmiary</option>
                                            <option value="small">🐭 Małe (do 10kg)</option>
                                            <option value="medium">🐕 Średnie (10-25kg)</option>
                                            <option value="large">🐺 Duże (25kg+)</option>
                                        </select>
                                    </div>

                                    <!-- Price Range -->
                                    <div class="space-y-2">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Cena (zł)</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input wire:model.live.debounce.500ms="min_price" type="number" placeholder="Min" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500">
                                            <input wire:model.live.debounce.500ms="max_price" type="number" placeholder="Max" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500">
                                        </div>
                                        <div class="flex space-x-3 text-xs">
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="price_type" value="hour" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                                <span class="ml-1 text-gray-600 dark:text-gray-400">za godz.</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="price_type" value="day" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                                <span class="ml-1 text-gray-600 dark:text-gray-400">za dzień</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="space-y-2">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Minimalna ocena</label>
                                        <select wire:model.live="min_rating" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Wszystkie oceny</option>
                                            <option value="3">⭐⭐⭐ 3+</option>
                                            <option value="4">⭐⭐⭐⭐ 4+</option>
                                            <option value="4.5">⭐⭐⭐⭐⭐ 4.5+</option>
                                        </select>
                                    </div>

                                    <!-- Max Pets -->
                                    <div class="space-y-2">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Maks. liczba zwierząt</label>
                                        <select wire:model.live="max_pets" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Bez ograniczeń</option>
                                            <option value="1">1 zwierzę</option>
                                            <option value="2">2 zwierzęta</option>
                                            <option value="3">3 zwierzęta</option>
                                            <option value="5">5+ zwierząt</option>
                                        </select>
                                    </div>

                                    <!-- Experience -->
                                    <div class="space-y-2">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Doświadczenie</label>
                                        <select wire:model.live="experience_years" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Dowolne</option>
                                            <option value="1">1+ rok</option>
                                            <option value="2">2+ lata</option>
                                            <option value="5">5+ lat</option>
                                            <option value="10">10+ lat</option>
                                        </select>
                                    </div>

                                    <!-- Checkboxes -->
                                    <div class="space-y-2">
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" wire:model.live="verified_only" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">✅ Tylko zweryfikowani</span>
                                        </label>
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" wire:model.live="has_insurance" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">🛡️ Z ubezpieczeniem</span>
                                        </label>
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" wire:model.live="instant_booking" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">⚡ Natychmiastowa rezerwacja</span>
                                        </label>
                                    </div>

                                    <!-- Date/Time Section -->
                                    <div class="space-y-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                                        <!-- Available Date -->
                                        <div class="space-y-2">
                                            <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Data dostępności</label>
                                            <input wire:model.live="available_date" type="date" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>

                                        <!-- Time Range -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Od godz.</label>
                                                <input wire:model.live="start_time" type="time" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Do godz.</label>
                                                <input wire:model.live="end_time" type="time" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Radius Slider -->
                                    <div class="space-y-2 border-t border-gray-200 dark:border-gray-600 pt-3">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                            📍 Promień: <span class="font-semibold text-purple-600">{{ $radius }} km</span>
                                        </label>
                                        <input wire:model.live="radius" type="range" min="1" max="50" class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <div class="flex justify-between text-xs text-gray-400">
                                            <span>1 km</span>
                                            <span>50 km</span>
                                        </div>
                                    </div>

                                    <!-- Sorting -->
                                    <div class="space-y-2 border-t border-gray-200 dark:border-gray-600 pt-3">
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Sortowanie</label>
                                        <select wire:model.live="sort_by" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="relevance">🎯 Trafność</option>
                                            <option value="distance">📍 Odległość</option>
                                            <option value="price_low">💰 Cena: najtańsze</option>
                                            <option value="price_high">💸 Cena: najdroższe</option>
                                            <option value="rating">⭐ Najwyżej oceniane</option>
                                            <option value="experience">🏆 Doświadczenie</option>
                                            <option value="most_booked">🔥 Popularne</option>
                                            <option value="newest">✨ Najnowsze</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Premium Features & Actions -->
                            <div class="glass-card rounded-2xl border border-white/20 backdrop-blur-md bg-gradient-to-br from-white/95 to-purple-50/95 dark:from-gray-800/95 dark:to-purple-900/20 shadow-xl p-5">
                                <!-- Saved Searches -->
                                <div class="mb-4">
                                    <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                        Zapisane wyszukiwania
                                    </h3>
                                    <div class="space-y-2">
                                        @auth
                                            <button
                                                wire:click="saveSearch"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-purple-700 dark:text-purple-300 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 border border-purple-200 dark:border-purple-700 rounded-xl transition-all duration-200 hover:scale-105"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                                </svg>
                                                Zapisz obecne wyszukiwanie
                                            </button>
                                            <button
                                                wire:click="loadSavedSearch"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl transition-all duration-200 hover:scale-105"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                                Wczytaj zapisane
                                            </button>
                                        @else
                                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Zaloguj się, aby zapisywać wyszukiwania</p>
                                                <a href="/login" class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg hover:from-purple-600 hover:to-blue-600 transition-all duration-200">
                                                    Zaloguj się
                                                </a>
                                            </div>
                                        @endauth
                                    </div>
                                </div>

                                <!-- Become a Sitter CTA -->
                                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-700 mb-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-gray-900 dark:text-white font-semibold text-sm">Zostań opiekunem</div>
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">40-80 zł/godz</div>
                                        </div>
                                    </div>
                                    <a href="/register?type=sitter" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-900 bg-yellow-400 hover:bg-yellow-300 rounded-lg transition-all duration-200 hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                        </svg>
                                        Dołącz jako opiekun
                                    </a>
                                </div>
                            </div>

                            <!-- Pet Sitter Hero Header -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900 p-6">
                                <!-- Background Pattern -->
                                <div class="absolute inset-0 opacity-10">
                                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                        <defs>
                                            <pattern id="bottomsearchpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                                <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100" height="100" fill="url(#bottomsearchpattern)"/>
                                    </svg>
                                </div>

                                <div class="relative z-10">
                                    <!-- Badge -->
                                    <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-3 py-1 mb-4">
                                        <span class="text-white/90 text-xs font-medium">🏆 Najlepsi opiekunowie</span>
                                    </div>

                                    <!-- Title -->
                                    <h2 class="text-xl font-bold text-white mb-2 leading-tight">
                                        Znajdź <span class="text-yellow-300">idealnego</span> pet sittera
                                    </h2>

                                    <!-- Pet Sitter focused features -->
                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        <div class="feature-pill-mini rounded-lg px-3 py-2 text-white text-xs font-medium flex items-center hover:bg-white/20 transition-colors">
                                            <span class="text-sm mr-1">🔒</span>
                                            Zweryfikowani
                                        </div>
                                        <div class="feature-pill-mini rounded-lg px-3 py-2 text-white text-xs font-medium flex items-center hover:bg-white/20 transition-colors">
                                            <span class="text-sm mr-1">🛡️</span>
                                            Ubezpieczeni
                                        </div>
                                        <div class="feature-pill-mini rounded-lg px-3 py-2 text-white text-xs font-medium flex items-center hover:bg-white/20 transition-colors">
                                            <span class="text-sm mr-1">⭐</span>
                                            Oceniani 4.9+
                                        </div>
                                        <div class="feature-pill-mini rounded-lg px-3 py-2 text-white text-xs font-medium flex items-center hover:bg-white/20 transition-colors">
                                            <span class="text-sm mr-1">💬</span>
                                            Chat 24/7
                                        </div>
                                    </div>

                                    <!-- Quick stats -->
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-yellow-300 mb-1">1,250+</div>
                                        <div class="text-white/70 text-xs">Dostępnych opiekunów</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center Column: Results List -->
                    <div class="flex-1 lg:ml-0 min-h-screen"
                         @if(!$show_desktop_map) style="margin-right: 0;" @endif>
                        <div class="h-full flex flex-col">
                            <!-- Sort and View Options -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                    <!-- Sort -->
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                            </svg>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sortuj:</label>
                                        </div>
                                        <select
                                            wire:model.live="sort_by"
                                            class="text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-2 py-1"
                                        >
                                            <option value="relevance">Trafność</option>
                                            <option value="distance">Odległość</option>
                                            <option value="price_low">Cena: rosnąco</option>
                                            <option value="price_high">Cena: malejąco</option>
                                            <option value="rating">Ocena</option>
                                        </select>
                                    </div>

                                    <!-- Results Count and View Toggle -->
                                    <div class="flex items-center space-x-4">
                                        <!-- Results Count -->
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Znaleziono <span class="font-semibold text-gray-900 dark:text-white">{{ $this->totalResults ?? 0 }}</span>
                                            @if(($this->totalResults ?? 0) === 1) usługę @elseif(($this->totalResults ?? 0) < 5) usługi @else usług @endif
                                        </div>

                                        <!-- View Mode Toggle -->
                                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg p-1 bg-white dark:bg-gray-700">
                                            <button
                                                wire:click="setViewMode('grid')"
                                                class="px-2 py-1 rounded {{ $view_mode === 'grid' ? 'bg-purple-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                                title="Widok siatki"
                                            >
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="setViewMode('list')"
                                                class="px-2 py-1 rounded {{ $view_mode === 'list' ? 'bg-purple-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                                title="Widok listy"
                                            >
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Map Toggle Button -->
                                        @if(!$show_desktop_map)
                                            <button
                                                wire:click="toggleDesktopMap"
                                                class="flex items-center space-x-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 shadow-sm"
                                                title="Pokaż mapę"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                </svg>
                                                <span class="text-sm font-medium">@if($show_desktop_map) Ukryj mapę @else Pokaż mapę @endif</span>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Map Toggle (mobile) -->
                                    <div class="lg:hidden">
                                        <button
                                            wire:click="$toggle('show_mobile_map')"
                                            class="inline-flex items-center px-3 py-2 bg-purple-50 hover:bg-purple-100 border border-purple-200 text-sm font-medium rounded-lg text-purple-700 transition-all duration-200"
                                        >
                                            {{ $show_mobile_map ?? false ? '📋 Lista' : '🗺️ Mapa' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Map (when toggled) -->
                            @if(($show_mobile_map ?? false))
                                <div class="lg:hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-4" style="height: 400px;">
                                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20">
                                        <h3 class="text-md font-semibold text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            Mapa wyników
                                        </h3>
                                    </div>
                                    <div class="h-full">
                                        <livewire:search.search-map wire:key="search-map-mobile" />
                                    </div>
                                </div>
                            @endif

                            <!-- Results List -->
                            <div class="flex-1">
                                <div class="h-full overflow-auto space-y-4 custom-scrollbar" wire:loading.class="opacity-50 pointer-events-none" wire:target="handleFiltersUpdate">
                                    <livewire:search.search-results wire:key="search-results" :viewMode="$view_mode" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Map (Desktop only) -->
                    @if($show_desktop_map)
                    <div class="hidden lg:block w-96 flex-shrink-0">
                        <div class="sticky top-20">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                                 style="height: calc(100vh - 6rem);"
                            >
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            Mapa wyników
                                        </h3>
                                        <button
                                            wire:click="toggleDesktopMap"
                                            class="flex items-center space-x-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition-colors duration-200"
                                            title="Ukryj mapę"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span>Ukryj mapę</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="h-full">
                                    <livewire:search.search-map wire:key="search-map-desktop" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                </div>
            </div>
        </div>

        <!-- Location Detector Component (hidden but active) -->
        <livewire:search.location-detector wire:key="location-detector" />

    </div>

    @push('scripts')
    <!-- Search Coordination Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            // URL state management
            Livewire.on('update-browser-url', (url) => {
                window.history.replaceState({}, '', url);
            });

            // Save UI state to localStorage as backup
            Livewire.on('view-mode-changed', (mode) => {
                localStorage.setItem('pethelp_view_mode', mode);
            });

            Livewire.on('desktop-map-toggled', (isVisible) => {
                localStorage.setItem('pethelp_map_visible', isVisible ? 'true' : 'false');
            });

            // Search saved notification
            Livewire.on('search-saved', () => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = '💾 Wyszukiwanie zostało zapisane!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });

            // Location error notification
            Livewire.on('location-error', (error) => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = '❌ Nie udało się wykryć lokalizacji: ' + (error || 'Sprawdź uprawnienia przeglądarki');
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });

            // Loading state management
            let loadingTimeout;
            Livewire.hook('message.sent', (message, component) => {
                loadingTimeout = setTimeout(() => {
                    document.body.classList.add('livewire-loading');
                }, 100);
            });

            Livewire.hook('message.processed', (message, component) => {
                clearTimeout(loadingTimeout);
                document.body.classList.remove('livewire-loading');
            });
        });
    </script>

    <style>
        /* Enhanced card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Glass effect for stats cards */
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Glassmorphism cards */
        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
        }

        /* Pet sitter focused feature pills */
        .feature-pill-mini {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s ease;
        }

        .feature-pill-mini:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.02);
        }

        /* Pet type buttons */
        .pet-type-btn {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .pet-type-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .pet-type-btn:hover::before {
            left: 100%;
        }

        .pet-type-btn:active {
            transform: scale(0.95);
        }

        /* Enhanced gradient animations */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero-gradient-animated {
            background: linear-gradient(-45deg, #667eea, #764ba2, #4f46e5, #7c3aed);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        /* Floating animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-4px) rotate(1deg); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-float-slow {
            animation: floatSlow 4s ease-in-out infinite;
        }

        /* Pulse glow effect */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 5px rgba(139, 92, 246, 0.3); }
            50% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.6), 0 0 30px rgba(139, 92, 246, 0.4); }
        }

        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }

        /* Loading shimmer */
        @keyframes shimmerNew {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .shimmer-loading {
            position: relative;
            overflow: hidden;
        }

        .shimmer-loading::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: translateX(-100%);
            animation: shimmerNew 1.5s infinite;
        }

        /* Enhanced scrollbars */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #8b5cf6;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #7c3aed;
        }

        /* Smooth fade-in animation for results */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading shimmer effect */
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .livewire-loading {
            cursor: wait !important;
        }

        .livewire-loading * {
            pointer-events: none !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Fullscreen Layout Overrides */
        .h-\\[calc\\(100vh-80px\\)\\] {
            height: calc(100vh - 80px);
        }

        /* Three Column Layout Responsiveness */
        @media (max-width: 1024px) {
            /* On tablets and below, stack columns vertically */
            .lg\\:col-span-3,
            .lg\\:col-span-5,
            .lg\\:col-span-4 {
                grid-column: span 12 / span 12;
            }

            /* Full height for mobile containers */
            .h-\\[calc\\(100vh-80px\\)\\] {
                height: auto;
                min-height: calc(100vh - 80px);
            }

            /* Mobile column heights */
            .lg\\:col-span-3 .h-full,
            .lg\\:col-span-5 .h-full {
                height: auto;
            }

            /* Adjust sticky positioning for mobile */
            .sticky {
                position: relative;
                top: auto;
            }

            /* Reduce map height on mobile */
            .lg\\:hidden .bg-white.dark\\:bg-gray-800 {
                height: 350px !important;
            }
        }

        /* Tablet specific adjustments */
        @media (min-width: 768px) and (max-width: 1023px) {
            /* Two column layout on tablets */
            .lg\\:col-span-3 {
                grid-column: span 4 / span 4;
            }

            .lg\\:col-span-5 {
                grid-column: span 8 / span 8;
            }

            .lg\\:col-span-4 {
                grid-column: span 12 / span 12;
                margin-top: 1.5rem;
            }
        }

        /* Enhanced mobile styles */
        @media (max-width: 767px) {
            /* Hero-inspired sidebar adjustments */
            .lg\\:col-span-3 {
                margin-bottom: 2rem;
            }

            /* Compact glass cards on mobile */
            .glass-card {
                margin: 0 -0.5rem;
                width: calc(100% + 1rem);
                border-radius: 1rem;
                backdrop-filter: blur(12px);
            }

            /* Mobile pet type grid */
            .pet-type-btn {
                padding: 0.75rem;
                min-height: 60px;
            }

            /* Better spacing for hero header */
            .space-y-6 {
                gap: 1.5rem;
            }

            /* Feature pills responsive */
            .feature-pill-mini {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }

            /* Better touch targets */
            button, select, input {
                min-height: 44px;
            }

            /* Reduce glass card padding on mobile */
            .glass-card .p-6 {
                padding: 1rem;
            }

            .glass-card .p-5 {
                padding: 1rem;
            }

            /* Mobile hero header */
            .rounded-2xl {
                border-radius: 1rem;
            }
        }

        /* Tablet optimizations */
        @media (min-width: 768px) and (max-width: 1023px) {
            .glass-card {
                backdrop-filter: blur(14px);
            }

            .pet-type-btn {
                padding: 1rem;
            }
        }

        /* Dark mode enhancements */
        @media (prefers-color-scheme: dark) {
            .glass-card {
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            }

            .glass-card:hover {
                box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.4);
            }

            .feature-pill-mini {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .feature-pill-mini:hover {
                background: rgba(255, 255, 255, 0.15);
            }
        }

        /* Print styles */
        @media print {
            .sticky,
            .lg\\:col-span-4,
            .bg-white.dark\\:bg-gray-800.rounded-lg.shadow-lg:has([style*="height: 600px"]) {
                display: none !important;
            }

            .lg\\:col-span-5 {
                grid-column: span 12 / span 12;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .bg-white.dark\\:bg-gray-800 {
                border: 2px solid currentColor;
            }

            .shadow-lg, .shadow-sm {
                box-shadow: 0 0 0 2px currentColor;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .transition-all,
            .transition-colors,
            .animate-pulse {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
    @endpush

    <script>
        // Handle URL updates from Livewire components
        document.addEventListener('livewire:init', () => {
            Livewire.on('update-browser-url', (url) => {
                if (history.replaceState) {
                    history.replaceState(null, null, url);
                }
            });
        });
    </script>
</div>