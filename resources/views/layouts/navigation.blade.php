<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Menu dla wszystkich -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile*')">
                        Profil
                    </x-nav-link>
                    <x-nav-link :href="route('search')" :active="request()->routeIs('search*')">
                        Usługi specjalistyczne
                    </x-nav-link>
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events*')">
                        Wydarzenia
                    </x-nav-link>
                    <x-nav-link :href="route('advertisements.index')" :active="request()->routeIs('advertisements*')">
                        Ogłoszenia
                    </x-nav-link>
                    <x-nav-link :href="route('reviews')" :active="request()->routeIs('reviews*')">
                        Opinie
                    </x-nav-link>

                    <!-- Separator -->
                    @if(Auth::check() && Auth::user()->isSitter())
                        <div class="border-l border-gray-300 mx-4 h-6 self-center"></div>

                        <!-- Menu tylko dla pet sitterów -->
                        <x-nav-link :href="route('sitter-services.index')" :active="request()->routeIs('sitter-services*')">
                            Oferta
                        </x-nav-link>
                        <x-nav-link :href="route('bookings')" :active="request()->routeIs('bookings*')" class="relative">
                            Zlecenia
                            @php
                                $newBookingsCount = Auth::user()->newBookingsCount ?? 0;
                            @endphp
                            @if($newBookingsCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $newBookingsCount }}
                                </span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('availability.calendar')" :active="request()->routeIs('availability*')">
                            Kalendarz
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications Icon -->
                <a href="{{ route('notifications') }}" class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition duration-150 ease-in-out mr-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if(Auth::check() && Auth::user()->notifications()->unread()->count() > 0)
                        <x-ui.badge variant="notification-red" size="icon" class="absolute top-0 right-0 transform translate-x-1 -translate-y-1">
                            {{ Auth::user()->notifications()->unread()->count() }}
                        </x-ui.badge>
                    @endif
                </a>

                <!-- Messages Icon -->
                <a href="{{ route('chat') }}" class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition duration-150 ease-in-out mr-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    @if(Auth::check() && Auth::user()->getUnreadMessagesCount() > 0)
                        <x-ui.badge variant="notification-green" size="icon" class="absolute top-0 right-0 transform translate-x-1 -translate-y-1">
                            {{ Auth::user()->getUnreadMessagesCount() }}
                        </x-ui.badge>
                    @endif
                </a>

                <!-- Language Switcher -->
                <livewire:language-switcher />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('ui.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('ui.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Menu dla wszystkich -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile*')">
                Profil
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search*')">
                Usługi specjalistyczne
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events*')">
                Wydarzenia
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('advertisements.index')" :active="request()->routeIs('advertisements*')">
                Ogłoszenia
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reviews')" :active="request()->routeIs('reviews*')">
                Opinie
            </x-responsive-nav-link>

            <!-- Separator dla pet sitterów -->
            @if(Auth::check() && Auth::user()->isSitter())
                <div class="border-t border-gray-200 my-2"></div>

                <!-- Menu tylko dla pet sitterów -->
                <x-responsive-nav-link :href="route('sitter-services.index')" :active="request()->routeIs('sitter-services*')">
                    Oferta
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings')" :active="request()->routeIs('bookings*')" class="relative">
                    Zlecenia
                    @php
                        $newBookingsCount = Auth::user()->newBookingsCount ?? 0;
                    @endphp
                    @if($newBookingsCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">
                            {{ $newBookingsCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('availability.calendar')" :active="request()->routeIs('availability*')">
                    Kalendarz
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>

                <!-- Mobile Language Switcher -->
                <div class="mt-3 flex justify-start">
                    <livewire:language-switcher />
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('ui.profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('ui.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
