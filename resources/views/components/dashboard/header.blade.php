@php
    $user = auth()->user();
    $unreadNotifications = $user->notifications()->unread()->count();
    $unreadMessages = $user->getUnreadMessagesCount();
@endphp

<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <div class="flex items-center lg:hidden">
            <button type="button"
                    x-data
                    x-on:click="$store.mobileMenu.toggle()"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                <span class="sr-only">Otwórz menu główne</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Page title or breadcrumb -->
        <div class="flex-1 min-w-0 px-4 lg:px-0">
            <div class="flex items-center">
                @if(View::hasSection('header-title'))
                    @yield('header-title')
                @else
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                        @switch(Route::currentRouteName())
                            @case('dashboard')
                                Dashboard
                                @break
                            @case('profile.edit')
                                Mój profil
                                @break
                            @case('bookings')
                                Moje zlecenia
                                @break
                            @case('pets.index')
                                Moje pupile
                                @break
                            @case('sitter-services.index')
                                Oferta
                                @break
                            @case('availability.calendar')
                                Kalendarz
                                @break
                            @case('reviews')
                                Oceny
                                @break
                            @case('chat')
                                Wiadomości
                                @break
                            @case('notifications')
                                Powiadomienia
                                @break
                            @case('search')
                                Znajdź opiekuna
                                @break
                            @default
                                PetHelp
                        @endswitch
                    </h1>
                @endif
            </div>
        </div>

        <!-- Right side actions -->
        <div class="flex items-center space-x-4">
            <!-- Quick actions based on user type -->
            @if(auth()->user()->isOwner())
                <a href="{{ route('search') }}"
                   class="hidden sm:inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                    <span class="mr-2">🔍</span>
                    Znajdź opiekuna
                </a>
            @endif


            <!-- Notifications -->
            <div class="relative">
                <a href="{{ route('notifications') }}"
                   class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full transition-colors duration-200">
                    <span class="sr-only">Powiadomienia</span>
                    <div class="relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unreadNotifications > 0)
                            <x-ui.badge variant="notification" size="dot" class="absolute -top-1 -right-1"></x-ui.badge>
                        @endif
                    </div>
                </a>
            </div>

            <!-- Messages -->
            <div class="relative">
                <a href="{{ route('chat') }}"
                   class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full transition-colors duration-200">
                    <span class="sr-only">Wiadomości</span>
                    <div class="relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @if($unreadMessages > 0)
                            <x-ui.badge variant="notification" size="dot" class="absolute -top-1 -right-1"></x-ui.badge>
                        @endif
                    </div>
                </a>
            </div>

            <!-- User menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        id="user-menu-button">
                    @if($user->profile?->avatar_url)
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </button>

                <!-- User dropdown -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 focus:outline-none z-50">
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}"
                           class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                            <span class="mr-3">👤</span>
                            Mój profil
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                            <span class="mr-3">📊</span>
                            Dashboard
                        </a>
                    </div>
                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                                <span class="mr-3">🚪</span>
                                Wyloguj się
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>