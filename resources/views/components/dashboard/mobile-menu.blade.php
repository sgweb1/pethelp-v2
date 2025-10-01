@php
    $user = auth()->user();
    $isOwner = $user->isOwner();
    $isSitter = $user->isSitter();

    $menuItems = [
        [
            'name' => 'Dashboard',
            'icon' => '📊',
            'route' => 'profile.dashboard',
            'active' => request()->routeIs('profile.dashboard'),
        ],
        [
            'name' => 'Mój profil',
            'icon' => '👤',
            'route' => 'profile.edit',
            'active' => request()->routeIs('profile.*'),
        ]
    ];

    if ($isOwner) {
        $menuItems = array_merge($menuItems, [
            [
                'name' => 'Moje zlecenia',
                'icon' => '📋',
                'route' => 'profile.bookings',
                'active' => request()->routeIs('profile.bookings*'),
                'badge' => $user->ownerBookings()->whereIn('status', ['pending'])->count()
            ],
            [
                'name' => 'Moje pupile',
                'icon' => '🐾',
                'route' => 'profile.pets.index',
                'active' => request()->routeIs('pets.*'),
            ],
            [
                'name' => 'Znajdź opiekuna',
                'icon' => '🔍',
                'route' => 'search',
                'active' => request()->routeIs('search*'),
            ]
        ]);
    }

    if ($isSitter) {
        $menuItems = array_merge($menuItems, [
            [
                'name' => 'Moje zlecenia',
                'icon' => '📋',
                'route' => 'profile.bookings',
                'active' => request()->routeIs('profile.bookings*'),
                'badge' => $user->sitterBookings()->whereIn('status', ['pending'])->count()
            ],
            [
                'name' => 'Moje usługi',
                'icon' => '🛠️',
                'route' => 'profile.services.index',
                'active' => request()->routeIs('sitter-services.*'),
            ],
            [
                'name' => 'Kalendarz',
                'icon' => '📅',
                'route' => 'profile.availability',
                'active' => request()->routeIs('availability.*'),
            ]
        ]);
    }

    $menuItems = array_merge($menuItems, [
        [
            'name' => 'Oceny',
            'icon' => '⭐',
            'route' => 'profile.reviews',
            'active' => request()->routeIs('profile.reviews*'),
        ],
        [
            'name' => 'Wiadomości',
            'icon' => '💬',
            'route' => 'profile.chat.index',
            'active' => request()->routeIs('profile.chat*'),
            'badge' => $user->getUnreadMessagesCount()
        ],
        [
            'name' => 'Powiadomienia',
            'icon' => '🔔',
            'route' => 'profile.notifications',
            'active' => request()->routeIs('profile.notifications*'),
            'badge' => $user->notifications()->unread()->count()
        ]
    ]);
@endphp

<!-- Mobile menu overlay -->
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

    <!-- Overlay background -->
    <div x-show="$store.mobileMenu.open"
         @click="$store.mobileMenu.close()"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

    <!-- Mobile menu -->
    <div x-show="$store.mobileMenu.open"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 shadow-xl">

        <!-- Close button -->
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="$store.mobileMenu.close()"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">Zamknij menu</span>
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile menu content -->
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center px-4 mb-5">
                <div class="flex items-center">
                    <div class="w-8 h-8 mr-3 bg-primary-600 dark:bg-primary-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-lg">🐾</span>
                    </div>
                    <span class="text-xl font-bold text-primary-700 dark:text-primary-300">PetHelp</span>
                </div>
            </div>

            <!-- User info -->
            <div class="px-4 mb-5">
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
                    <div class="ml-3">
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $user->profile?->first_name ?? $user->name }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
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
            <nav class="px-2 space-y-1">
                @foreach($menuItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @click="$store.mobileMenu.close()"
                       class="group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors duration-200
                              {{ $item['active']
                                 ? 'bg-primary-100 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                 : 'text-gray-900 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <span class="mr-4 text-lg">{{ $item['icon'] }}</span>
                        <span class="flex-1">{{ $item['name'] }}</span>
                        @if(isset($item['badge']) && $item['badge'] > 0)
                            <x-ui.badge variant="danger" size="xs" class="ml-auto">
                                {{ $item['badge'] }}
                            </x-ui.badge>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Bottom section -->
        <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Dark Mode Toggle -->
            <div class="mb-3">
                <x-dark-mode-toggle size="sm" />
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="group flex items-center w-full px-2 py-2 text-base font-medium text-gray-900 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <span class="mr-4 text-lg">🚪</span>
                    <span>Wyloguj się</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alpine.js mobile menu store -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('mobileMenu', {
        open: false,

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        }
    });
});
</script>