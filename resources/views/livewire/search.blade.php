<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Compact Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4">
        <div class="max-w-none px-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Znajdź opiekuna</h1>
                <!-- Breadcrumb -->
                <nav class="text-sm text-white/80">
                    <a href="/" class="hover:text-white">Strona główna</a>
                    <span class="mx-2">/</span>
                    <span>Wyszukiwanie</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Fullscreen Container -->
    <div class="h-[calc(100vh-80px)]">

        <!-- Fullscreen Three Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 h-full px-4">
            <!-- Left Column - Filters (3 columns) -->
            <div class="lg:col-span-3">
                <div class="h-full overflow-y-auto space-y-4 py-4">
                    <!-- Search Actions -->
                    <div class="flex flex-wrap gap-2">
                        @auth
                            <button
                                wire:click="saveSearch"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                title="Zapisz wyszukiwanie"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Zapisz
                            </button>
                            <button
                                wire:click="loadSavedSearch"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                title="Wczytaj ostatnie wyszukiwanie"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Wczytaj
                            </button>
                        @endauth
                    </div>

                    <!-- Compact Search Filters -->
                    <livewire:search.search-filters wire:key="search-filters" />

                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Znalezione wyniki:</div>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" wire:loading.class="animate-pulse">
                            <span wire:loading.remove>{{ $this->totalResults ?? 0 }}</span>
                            <span wire:loading>...</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">opiekunów w Twojej okolicy</div>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Results List (5 columns) -->
            <div class="lg:col-span-5">
                <div class="h-full flex flex-col">
                    <!-- Sort and View Options - Fixed Header -->
                    <div class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 mb-4 border">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Sortuj:</label>
                                <select
                                    wire:model.live="sort_by"
                                    class="text-xs border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                >
                                    <option value="relevance">Trafność</option>
                                    <option value="distance">Odległość</option>
                                    <option value="price_low">Cena: najniższa</option>
                                    <option value="price_high">Cena: najwyższa</option>
                                    <option value="rating">Najlepsze oceny</option>
                                </select>
                            </div>

                            <!-- Show Map Toggle for Mobile -->
                            <div class="lg:hidden">
                                <button
                                    wire:click="$toggle('show_mobile_map')"
                                    class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    {{ $show_mobile_map ?? false ? 'Lista' : 'Mapa' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Map (when toggled) -->
                    @if(($show_mobile_map ?? false) && $this->isMobile())
                        <div class="lg:hidden bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-4" style="height: 350px;">
                            <livewire:search.search-map wire:key="search-map-mobile" />
                        </div>
                    @endif

                    <!-- Results List - Scrollable -->
                    <div class="flex-1 overflow-y-auto" wire:loading.class="opacity-50 pointer-events-none" wire:target="handleFiltersUpdate">
                        <livewire:search.search-results wire:key="search-results" lazy />
                    </div>
                </div>
            </div>

            <!-- Right Column - Map (4 columns) - Hidden on Mobile -->
            <div class="hidden lg:block lg:col-span-4">
                <div class="h-full flex flex-col py-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden h-full border flex flex-col">
                        <!-- Map Header -->
                        <div class="flex-shrink-0 p-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Mapa wyników</h3>
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span wire:loading.remove>{{ $this->totalResults ?? 0 }} lokalizacji</span>
                                    <span wire:loading>Ładowanie...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Map Component - Full Height -->
                        <div class="flex-1 min-h-0">
                            <livewire:search.search-map wire:key="search-map-desktop" />
                        </div>
                    </div>
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
            /* Full width cards on mobile */
            .bg-white.dark\\:bg-gray-800.rounded-lg {
                border-radius: 0.5rem;
                margin: 0 -1rem;
                width: calc(100% + 2rem);
            }

            /* Better padding for mobile content */
            .max-w-7xl.mx-auto.px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            /* Compact filters on mobile */
            .space-y-4 > div:not(:last-child) {
                margin-bottom: 1rem;
            }

            /* Better touch targets */
            button, select, input {
                min-height: 44px;
            }

            /* Stack filter controls vertically */
            .flex.justify-between.items-center {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .flex.items-center.space-x-4 {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
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
</div>