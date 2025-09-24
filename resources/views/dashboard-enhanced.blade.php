<x-dashboard-layout>
    @section('title', 'Dashboard - PetHelp')

    @section('header-title')
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Dashboard</h1>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, j F Y') }}
            </span>
        </div>
    @endsection

    <!-- Hero-style Background -->
    <div class="relative -m-4 sm:-m-6 lg:-m-8 mb-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dashboardpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#dashboardpattern)"/>
            </svg>
        </div>

        <div class="relative z-10 p-4 sm:p-6 lg:p-8 space-y-6">
            <!-- Welcome Hero Banner -->
            <div class="text-center py-8">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-4">
                    <span class="text-white/90 text-sm font-medium">🏆 Panel użytkownika</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                    Witaj, <span class="text-yellow-300">{{ auth()->user()->profile?->first_name ?? auth()->user()->name }}</span>! 👋
                </h1>

                <p class="text-lg text-white/90 max-w-2xl mx-auto mb-8">
                    @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                        Zarządzaj swoimi pupilami i świadcz usługi opieki
                    @elseif(auth()->user()->isOwner())
                        Znajdź najlepszą opiekę dla swoich pupili
                    @elseif(auth()->user()->isSitter())
                        Świadcz usługi opieki nad zwierzętami
                    @else
                        Uzupełnij swój profil, aby zacząć korzystać z platformy
                    @endif
                </p>

                <!-- Hero Stats -->
                @if(auth()->user()->isOwner() || auth()->user()->isSitter())
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl mx-auto">
                        @if(auth()->user()->isOwner())
                            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->pets()->count() }}</div>
                                <div class="text-white/70 text-sm">Moje zwierzęta</div>
                            </div>
                        @endif

                        @if(auth()->user()->isSitter())
                            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->services()->count() }}</div>
                                <div class="text-white/70 text-sm">Usługi</div>
                            </div>
                        @endif

                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->ownerBookings()->count() + auth()->user()->sitterBookings()->count() }}</div>
                            <div class="text-white/70 text-sm">Rezerwacje</div>
                        </div>

                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->reviewsReceived()->count() }}</div>
                            <div class="text-white/70 text-sm">Opinie</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content with Glass Cards -->
    <div class="bg-white dark:bg-gray-900 rounded-t-3xl relative z-20 -mt-8">
        <div class="pt-8 pb-8 space-y-6">

            <!-- Quick Actions Glass Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- My Pets Action -->
                <a href="{{ route('pets.index') }}" class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🐕‍🦺</div>
                    <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Moje zwierzęta</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Zarządzaj pupilami</p>
                </a>

                <!-- Gallery Action -->
                <a href="{{ route('gallery.index') }}" class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">📸</div>
                    <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Galeria</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Zdjęcia pupili</p>
                </a>

                <!-- Services Action -->
                @if(auth()->user()->isSitter())
                    <a href="{{ route('sitter-services.index') }}" class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl">
                        <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🔧</div>
                        <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Usługi</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Zarządzaj ofertą</p>
                    </a>
                @else
                    <a href="/search" class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl">
                        <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🔍</div>
                        <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Szukaj</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Znajdź opiekuna</p>
                    </a>
                @endif

                <!-- Events Action -->
                <a href="{{ route('events.index') }}" class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-xl">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎉</div>
                    <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Wydarzenia</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Spotkania i eventy</p>
                </a>
            </div>

            <!-- Main Content Grid with Glass Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - 2/3 width -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Welcome Message -->
                    <x-ui.card variant="default" :shadow="true" class="backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Szybkie akcje
                            </h3>
                        </x-slot>
                        <div class="space-y-4">
                            <p class="text-gray-600 dark:text-gray-400">Wybierz jedną z dostępnych opcji:</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <x-ui.button variant="primary" fullWidth="true">
                                    <a href="{{ route('pets.create') }}" class="flex items-center justify-center w-full h-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Dodaj zwierzę
                                    </a>
                                </x-ui.button>

                                @if(auth()->user()->isSitter())
                                    <x-ui.button variant="secondary" fullWidth="true">
                                        <a href="{{ route('sitter-services.create') }}" class="flex items-center justify-center w-full h-full">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                            </svg>
                                            Dodaj usługę
                                        </a>
                                    </x-ui.button>
                                @else
                                    <x-ui.button variant="secondary" fullWidth="true">
                                        <a href="/search" class="flex items-center justify-center w-full h-full">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            Znajdź opiekuna
                                        </a>
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>
                    </x-ui.card>

                    <!-- Stats Overview -->
                    <x-ui.card variant="default" :shadow="true" class="backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Przegląd aktywności
                            </h3>
                        </x-slot>
                        <div class="space-y-4">
                            @if(auth()->user()->isOwner())
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">Moje zwierzęta</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->pets()->count() }}</span>
                                </div>
                            @endif

                            @if(auth()->user()->isSitter())
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">Moje usługi</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->services()->count() }}</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Wszystkie rezerwacje</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->ownerBookings()->count() + auth()->user()->sitterBookings()->count() }}</span>
                            </div>

                            <div class="flex items-center justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Otrzymane opinie</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->reviewsReceived()->count() }}</span>
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                <!-- Right Column - 1/3 width -->
                <div class="space-y-6">
                    <!-- Profile Summary -->
                    <x-ui.card variant="default" :shadow="true" class="backdrop-blur-md bg-gradient-to-br from-white/95 to-purple-50/95 dark:from-gray-800/95 dark:to-purple-900/20">
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Twój profil
                            </h3>
                        </x-slot>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">
                                        @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                                            Właściciel & Opiekun
                                        @elseif(auth()->user()->isOwner())
                                            Właściciel zwierząt
                                        @elseif(auth()->user()->isSitter())
                                            Opiekun zwierząt
                                        @else
                                            Nowy użytkownik
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Członek od:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">{{ auth()->user()->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-ui.button variant="outline" fullWidth="true">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-center w-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edytuj profil
                                    </a>
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>

                    <!-- Become Pet Sitter (for non-sitters) -->
                    @if(!auth()->user()->isSitter())
                        <x-ui.card variant="warning" :shadow="true" class="backdrop-blur-md bg-gradient-to-br from-yellow-50/95 to-orange-50/95 dark:from-yellow-900/20 dark:to-orange-900/20">
                            <x-slot name="header">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Zostań opiekunem
                                </h3>
                            </x-slot>
                            <div class="space-y-4">
                                <p class="text-gray-700 dark:text-gray-300 text-sm">
                                    Dołącz do naszej społeczności opiekunów i zarabiaj opiekując się zwierzętami.
                                </p>

                                <div class="bg-yellow-100 dark:bg-yellow-900/20 rounded-lg p-3">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">40-80 zł/godz</div>
                                        <div class="text-xs text-yellow-600 dark:text-yellow-500">Średnie zarobki</div>
                                    </div>
                                </div>

                                <x-ui.button variant="warning" fullWidth="true">
                                    <a href="/register?type=sitter" class="flex items-center justify-center w-full">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                        </svg>
                                        Dołącz jako opiekun
                                    </a>
                                </x-ui.button>
                            </div>
                        </x-ui.card>
                    @endif

                    <!-- Quick Links -->
                    <x-ui.card variant="default" :shadow="true" class="backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                Szybkie linki
                            </h3>
                        </x-slot>
                        <div class="space-y-2">
                            <a href="{{ route('pets.index') }}" class="flex items-center py-2 px-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Moje zwierzęta
                            </a>

                            <a href="{{ route('gallery.index') }}" class="flex items-center py-2 px-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Galeria zdjęć
                            </a>

                            @if(auth()->user()->isSitter())
                                <a href="{{ route('sitter-services.index') }}" class="flex items-center py-2 px-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                    </svg>
                                    Oferta
                                </a>
                            @else
                                <a href="/search" class="flex items-center py-2 px-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Znajdź opiekuna
                                </a>
                            @endif

                            <a href="{{ route('events.index') }}" class="flex items-center py-2 px-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Wydarzenia
                            </a>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>

        <!-- Welcome Message for New Users -->
        @if(!auth()->user()->profile?->bio || (!auth()->user()->isOwner() && !auth()->user()->isSitter()))
            <div class="mt-6">
                <x-ui.card variant="primary" :shadow="true" class="backdrop-blur-md bg-gradient-to-br from-blue-50/95 to-indigo-50/95 dark:from-blue-900/20 dark:to-indigo-900/20">
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Witaj w PetHelp!
                        </h3>
                    </x-slot>
                    <div class="space-y-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            Uzupełnij swój profil i zacznij korzystać z pełni możliwości platformy.
                        </p>

                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Dodaj informacje o sobie
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Zarejestruj swoje zwierzęta
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Znajdź najlepszych opiekunów
                            </div>
                        </div>

                        <x-ui.button variant="primary" fullWidth="true">
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Uzupełnij profil
                            </a>
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Dashboard interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                Livewire.emit('refreshDashboard');
            }, 300000);

            // Handle dashboard notifications
            window.addEventListener('dashboard-notification', event => {
                // You can add custom notification handling here
                console.log('Dashboard notification:', event.detail);
            });
        });
    </script>

    <style>
        /* Glass card effects similar to search page */
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

        /* Enhanced card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Hero-style animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Floating animations from search page */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
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
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .transition-all,
            .transition-colors,
            .animate-float {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
    @endpush
</x-dashboard-layout>