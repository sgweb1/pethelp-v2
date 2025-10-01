{{--
    Panel szybkich akcji dla dashboard PetHelp

    Adaptacyjny panel akcji dostosowujący się do roli użytkownika.
    Wyświetla najważniejsze funkcjonalności w formie kart akcji
    z możliwością wyróżnienia najważniejszych elementów.

    @param string $title - Tytuł panelu (opcjonalny)
    @param array $actions - Tablica akcji do wyświetlenia
    @param string $layout - Układ siatki ('grid-2'|'grid-3'|'grid-4'|'auto')
    @param bool $showStats - Czy pokazać sekcję statystyk na dole
--}}
@props([
    'title' => 'Szybkie akcje',
    'actions' => [],
    'layout' => 'auto',
    'showStats' => true
])

@php
/**
 * Generowanie akcji na podstawie roli użytkownika jeśli nie podano explicite.
 * System automatycznie dostosowuje akcje do uprawnień.
 */
if (empty($actions)) {
    $user = auth()->user();
    $actions = [];

    // Akcje dla właścicieli zwierząt
    if ($user->isOwner()) {
        $actions = array_merge($actions, [
            [
                'title' => 'Znajdź opiekuna',
                'description' => 'Wyszukaj usługi opieki w Twojej okolicy',
                'icon' => '🔍',
                'route' => 'search',
                'color' => 'primary',
                'featured' => true,
                'priority' => 1
            ],
            [
                'title' => 'Dodaj pupila',
                'description' => 'Zarejestruj nowe zwierzę w systemie',
                'icon' => '🐾',
                'route' => 'profile.pets.create',
                'color' => 'green',
                'priority' => 2
            ],
            [
                'title' => 'Moje rezerwacje',
                'description' => 'Zarządzaj aktywnymi zleceniami',
                'icon' => '📋',
                'route' => 'profile.bookings',
                'color' => 'blue',
                'badge' => $user->ownerBookings()->whereIn('status', ['pending', 'confirmed'])->count(),
                'priority' => 3
            ]
        ]);
    }

    // Akcje dla opiekunów zwierząt
    if ($user->isSitter()) {
        $actions = array_merge($actions, [
            [
                'title' => 'Dodaj usługę',
                'description' => 'Stwórz nową ofertę opieki',
                'icon' => '✨',
                'route' => 'profile.services.create',
                'color' => 'primary',
                'featured' => true,
                'priority' => 1
            ],
            [
                'title' => 'Moje usługi',
                'description' => 'Zarządzaj swoją ofertą',
                'icon' => '🛠️',
                'route' => 'profile.services.index',
                'color' => 'purple',
                'badge' => $user->services()->where('is_active', true)->count(),
                'priority' => 2
            ],
            [
                'title' => 'Kalendarz',
                'description' => 'Ustaw swoją dostępność',
                'icon' => '📅',
                'route' => 'profile.availability',
                'color' => 'green',
                'priority' => 3
            ],
            [
                'title' => 'Zlecenia',
                'description' => 'Przegląj aktywne rezerwacje',
                'icon' => '📋',
                'route' => 'profile.bookings',
                'color' => 'blue',
                'badge' => $user->sitterBookings()->whereIn('status', ['pending'])->count(),
                'priority' => 4
            ]
        ]);
    }

    // Wspólne akcje dla wszystkich użytkowników
    $commonActions = [
        [
            'title' => 'Wiadomości',
            'description' => 'Komunikacja z użytkownikami',
            'icon' => '💬',
            'route' => 'profile.chat.index',
            'color' => 'indigo',
            'badge' => $user->getUnreadMessagesCount(),
            'priority' => 10
        ],
        [
            'title' => 'Galeria',
            'description' => 'Zarządzaj zdjęciami zwierząt',
            'icon' => '📸',
            'route' => 'profile.gallery.index',
            'color' => 'yellow',
            'priority' => 11
        ]
    ];

    // Sortowanie według priorytetu
    $actions = collect(array_merge($actions, $commonActions))
        ->sortBy('priority')
        ->values()
        ->toArray();
}

/**
 * Określanie układu siatki na podstawie liczby akcji i preferencji.
 */
