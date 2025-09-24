<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Mobile app meta tags -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'PetHelp') }}">
        <meta name="theme-color" content="#3b82f6">
        <meta name="format-detection" content="telephone=no">

        <!-- Title -->
        <title>@yield('title', config('app.name', 'PetHelp'))</title>

        <!-- Optional Meta Section -->
        @if(View::hasSection('meta'))
            @yield('meta')
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="desktop-container min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-warm-600 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
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

                        <!-- Navigation Links -->
                        <div class="hidden md:flex space-x-8">
                            <a href="/" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Strona główna</a>
                            <a href="#jak-to-dziala" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Jak to działa</a>
                            <a href="#opiekunowie" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Zostań opiekunem</a>
                            <a href="#kontakt" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200">Kontakt</a>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="flex items-center space-x-3">
                            <!-- Dark Mode Toggle -->
                            <x-dark-mode-toggle size="sm" :show-label="false" />

                            @auth
                                <x-ui.button variant="outline" size="sm" href="{{ route('dashboard') }}">
                                    <x-icon name="briefcase" class="w-4 h-4 mr-2" />
                                    Panel
                                </x-ui.button>
                            @else
                                <x-ui.button variant="ghost" size="sm">
                                    Zaloguj się
                                </x-ui.button>
                                <x-ui.button variant="primary" size="sm">
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
                            <a href="/" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Strona główna</a>
                            <a href="#jak-to-dziala" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Jak to działa</a>
                            <a href="#opiekunowie" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Zostań opiekunem</a>
                            <a href="#kontakt" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl font-medium transition-colors duration-200">Kontakt</a>
                            <hr class="my-2 border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col space-y-2 pt-2">
                                @auth
                                    <x-ui.button variant="outline" size="sm" fullWidth="true">Panel</x-ui.button>
                                @else
                                    <x-ui.button variant="ghost" size="sm" fullWidth="true">Zaloguj się</x-ui.button>
                                    <x-ui.button variant="primary" size="sm" fullWidth="true">Zarejestruj się</x-ui.button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="desktop-window">
            @if(View::hasSection('content'))
                @yield('content')
            @else
                {{ $slot }}
            @endif
            </main>
        </div>

        <!-- Alpine.js Store for Mobile Menu is defined in alpine-components.js -->

        <!-- Global notification components -->
        <livewire:notification-toast />
        <livewire:confirmation-modal />

        @livewireScripts
        @stack('scripts')
    </body>
</html>
