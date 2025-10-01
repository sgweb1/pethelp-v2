@props(['activeSection' => null])

<nav class="bg-white dark:bg-gray-800 shadow-sm border-r border-gray-200 dark:border-gray-700 min-h-screen w-64 fixed left-0 top-0 z-40 transform transition-transform duration-300 ease-in-out"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     x-data="{
         activeSection: '{{ $activeSection }}',
         expandedSections: {
             profile: '{{ $activeSection }}' === 'profile',
             pets: '{{ $activeSection }}' === 'pets',
             gallery: '{{ $activeSection }}' === 'gallery',
             petSitting: '{{ $activeSection }}' === 'petSitting',
             announcements: '{{ $activeSection }}' === 'announcements',
             communication: '{{ $activeSection }}' === 'communication',
             statistics: '{{ $activeSection }}' === 'statistics'
         }
     }"
     :class="{ '-translate-x-full': !open, 'translate-x-0': open }">

    <!-- Logo/Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                <span class="text-white font-bold text-sm">PH</span>
            </div>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">PetHelp</span>
        </div>
        <button @click="open = false" class="lg:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <div class="py-4 overflow-y-auto">
        <!-- Dashboard Overview -->
        <div class="px-4 mb-6">
            <a href="{{ route('profile.dashboard') }}"
               class="flex items-center p-3 rounded-lg {{ $activeSection === 'dashboard' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
        </div>

        <!-- Main Sections -->
        <div class="space-y-2">
            <!-- Profile Section -->
            <div class="px-4">
                <button @click="expandedSections.profile = !expandedSections.profile"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Mój Profil</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.profile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.profile" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="{{ route('profile.edit') }}"
                       class="block p-2 text-sm {{ $activeSection === 'profile' ? 'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-primary-900/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} rounded transition-colors">
                        Mój profil
                    </a>
                    <a href="{{ route('profile.edit') }}#personal-data"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Dane osobowe
                    </a>
                    @if(auth()->user()->isSitter())
                        <a href="{{ route('profile.edit') }}#payout-settings"
                           class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                            Ustawienia wypłat
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}#account-management"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Zarządzanie kontem
                    </a>
                    <a href="{{ route('profile.edit') }}#notifications"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Powiadomienia
                    </a>
                    @if(auth()->user()->isSitter())
                        <a href="{{ route('profile.edit') }}#earnings-report"
                           class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                            Raportowanie zarobków
                        </a>
                    @endif
                </div>
            </div>

            <!-- Pets Section -->
            <div class="px-4">
                <button @click="expandedSections.pets = !expandedSections.pets"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="font-medium">Moje Zwierzęta</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.pets }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.pets" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="{{ route('profile.pets.index') }}"
                       class="block p-2 text-sm {{ $activeSection === 'pets' ? 'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-primary-900/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} rounded transition-colors">
                        Lista zwierząt
                    </a>
                    <a href="{{ route('profile.pets.create') }}"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Dodaj zwierzę
                    </a>
                    <a href="#"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Kalendarz opieki
                    </a>
                    <a href="#"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Dokumenty
                    </a>
                </div>
            </div>

            <!-- Gallery Section -->
            <div class="px-4">
                <button @click="expandedSections.gallery = !expandedSections.gallery"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-medium">Galeria</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.gallery }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.gallery" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="{{ route('profile.gallery.index') }}"
                       class="block p-2 text-sm {{ $activeSection === 'gallery' ? 'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-primary-900/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} rounded transition-colors">
                        Wszystkie zdjęcia
                    </a>
                    <a href="{{ route('profile.gallery.upload') }}"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Upload zdjęć
                    </a>
                    <a href="{{ route('profile.gallery.index') }}?view=albums"
                       class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors">
                        Albumy
                    </a>
                </div>
            </div>

            <!-- Pet Sitting Section (tylko dla sitterów) -->
            @if(auth()->user()->isSitter())
            <div class="px-4">
                <button @click="expandedSections.petSitting = !expandedSections.petSitting"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-medium">Pet Sitting</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.petSitting }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.petSitting" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Mój dom</a>
                    <a href="{{ route('profile.services.index') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Moje usługi</a>
                    <a href="{{ route('profile.availability') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Kalendarz dostępności</a>
                    <a href="{{ route('profile.bookings') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Zlecenia</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Rozliczenia</a>
                </div>
            </div>
            @endif

            <!-- Announcements Section -->
            <div class="px-4">
                <button @click="expandedSections.announcements = !expandedSections.announcements"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <span class="font-medium">Ogłoszenia</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.announcements }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.announcements" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="{{ route('profile.events.index') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Wydarzenia</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Usługi dodatkowe</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Ogłoszenia różne</a>
                    <a href="{{ route('advertisements.index') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Sprzedaż/Adopcja</a>
                </div>
            </div>

            <!-- Communication Section -->
            <div class="px-4">
                <button @click="expandedSections.communication = !expandedSections.communication"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="font-medium">Komunikacja</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.communication }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.communication" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="{{ route('profile.chat.index') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Wiadomości</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Połączenia</a>
                    <a href="{{ route('profile.reviews') }}" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Recenzje</a>
                </div>
            </div>

            <!-- Statistics Section (tylko dla sitterów) -->
            @if(auth()->user()->isSitter())
            <div class="px-4">
                <button @click="expandedSections.statistics = !expandedSections.statistics"
                        class="flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-medium">Statystyki</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expandedSections.statistics }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="expandedSections.statistics" x-collapse class="ml-8 mt-2 space-y-1">
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Przychody</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Raport zleceń</a>
                    <a href="#" class="block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Oceny</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- User Info Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                <span class="text-gray-600 text-xs font-medium">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->profile?->first_name ?? auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    @if(auth()->user()->isSitter() && auth()->user()->isOwner())
                        Pet Sitter & Właściciel
                    @elseif(auth()->user()->isSitter())
                        Pet Sitter
                    @elseif(auth()->user()->isOwner())
                        Właściciel
                    @else
                        Użytkownik
                    @endif
                </p>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile menu button -->
<button @click="open = true"
        class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Overlay for mobile -->
<div x-show="open"
     @click="open = false"
     class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>