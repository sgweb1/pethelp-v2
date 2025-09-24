@php
    $user = auth()->user();
    $isOwner = $user->isOwner();
    $isSitter = $user->isSitter();

    // Menu dla wszystkich użytkowników
    $menuItems = [
        [
            'name' => 'Dashboard',
            'icon' => '📊',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'description' => 'Przegląd ogólny'
        ],
        [
            'name' => 'Profil',
            'icon' => '👤',
            'route' => 'profile.edit',
            'active' => request()->routeIs('profile.*'),
            'description' => 'Zarządzaj profilem'
        ],
        [
            'name' => 'Wiadomości',
            'icon' => '💬',
            'route' => 'chat',
            'active' => request()->routeIs('chat*'),
            'description' => 'Komunikacja',
            'badge' => $user->getUnreadMessagesCount()
        ],
        [
            'name' => 'Usługi',
            'icon' => '🔍',
            'route' => 'search',
            'active' => request()->routeIs('search*'),
            'description' => 'Wyszukaj usługi profesjonalne',
            'badge' => $user->professionalServices()->count(),
            'show_add_button' => true,
            'add_route' => 'professional-services.create',
            'add_tooltip' => 'Dodaj usługę profesjonalną'
        ],
        [
            'name' => 'Wydarzenia',
            'icon' => '🎉',
            'route' => 'events.index',
            'active' => request()->routeIs('events*'),
            'description' => 'Wydarzenia społeczności',
            'badge' => $user->events()->count(),
            'show_add_button' => true,
            'add_route' => 'events.create',
            'add_tooltip' => 'Dodaj wydarzenie'
        ],
        [
            'name' => 'Ogłoszenia',
            'icon' => '📢',
            'route' => 'advertisements.index',
            'active' => request()->routeIs('advertisements*'),
            'description' => 'Marketplace zwierząt',
            'badge' => $user->advertisements()->count(),
            'show_add_button' => true,
            'add_route' => 'advertisements.create',
            'add_tooltip' => 'Dodaj ogłoszenie'
        ],
        [
            'name' => 'Opinie',
            'icon' => '⭐',
            'route' => 'reviews',
            'active' => request()->routeIs('reviews*'),
            'description' => 'Opinie i oceny'
        ]
    ];

    // Menu tylko dla pet sitterów (poniżej separatora)
    $sitterMenuItems = [];
    if ($isSitter) {
        $sitterMenuItems = [
            [
                'name' => 'Oferta',
                'icon' => '🛠️',
                'route' => 'sitter-services.index',
                'active' => request()->routeIs('sitter-services.*'),
                'description' => 'Zarządzaj usługami',
                'badge' => $user->services()->count(),
                'show_add_button' => true,
                'add_route' => 'sitter-services.create',
                'add_tooltip' => 'Dodaj usługę'
            ],
            [
                'name' => 'Zlecenia',
                'icon' => '📋',
                'route' => 'bookings',
                'active' => request()->routeIs('bookings*'),
                'description' => 'Zarządzaj rezerwacjami',
                'badge' => $user->sitterBookings()->whereIn('status', ['pending'])->count()
            ],
            [
                'name' => 'Kalendarz',
                'icon' => '📅',
                'route' => 'availability.calendar',
                'active' => request()->routeIs('availability.*'),
                'description' => 'Dostępność'
            ]
        ];
    }

@endphp

<div class="flex flex-col h-full bg-white dark:bg-gray-800 shadow-xl">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="w-8 h-8 mr-3 bg-primary-600 dark:bg-primary-500 rounded-xl flex items-center justify-center">
                <span class="text-white text-lg">🐾</span>
            </div>
            <span class="text-xl font-bold text-primary-700 dark:text-primary-300">PetHelp</span>
        </div>
    </div>

    <!-- User Profile Section -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($user->profile?->avatar_url)
                    <img class="w-10 h-10 rounded-full object-cover" src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}">
                @else
                    <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center">
                        <span class="text-white font-medium">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div class="ml-3 min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $user->profile?->first_name ?? $user->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    @if($isOwner && $isSitter)
                        Właściciel i Opiekun
                    @elseif($isOwner)
                        Właściciel pupila
                    @elseif($isSitter)
                        Opiekun zwierząt
                    @else
                        Użytkownik
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        <!-- Menu dla wszystkich -->
        @foreach($menuItems as $item)
            <div class="flex items-center space-x-1">
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 flex-1
                          {{ $item['active']
                             ? 'bg-primary-100 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                    <span class="mr-3 text-lg">{{ $item['icon'] }}</span>
                    <span class="flex-1">{{ $item['name'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <x-ui.badge variant="info" size="xs" class="ml-auto">
                            {{ $item['badge'] }}
                        </x-ui.badge>
                    @endif
                </a>

                @if(isset($item['show_add_button']) && $item['show_add_button'])
                    <div class="relative group">
                        <a href="{{ route($item['add_route']) }}"
                           class="inline-flex items-center justify-center w-6 h-6 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/20 dark:hover:bg-primary-900/30 rounded transition-colors duration-200">
                            <svg class="w-3 h-3 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </a>
                        <!-- Tooltip -->
                        <div class="absolute bottom-full right-0 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50">
                            {{ $item['add_tooltip'] }}
                            <div class="absolute top-full right-2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-l-transparent border-r-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Separator i menu dla pet sitterów -->
        @if($isSitter && count($sitterMenuItems) > 0)
            <div class="pt-6">
                <!-- Ramka dla sekcji Pet Sitter -->
                <div class="mx-2 mb-4 border border-purple-200 dark:border-purple-700 rounded-lg bg-purple-50/50 dark:bg-purple-900/20 shadow-sm">
                    <div class="px-3 py-2 border-b border-purple-200 dark:border-purple-700 bg-purple-100/30 dark:bg-purple-800/20 rounded-t-lg">
                        <h3 class="text-xs font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wider">
                            🐾 Pet Sitter
                        </h3>
                    </div>
                    <div class="p-1 space-y-0.5">
                        @foreach($sitterMenuItems as $item)
                            <div class="flex items-center space-x-1">
                                <a href="{{ route($item['route']) }}"
                                   class="group flex items-center px-2 py-1.5 text-sm font-medium rounded-md transition-colors duration-200 flex-1
                                          {{ $item['active']
                                             ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'
                                             : 'text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-700 dark:hover:text-purple-300' }}">
                                    <span class="mr-3 text-lg">{{ $item['icon'] }}</span>
                                    <span class="flex-1">{{ $item['name'] }}</span>
                                    @if(isset($item['badge']) && $item['badge'] > 0)
                                        <x-ui.badge variant="purple" size="xs" class="ml-auto">
                                            {{ $item['badge'] }}
                                        </x-ui.badge>
                                    @endif
                                </a>

                                @if(isset($item['show_add_button']) && $item['show_add_button'])
                                    <div class="relative group">
                                        <a href="{{ route($item['add_route']) }}"
                                           class="inline-flex items-center justify-center w-6 h-6 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 rounded transition-colors duration-200">
                                            <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </a>
                                        <!-- Tooltip -->
                                        <div class="absolute bottom-full right-0 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50">
                                            {{ $item['add_tooltip'] }}
                                            <div class="absolute top-full right-2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-l-transparent border-r-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </nav>

    <!-- Bottom section -->
    <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <span class="mr-3 text-lg">🚪</span>
                <span>Wyloguj się</span>
            </button>
        </form>
    </div>
</div>