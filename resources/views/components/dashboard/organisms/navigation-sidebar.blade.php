{{--
    Komponent sidebaru nawigacyjnego dla dashboard PetHelp

    Adaptacyjny sidebar dostosowujący się do roli użytkownika z sekcjami
    dla właścicieli i opiekunów zwierząt. Obsługuje aktywne stany,
    badge'y powiadomień i responsywność mobilną.

    @param bool $mobile - Czy renderować wersję mobilną
    @param bool $collapsible - Czy sidebar może być zwijany
    @param string $variant - Wariant wizualny ('default'|'compact'|'minimal')
--}}
@props([
    'mobile' => false,
    'collapsible' => true,
    'variant' => 'default'
])

@php
/**
 * Pobieranie danych użytkownika i konfiguracji menu.
 */
$user = auth()->user();
$isOwner = $user->isOwner();
$isSitter = $user->isSitter();

/**
 * Menu główne dla wszystkich użytkowników.
 */
$mainMenuItems = [
    [
        'name' => 'Dashboard',
        'icon' => '📊',
        'route' => 'profile.dashboard',
        'active' => request()->routeIs('profile.dashboard'),
        'description' => 'Przegląd ogólny',
        'priority' => 1
    ],
    [
        'name' => 'Profil',
        'icon' => '👤',
        'route' => 'profile.edit',
        'active' => request()->routeIs('profile.edit'),
        'description' => 'Zarządzaj profilem',
        'priority' => 2
    ]
];

/**
 * Menu dla właścicieli zwierząt.
 */
$ownerMenuItems = [];
if ($isOwner) {
    $ownerMenuItems = [
        [
            'name' => 'Moje zwierzęta',
            'icon' => '🐾',
            'route' => 'profile.pets.index',
            'active' => request()->routeIs('profile.pets.*'),
            'description' => 'Zarządzaj pupilami',
            'badge' => $user->pets()->count(),
            'show_add_button' => true,
            'add_route' => 'profile.pets.create',
            'add_tooltip' => 'Dodaj nowe zwierzę',
            'priority' => 10
        ],
        [
            'name' => 'Galeria',
            'icon' => '📸',
            'route' => 'profile.gallery.index',
            'active' => request()->routeIs('profile.gallery.*'),
            'description' => 'Zdjęcia pupili',
            'show_add_button' => true,
            'add_route' => 'profile.gallery.upload',
            'add_tooltip' => 'Dodaj zdjęcie',
            'priority' => 11
        ],
        [
            'name' => 'Znajdź opiekuna',
            'icon' => '🔍',
            'route' => 'search',
            'active' => request()->routeIs('search*'),
            'description' => 'Wyszukaj usługi',
            'priority' => 12
        ]
    ];
}

/**
 * Menu dla opiekunów zwierząt.
 */
$sitterMenuItems = [];
if ($isSitter) {
    $sitterMenuItems = [
        [
            'name' => 'Moje usługi',
            'icon' => '🛠️',
            'route' => 'profile.services.index',
            'active' => request()->routeIs('profile.services.*'),
            'description' => 'Zarządzaj ofertami',
            'badge' => $user->services()->where('is_active', true)->count(),
            'show_add_button' => true,
            'add_route' => 'profile.services.create',
            'add_tooltip' => 'Dodaj nową usługę',
            'priority' => 20
        ],
        [
            'name' => 'Kalendarz',
            'icon' => '📅',
            'route' => 'profile.availability',
            'active' => request()->routeIs('profile.availability*'),
            'description' => 'Dostępność',
            'priority' => 21
        ],
        [
            'name' => 'Zlecenia',
            'icon' => '📋',
            'route' => 'profile.bookings',
            'active' => request()->routeIs('profile.bookings*'),
            'description' => 'Zarządzaj rezerwacjami',
            'badge' => $user->sitterBookings()->whereIn('status', ['pending'])->count(),
            'priority' => 22
        ]
    ];
}

/**
 * Menu wspólne dla wszystkich użytkowników.
 */
$commonMenuItems = [
    [
        'name' => 'Wiadomości',
        'icon' => '💬',
        'route' => 'profile.chat.index',
        'active' => request()->routeIs('profile.chat.*'),
        'description' => 'Komunikacja',
        'badge' => $user->getUnreadMessagesCount(),
        'priority' => 30
    ],
    [
        'name' => 'Powiadomienia',
        'icon' => '🔔',
        'route' => 'profile.notifications',
        'active' => request()->routeIs('profile.notifications*'),
        'description' => 'Centrum powiadomień',
        'badge' => $user->notifications()->unread()->count(),
        'priority' => 31
    ],
    [
        'name' => 'Wydarzenia',
        'icon' => '🎉',
        'route' => 'events.index',
        'active' => request()->routeIs('events*'),
        'description' => 'Spotkania społeczności',
        'show_add_button' => true,
        'add_route' => 'profile.events.create',
        'add_tooltip' => 'Dodaj wydarzenie',
        'priority' => 32
    ],
    [
        'name' => 'Ogłoszenia',
        'icon' => '📢',
        'route' => 'advertisements.index',
        'active' => request()->routeIs('advertisements*'),
        'description' => 'Marketplace zwierząt',
        'show_add_button' => true,
        'add_route' => 'profile.advertisements.create',
        'add_tooltip' => 'Dodaj ogłoszenie',
        'priority' => 33
    ],
    [
        'name' => 'Opinie',
        'icon' => '⭐',
        'route' => 'profile.reviews',
        'active' => request()->routeIs('profile.reviews*'),
        'description' => 'Opinie i oceny',
        'priority' => 34
    ]
];

/**
 * Łączenie i sortowanie wszystkich elementów menu.
 */
$allMenuItems = collect(array_merge($mainMenuItems, $ownerMenuItems, $commonMenuItems))
    ->sortBy('priority')
    ->values()
    ->toArray();

/**
 * Warianty stylów sidebar.
 */
$variants = [
    'default' => 'w-64',
    'compact' => 'w-56',
    'minimal' => 'w-16'
];

$sidebarWidth = $variants[$variant] ?? $variants['default'];
$isMinimal = $variant === 'minimal';
@endphp

@if($mobile)
    {{-- Wersja mobilna --}}
    <div x-data
         x-show="$store.mobileMenu.open"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex lg:hidden"
         style="display: none;">

        {{-- Overlay --}}
        <div x-show="$store.mobileMenu.open"
             @click="$store.mobileMenu.close()"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

        {{-- Mobile sidebar --}}
        <div x-show="$store.mobileMenu.open"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 shadow-xl">

            {{-- Close button --}}
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="$store.mobileMenu.close()"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Zamknij menu</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <x-dashboard.molecules.sidebar-content
                :menuItems="$allMenuItems"
                :sitterMenuItems="$sitterMenuItems"
                :user="$user"
                :mobile="true" />
        </div>
    </div>
@else
    {{-- Wersja desktop --}}
    <div class="hidden lg:flex lg:flex-shrink-0"
         x-data="{ collapsed: false }"
         {{ $attributes }}>
        <div class="flex flex-col {{ $collapsible && $isMinimal ? 'w-16 hover:w-64 transition-all duration-300' : $sidebarWidth }}">
            <div class="flex flex-col h-full bg-white dark:bg-gray-800 shadow-xl border-r border-gray-200 dark:border-gray-700">
                <x-dashboard.molecules.sidebar-content
                    :menuItems="$allMenuItems"
                    :sitterMenuItems="$sitterMenuItems"
                    :user="$user"
                    :minimal="$isMinimal"
                    :mobile="false" />
            </div>
        </div>
    </div>
@endif