$gridClasses = match($layout) {
    'grid-2' => 'grid-cols-1 sm:grid-cols-2',
    'grid-3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    'grid-4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    'auto' => count($actions) <= 2 ? 'grid-cols-1 sm:grid-cols-2'
            : (count($actions) <= 4 ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
            : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'),
    default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
};

/**
 * Definiowanie wariantów kolorystycznych dla akcji.
 */
$colorVariants = [
    'primary' => [
        'bg' => 'bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/30',
        'border' => 'border-primary-200 dark:border-primary-700',
        'button' => 'bg-primary-600 hover:bg-primary-700 text-white',
        'icon' => 'bg-primary-100 dark:bg-primary-800/50 text-primary-600 dark:text-primary-400'
    ],
    'blue' => [
        'bg' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/30',
        'border' => 'border-blue-200 dark:border-blue-700',
        'button' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'icon' => 'bg-blue-100 dark:bg-blue-800/50 text-blue-600 dark:text-blue-400'
    ],
    'green' => [
        'bg' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/30',
        'border' => 'border-green-200 dark:border-green-700',
        'button' => 'bg-green-600 hover:bg-green-700 text-white',
        'icon' => 'bg-green-100 dark:bg-green-800/50 text-green-600 dark:text-green-400'
    ],
    'purple' => [
        'bg' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/30',
        'border' => 'border-purple-200 dark:border-purple-700',
        'button' => 'bg-purple-600 hover:bg-purple-700 text-white',
        'icon' => 'bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400'
    ],
    'indigo' => [
        'bg' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/30',
        'border' => 'border-indigo-200 dark:border-indigo-700',
        'button' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'icon' => 'bg-indigo-100 dark:bg-indigo-800/50 text-indigo-600 dark:text-indigo-400'
    ],
    'yellow' => [
        'bg' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/30',
        'border' => 'border-yellow-200 dark:border-yellow-700',
        'button' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'icon' => 'bg-yellow-100 dark:bg-yellow-800/50 text-yellow-600 dark:text-yellow-400'
    ]
];
@endphp

<div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-soft border border-white/20 dark:border-gray-700/50"
     {{ $attributes }}>

    {{-- Header panelu --}}
    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                {{ $title }}
            </h3>

            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ count($actions) }} {{ count($actions) === 1 ? 'akcja' : 'akcji' }}
            </span>
        </div>
    </div>

    {{-- Grid akcji --}}
    <div class="p-6">
        <div class="grid {{ $gridClasses }} gap-4">
            @foreach($actions as $action)
                @php
                    $colors = $colorVariants[$action['color']] ?? $colorVariants['primary'];
                    $isFeatured = isset($action['featured']) && $action['featured'];
                @endphp

                <div class="group relative {{ $isFeatured ? 'sm:col-span-2 lg:col-span-1' : '' }}">
                    <a href="{{ route($action['route']) }}"
                       class="block h-full p-5 rounded-xl border-2 border-transparent {{ $colors['bg'] }} hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] transform">

                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                {{-- Ikona akcji --}}
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 {{ $colors['icon'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-xl">{{ $action['icon'] }}</span>
                                    </div>
                                </div>

                                {{-- Tekst akcji --}}
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $action['title'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        {{ $action['description'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Badge z liczbą --}}
                            @if(isset($action['badge']) && $action['badge'] > 0)
                                <x-ui.badge variant="notification" size="xs" class="flex-shrink-0">
                                    {{ $action['badge'] }}
                                </x-ui.badge>
                            @endif
                        </div>

                        {{-- Przycisk featured --}}
                        @if($isFeatured)
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium {{ $colors['button'] }} transition-all duration-200 group-hover:shadow-md">
                                    Rozpocznij teraz
                                    <svg class="ml-1.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                        @endif

                        {{-- Wskaźnik hover --}}
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Sekcja statystyk aktywności --}}
        @if($showStats)
            <div class="mt-6 pt-6 border-t border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4 text-gray-500 dark:text-gray-400">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Ostatnia aktywność: {{ now()->diffForHumans() }}
                        </span>
                    </div>

                    <a href="{{ route('profile.dashboard') }}"
                       class="flex items-center text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                        <span class="mr-1">Odśwież dane</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>