{{--
    Komponent górnego paska nawigacyjnego

    Wyświetla logo, breadcrumbs lub linki nawigacyjne, toggle trybu ciemnego
    i przyciski autoryzacji. Automatycznie dostosowuje się do stron panelu
    użytkownika i publicznych stron.

    @param array|null $breadcrumbs - Opcjonalne breadcrumbs dla stron panelu
    @param bool $showBreadcrumbs - Czy pokazywać breadcrumbs zamiast zwykłej nawigacji
--}}

@props([
    'breadcrumbs' => null,
    'showBreadcrumbs' => false
])

<!-- Navigation Header -->
<header class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-soft sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="w-8 h-8 mr-3 bg-primary-600 dark:bg-primary-500 rounded-xl flex items-center justify-center">
                    <x-icon name="heart" class="w-5 h-5 text-white" />
                </div>
                <span class="text-2xl font-bold text-primary-700 dark:text-primary-300">PetHelp</span>
            </div>

            <!-- Breadcrumbs or Navigation Links -->
            <div class="hidden md:flex flex-1 justify-center">
                @if($breadcrumbs && !empty($breadcrumbs))
                    <x-breadcrumbs :items="$breadcrumbs" class="text-gray-700 dark:text-gray-300" />
                @else
                    <!-- Default Navigation Links for public pages -->
                    <div class="flex space-x-8">
                        <a href="/" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Strona główna</a>
                        <a href="#jak-to-dziala" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Jak to działa</a>
                        <a href="#opiekunowie" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Zostań opiekunem</a>
                        <a href="#kontakt" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Kontakt</a>
                    </div>
                @endif
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-3">
                <!-- Dark Mode Toggle -->
                <x-dark-mode-toggle size="sm" :show-label="false" />

                @auth
                    <x-ui.button variant="outline" size="sm" href="{{ route('profile.dashboard') }}">
                        <x-icon name="briefcase" class="w-4 h-4 mr-2" />
                        Panel
                    </x-ui.button>
                @else
                    <x-ui.button variant="ghost" size="sm" href="{{ route('login') }}">
                        Zaloguj się
                    </x-ui.button>
                    <x-ui.button variant="primary" size="sm" href="{{ route('register') }}">
                        <x-icon name="user" class="w-4 h-4 mr-2" />
                        Zarejestruj się
                    </x-ui.button>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors duration-200" x-data x-on:click="$store.mobileMenu.toggle()">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>
            </div>
        </nav>

        <!-- Mobile Navigation -->
        <div class="md:hidden" x-data x-show="$store.mobileMenu.open" x-transition>
            <div class="px-4 py-4 space-y-2 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-large mb-4">
                @if($breadcrumbs && !empty($breadcrumbs))
                    <!-- Mobile breadcrumbs -->
                    <div class="px-4 py-3">
                        <x-breadcrumbs :items="$breadcrumbs" class="text-gray-700 dark:text-gray-300" />
                    </div>
                    <hr class="my-2 border-gray-200 dark:border-gray-700">
                @else
                    <!-- Mobile navigation links -->
                    <a href="/" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Strona główna</a>
                    <a href="#jak-to-dziala" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Jak to działa</a>
                    <a href="#opiekunowie" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Zostań opiekunem</a>
                    <a href="#kontakt" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Kontakt</a>
                    <hr class="my-2 border-gray-200 dark:border-gray-700">
                @endif
                <div class="flex flex-col space-y-2 pt-2">
                    @auth
                        <x-ui.button variant="outline" size="sm" fullWidth="true" href="{{ route('profile.dashboard') }}">Panel</x-ui.button>
                    @else
                        <x-ui.button variant="ghost" size="sm" fullWidth="true" href="{{ route('login') }}">Zaloguj się</x-ui.button>
                        <x-ui.button variant="primary" size="sm" fullWidth="true" href="{{ route('register') }}">Zarejestruj się</x-ui.button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>