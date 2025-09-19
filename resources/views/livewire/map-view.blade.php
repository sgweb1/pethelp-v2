<div
    x-data="{
        mapComponent: null,
        init() {
            console.log('Map container initializing...');
            console.log('createMapComponent available:', typeof window.createMapComponent);

            if (window.createMapComponent) {
                this.mapComponent = window.createMapComponent();
                this.$nextTick(() => {
                    if (this.mapComponent && this.mapComponent.initMap) {
                        console.log('Initializing map...');
                        this.mapComponent.initMap();
                    }
                });
            } else {
                console.error('Map component not available. Retrying in 1 second...');
                setTimeout(() => {
                    if (window.createMapComponent) {
                        this.mapComponent = window.createMapComponent();
                        this.mapComponent.initMap();
                    } else {
                        console.error('Map component still not available after retry');
                        this.showError();
                    }
                }, 1000);
            }
        },
        showError() {
            const container = document.getElementById('map-container');
            if (container) {
                container.innerHTML = '&lt;div class=&quot;flex items-center justify-center h-full bg-gray-100&quot;&gt;&lt;div class=&quot;text-center p-8&quot;&gt;&lt;div class=&quot;text-red-500 mb-4&quot;&gt;&lt;svg class=&quot;w-16 h-16 mx-auto&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;&gt;&lt;path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;&lt;/div&gt;&lt;h3 class=&quot;text-lg font-medium text-gray-900 mb-2&quot;&gt;Błąd ładowania mapy&lt;/h3&gt;&lt;p class=&quot;text-gray-600 mb-4&quot;&gt;Komponent mapy nie załadował się. Odśwież stronę.&lt;/p&gt;&lt;button onclick=&quot;location.reload()&quot; class=&quot;px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700&quot;&gt;Odśwież&lt;/button&gt;&lt;/div&gt;&lt;/div&gt;';
            }
        }
    }"
    class="w-full h-screen relative">
    {{-- Map Container --}}
    <div id="map-container" class="w-full h-full" wire:ignore></div>

    {{-- Search & Filters Panel --}}
    <div class="absolute top-4 left-4 max-w-sm z-10">
        {{-- Główny panel z glassmorphism --}}
        <div class="backdrop-blur-xl bg-white/90 dark:bg-gray-900/90 border border-white/20 rounded-2xl shadow-2xl p-6 transition-all duration-300 hover:bg-white/95 hover:shadow-3xl">

            {{-- Search z enhanced styling --}}
            <div class="mb-6 group">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Szukaj na mapie..."
                        class="w-full pl-12 pr-4 py-3 bg-gray-50/80 backdrop-blur-sm border border-gray-200/50 rounded-xl
                               focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50 focus:bg-white/90
                               transition-all duration-200 placeholder-gray-400"
                    />
                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <div class="animate-spin h-4 w-4 border-2 border-blue-500/30 border-t-blue-500 rounded-full"></div>
                    </div>
                </div>
            </div>

        {{-- Content Type Filters with Enhanced Design --}}
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 text-base">
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Kategorie
                </span>
            </h3>
            <div class="space-y-3">
                @foreach($categories as $type => $typeCategories)
                    <label wire:key="category-{{ $type }}"
                           class="group flex items-center space-x-3 cursor-pointer p-3 rounded-xl
                                  backdrop-blur-sm bg-white/40 dark:bg-gray-800/40
                                  border border-gray-200/30 dark:border-gray-600/30
                                  hover:bg-white/60 dark:hover:bg-gray-700/60
                                  hover:border-blue-300/50 dark:hover:border-blue-400/50
                                  hover:shadow-lg hover:-translate-y-0.5
                                  transition-all duration-300 ease-out">
                        <div class="relative">
                            <input
                                type="checkbox"
                                wire:change="toggleContentType('{{ $type }}')"
                                @checked(in_array($type, $contentTypes))
                                class="peer sr-only"
                                id="category-{{ $type }}"
                            />
                            <div class="w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-500
                                        peer-checked:border-blue-500 peer-checked:bg-gradient-to-br peer-checked:from-blue-500 peer-checked:to-purple-600
                                        transition-all duration-200 ease-out
                                        flex items-center justify-center
                                        group-hover:border-blue-400 group-hover:shadow-md">
                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200
                                         group-hover:text-gray-900 dark:group-hover:text-white
                                         transition-colors duration-200">
                                {{ $this->getContentTypeLabel($type) }}
                            </span>
                            @if(isset($mapStats['content_types'][$type]))
                                <span class="ml-2 px-2 py-0.5 text-xs font-medium
                                           bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-600 dark:to-gray-700
                                           text-gray-600 dark:text-gray-300 rounded-full
                                           group-hover:from-blue-50 group-hover:to-purple-50
                                           dark:group-hover:from-blue-900/30 dark:group-hover:to-purple-900/30
                                           transition-all duration-200">
                                    {{ $mapStats['content_types'][$type] }}
                                </span>
                            @endif
                        </div>
                        {{-- Ripple effect on click --}}
                        <div class="absolute inset-0 rounded-xl opacity-0 group-active:opacity-20
                                   bg-gradient-to-r from-blue-400 to-purple-500
                                   transition-opacity duration-150 pointer-events-none"></div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Feature Flags with Enhanced Design --}}
        <div class="mb-6 space-y-3">
            <label class="group flex items-center space-x-3 cursor-pointer p-3 rounded-xl
                          backdrop-blur-sm bg-white/40 dark:bg-gray-800/40
                          border border-gray-200/30 dark:border-gray-600/30
                          hover:bg-white/60 dark:hover:bg-gray-700/60
                          hover:border-amber-300/50 dark:hover:border-amber-400/50
                          hover:shadow-lg hover:-translate-y-0.5
                          transition-all duration-300 ease-out">
                <div class="relative">
                    <input
                        type="checkbox"
                        wire:model.live="featuredOnly"
                        class="peer sr-only"
                        id="featured-only"
                    />
                    <div class="w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-500
                                peer-checked:border-amber-500 peer-checked:bg-gradient-to-br peer-checked:from-amber-400 peer-checked:to-orange-500
                                transition-all duration-200 ease-out
                                flex items-center justify-center
                                group-hover:border-amber-400 group-hover:shadow-md">
                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200
                                 group-hover:text-gray-900 dark:group-hover:text-white
                                 transition-colors duration-200">
                        Tylko wyróżnione
                    </span>
                    <span class="ml-2 px-2 py-0.5 text-xs font-medium
                               bg-gradient-to-r from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30
                               text-amber-700 dark:text-amber-300 rounded-full
                               transition-all duration-200">
                        {{ $mapStats['featured_count'] ?? 0 }}
                    </span>
                </div>
                <div class="absolute inset-0 rounded-xl opacity-0 group-active:opacity-20
                           bg-gradient-to-r from-amber-400 to-orange-500
                           transition-opacity duration-150 pointer-events-none"></div>
            </label>

            <label class="group flex items-center space-x-3 cursor-pointer p-3 rounded-xl
                          backdrop-blur-sm bg-white/40 dark:bg-gray-800/40
                          border border-gray-200/30 dark:border-gray-600/30
                          hover:bg-white/60 dark:hover:bg-gray-700/60
                          hover:border-red-300/50 dark:hover:border-red-400/50
                          hover:shadow-lg hover:-translate-y-0.5
                          transition-all duration-300 ease-out">
                <div class="relative">
                    <input
                        type="checkbox"
                        wire:model.live="urgentOnly"
                        class="peer sr-only"
                        id="urgent-only"
                    />
                    <div class="w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-500
                                peer-checked:border-red-500 peer-checked:bg-gradient-to-br peer-checked:from-red-500 peer-checked:to-pink-600
                                transition-all duration-200 ease-out
                                flex items-center justify-center
                                group-hover:border-red-400 group-hover:shadow-md">
                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200
                                 group-hover:text-gray-900 dark:group-hover:text-white
                                 transition-colors duration-200">
                        Tylko pilne
                    </span>
                    <span class="ml-2 px-2 py-0.5 text-xs font-medium
                               bg-gradient-to-r from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30
                               text-red-700 dark:text-red-300 rounded-full
                               transition-all duration-200">
                        {{ $mapStats['urgent_count'] ?? 0 }}
                    </span>
                </div>
                <div class="absolute inset-0 rounded-xl opacity-0 group-active:opacity-20
                           bg-gradient-to-r from-red-400 to-pink-500
                           transition-opacity duration-150 pointer-events-none"></div>
            </label>
        </div>

        {{-- Price Range with Enhanced Design --}}
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 text-base">
                <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    Zakres cen
                </span>
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="relative group">
                    <label class="absolute -top-2 left-3 px-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                  bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full z-10">
                        Od
                    </label>
                    <input
                        wire:model.live.debounce.500ms="minPrice"
                        type="number"
                        placeholder="0"
                        class="w-full px-4 py-3 text-sm
                               backdrop-blur-sm bg-white/60 dark:bg-gray-800/60
                               border border-gray-200/50 dark:border-gray-600/50 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500/50
                               focus:bg-white/80 dark:focus:bg-gray-700/80
                               hover:bg-white/70 dark:hover:bg-gray-700/70
                               transition-all duration-200 ease-out
                               placeholder-gray-400 dark:placeholder-gray-500"
                    />
                    <div class="absolute inset-0 rounded-xl opacity-0 group-focus-within:opacity-10
                               bg-gradient-to-r from-green-400 to-emerald-500
                               transition-opacity duration-200 pointer-events-none"></div>
                </div>
                <div class="relative group">
                    <label class="absolute -top-2 left-3 px-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                  bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full z-10">
                        Do
                    </label>
                    <input
                        wire:model.live.debounce.500ms="maxPrice"
                        type="number"
                        placeholder="∞"
                        class="w-full px-4 py-3 text-sm
                               backdrop-blur-sm bg-white/60 dark:bg-gray-800/60
                               border border-gray-200/50 dark:border-gray-600/50 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500/50
                               focus:bg-white/80 dark:focus:bg-gray-700/80
                               hover:bg-white/70 dark:hover:bg-gray-700/70
                               transition-all duration-200 ease-out
                               placeholder-gray-400 dark:placeholder-gray-500"
                    />
                    <div class="absolute inset-0 rounded-xl opacity-0 group-focus-within:opacity-10
                               bg-gradient-to-r from-green-400 to-emerald-500
                               transition-opacity duration-200 pointer-events-none"></div>
                </div>
            </div>
        </div>

        {{-- Clear Filters Button with Enhanced Design --}}
        <button
            wire:click="clearFilters"
            class="group relative w-full px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300
                   backdrop-blur-sm bg-white/50 dark:bg-gray-800/50
                   border border-gray-200/50 dark:border-gray-600/50 rounded-xl
                   hover:bg-white/70 dark:hover:bg-gray-700/70
                   hover:border-gray-300/60 dark:hover:border-gray-500/60
                   hover:text-gray-900 dark:hover:text-white
                   hover:shadow-lg hover:-translate-y-0.5
                   active:translate-y-0 active:shadow-md
                   transition-all duration-300 ease-out
                   overflow-hidden"
        >
            <span class="relative z-10 flex items-center justify-center space-x-2">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Wyczyść filtry</span>
            </span>
            {{-- Gradient overlay on hover --}}
            <div class="absolute inset-0 opacity-0 group-hover:opacity-20
                       bg-gradient-to-r from-gray-400 to-gray-500
                       transition-opacity duration-300"></div>
            {{-- Subtle border glow --}}
            <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100
                       bg-gradient-to-r from-transparent via-gray-200/20 to-transparent
                       transition-opacity duration-300"></div>
        </button>
    </div>

    {{-- Map Stats with Enhanced Design --}}
    <div class="absolute bottom-4 left-4 z-10">
        <div class="backdrop-blur-xl bg-white/90 dark:bg-gray-900/90
                   border border-white/20 dark:border-gray-700/20
                   rounded-2xl shadow-2xl p-4 text-sm
                   hover:bg-white/95 dark:hover:bg-gray-800/95
                   transition-all duration-300">
            <div class="flex items-center space-x-2 mb-2">
                <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 animate-pulse"></div>
                <div class="font-semibold text-gray-800 dark:text-gray-200">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Znaleziono: {{ count($mapData) }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">lokalizacji</span>
                </div>
            </div>
            @if(isset($mapStats['total_locations']))
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-5">
                    z {{ $mapStats['total_locations'] }} łącznie w bazie
                </div>
            @endif
        </div>
    </div>

</div>
