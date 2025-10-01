<div>
    <!-- Hero-style Background -->
    <div class="relative -m-4 sm:-m-6 lg:-m-8 mb-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="overviewpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#overviewpattern)"/>
            </svg>
        </div>

        <div class="relative z-10 p-4 sm:p-6 lg:p-8 space-y-6">
            <!-- Welcome Header -->
            <div class="text-center py-8">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-4">
                    <span class="text-white/90 text-sm font-medium">🏆 Panel użytkownika</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                    Witaj, <span class="text-yellow-300">{{ auth()->user()->profile?->first_name ?? auth()->user()->name }}</span>! 👋
                </h1>

                <p class="text-lg text-white/90 max-w-2xl mx-auto mb-8">
                    @if(auth()->user()->isSitter() && auth()->user()->isOwner())
                        Zarządzaj swoimi pupilami i świadcz usługi opieki
                    @elseif(auth()->user()->isSitter())
                        Świadcz usługi opieki nad zwierzętami
                    @elseif(auth()->user()->isOwner())
                        Znajdź najlepszą opiekę dla swoich pupili
                    @else
                        Uzupełnij swój profil, aby zacząć korzystać z platformy
                    @endif
                </p>

                <!-- Hero Stats -->
                @php $stats = $this->quickStats; @endphp
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <!-- Pets Count -->
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $stats['pets_count'] }}</div>
                        <div class="text-white/70 text-sm">Moje zwierzęta</div>
                    </div>

                    @if(auth()->user()->isSitter())
                        <!-- Active Services -->
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $stats['active_services'] }}</div>
                            <div class="text-white/70 text-sm">Aktywne usługi</div>
                        </div>

                        <!-- Upcoming Bookings -->
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $stats['upcoming_bookings'] }}</div>
                            <div class="text-white/70 text-sm">Nadchodzące zlecenia</div>
                        </div>

                        <!-- Rating -->
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">
                                {{ $stats['rating'] ? number_format($stats['rating'], 1) : '—' }}
                            </div>
                            <div class="text-white/70 text-sm">Średnia ocena</div>
                        </div>
                    @else
                        <!-- Advertisements -->
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->advertisements()->count() }}</div>
                            <div class="text-white/70 text-sm">Moje ogłoszenia</div>
                        </div>

                        <!-- Events -->
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ auth()->user()->events()->count() }}</div>
                            <div class="text-white/70 text-sm">Wydarzenia</div>
                        </div>

                        <!-- Become Pet Sitter CTA -->
                        <div class="bg-gradient-to-br from-green-500/80 to-emerald-600/80 backdrop-blur-md rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-white mb-1">💰</div>
                            <div class="text-white/90 text-sm">Zostań Pet Sitterem</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content with Glass Cards -->
    <div class="bg-white dark:bg-gray-900 rounded-t-3xl relative z-20 -mt-8">
        <div class="pt-8 pb-8 space-y-6">

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php $stats = $this->quickStats; @endphp

        <!-- Pets Count -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-2xl">🐕</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Moje zwierzęta</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pets_count'] }}</p>
                </div>
            </div>
        </div>

        @if(auth()->user()->isSitter())
            <!-- Active Services -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">💼</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktywne usługi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_services'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">📅</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nadchodzące zlecenia</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['upcoming_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Rating -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Średnia ocena</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stats['rating'] ? number_format($stats['rating'], 1) : '—' }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- For non-sitters, show different stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Moje ogłoszenia</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->advertisements()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">🎉</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Wydarzenia</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->events()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Become Pet Sitter CTA -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-sm p-6 text-white">
                <div class="text-center">
                    <p class="text-sm font-medium mb-1">Zostań Pet Sitterem</p>
                    <p class="text-2xl font-bold mb-2">💰</p>
                    <a href="#" class="text-xs text-green-100 hover:text-white">Rozpocznij zarabianie →</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Moje Zwierzęta -->
            @if($this->myPetsPreview->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Moje Zwierzęta</h2>
                    <a href="{{ route('profile.pets.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Zobacz wszystkie →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($this->myPetsPreview as $pet)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($pet->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $pet->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $pet->petType?->name ?? ucfirst($pet->type) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <p>Wiek: {{ $pet->age_years ?? 'Nieznany' }} lat</p>
                                @if($pet->breed)
                                    <p>Rasa: {{ $pet->breed }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Moje Wydarzenia -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-indigo-600 dark:text-indigo-400 text-lg">🎉</span>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Moje Wydarzenia</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('events.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Zobacz wszystkie →
                        </a>
                        <div class="relative group">
                            <a href="{{ route('events.create') }}"
                               class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </a>
                            <!-- Tooltip -->
                            <div class="absolute bottom-full right-0 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                Dodaj nowe wydarzenie
                                <div class="absolute top-full right-2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-l-transparent border-r-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse(auth()->user()->events()->orderBy('event_date', 'asc')->limit(3)->get() as $event)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-indigo-600 dark:text-indigo-400">🎉</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        @if($event->location_name)
                                            📍 {{ $event->location_name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($event->event_date)
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $event->event_date->format('d.m') }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $event->start_time ? $event->start_time->format('H:i') : '' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">🎉</div>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Brak wydarzeń</p>
                            <a href="{{ route('events.create') }}" class="text-primary-600 hover:text-primary-700 text-sm">
                                Dodaj swoje pierwsze wydarzenie →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Active Bookings (Pet Sitter) -->
            @if(auth()->user()->isSitter())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aktywne Zlecenia</h2>
                    <a href="{{ route('profile.bookings') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Zobacz wszystkie →
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse(auth()->user()->sitterBookings()->where('status', 'confirmed')->limit(3)->get() as $booking)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-blue-600 dark:text-blue-400">💼</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $booking->service->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Klient: {{ $booking->user->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $booking->start_date?->format('d.m') }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $booking->start_date?->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">📅</div>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Brak aktywnych zleceń</p>
                            <a href="{{ route('services.index') }}" class="text-primary-600 hover:text-primary-700 text-sm">
                                Sprawdź swoje usługi →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Ostatnia Aktywność</h2>

                <div class="space-y-4">
                    @foreach($this->recentActivity as $activity)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/20 rounded-lg flex items-center justify-center mr-3 mt-1">
                                <span class="text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400 text-sm">
                                    {{ $activity['icon'] }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ $activity['time']->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Upcoming Events -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Nadchodzące</h3>

                <div class="space-y-3">
                    @forelse($this->upcomingEvents as $event)
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-{{ $event['color'] }}-100 dark:bg-{{ $event['color'] }}-900/20 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-{{ $event['color'] }}-600 dark:text-{{ $event['color'] }}-400 text-sm">
                                    {{ $event['icon'] }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $event['title'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $event['subtitle'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    {{ $event['date']->format('d.m') }} {{ $event['time'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="text-2xl mb-2">📅</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Brak nadchodzących wydarzeń</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Szybkie Akcje</h3>

                <div class="space-y-3">
                    <a href="{{ route('profile.pets.create') }}"
                       class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200">
                        <span class="text-purple-600 dark:text-purple-400 mr-3">🐕</span>
                        <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Dodaj zwierzę</span>
                    </a>

                    @if(auth()->user()->isSitter())
                        <a href="{{ route('profile.services.create') }}"
                           class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">
                            <span class="text-green-600 dark:text-green-400 mr-3">💼</span>
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Dodaj usługę</span>
                        </a>
                    @endif

                    <a href="{{ route('events.create') }}"
                       class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                        <span class="text-blue-600 dark:text-blue-400 mr-3">🎉</span>
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Dodaj wydarzenie</span>
                    </a>

                    <a href="{{ route('advertisements.create') }}"
                       class="flex items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors duration-200">
                        <span class="text-orange-600 dark:text-orange-400 mr-3">📢</span>
                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Dodaj ogłoszenie</span>
                    </a>
                </div>
            </div>

            <!-- Become Pet Sitter (for non-sitters) -->
            @if(!auth()->user()->isSitter())
                @livewire('dashboard.become-sitter')
            @endif
        </div>
    </div>
</div>
