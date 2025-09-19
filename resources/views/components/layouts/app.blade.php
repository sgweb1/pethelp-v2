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

        <title>{{ $title ?? config('app.name', 'PetHelp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <!-- Skip Navigation Link -->
        <a href="#main-content" class="skip-navigation">Przejdź do głównej treści</a>

        <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-700 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <!-- Navigation Header -->
            <header class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-lg sticky top-0 z-50" role="banner">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <nav class="flex justify-between items-center py-4" role="navigation" aria-label="Główna nawigacja">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <span class="text-2xl mr-2" aria-hidden="true">🐾</span>
                            <span class="text-2xl font-bold text-indigo-600">PetHelp</span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex space-x-8" role="menubar">
                            <a href="/" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors link-accessible" role="menuitem" aria-current="{{ request()->is('/') ? 'page' : 'false' }}">Strona główna</a>
                            <a href="#jak-to-dziala" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors link-accessible" role="menuitem">Jak to działa</a>
                            <a href="#opiekunowie" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors link-accessible" role="menuitem">Zostań opiekunem</a>
                            <a href="#kontakt" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors link-accessible" role="menuitem">Kontakt</a>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="hidden sm:flex items-center space-x-2 lg:space-x-4">
                            <!-- Dark Mode Toggle -->
                            <x-dark-mode-toggle size="sm" :show-label="false" />

                            @auth
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Witaj, {{ auth()->user()->name }}!</span>
                                    <a href="{{ route('dashboard') }}" class="px-4 lg:px-6 py-2 text-sm lg:text-base text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all duration-300">Panel</a>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 lg:px-6 py-2 text-sm lg:text-base text-gray-600 dark:text-gray-400 border-2 border-gray-600 dark:border-gray-400 rounded-full font-semibold hover:bg-gray-600 dark:hover:bg-gray-500 hover:text-white transition-all duration-300">
                                            Wyloguj
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="px-3 lg:px-6 py-2 text-sm lg:text-base text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all duration-300">Zaloguj</a>
                                <a href="{{ route('register') }}" class="px-3 lg:px-6 py-2 text-sm lg:text-base bg-indigo-600 dark:bg-indigo-500 text-white rounded-full font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-600 hover:transform hover:-translate-y-0.5 transition-all duration-300 shadow-md">Zarejestruj</a>
                            @endauth
                        </div>

                        <!-- Mobile menu button (temporarily hidden to fix Alpine errors) -->
                        <div class="md:hidden hidden">
                            <button type="button" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </nav>

                    <!-- Mobile Navigation (temporarily hidden to fix Alpine errors) -->
                    <div class="md:hidden hidden">
                        <div class="px-2 pt-2 pb-4 space-y-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-lg shadow-lg mb-4">
                            <!-- Navigation Links -->
                            <a href="/" class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 font-medium rounded-md transition-colors">Strona główna</a>
                            <a href="#jak-to-dziala" class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 font-medium rounded-md transition-colors">Jak to działa</a>
                            <a href="#opiekunowie" class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 font-medium rounded-md transition-colors">Zostań opiekunem</a>
                            <a href="#kontakt" class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 font-medium rounded-md transition-colors">Kontakt</a>

                            <!-- Mobile Auth Buttons -->
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                                @auth
                                    <div class="text-center text-sm text-gray-700 dark:text-gray-300 mb-2">Witaj, {{ auth()->user()->name }}!</div>
                                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-2 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all">Panel użytkownika</a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="block w-full text-center px-4 py-2 text-gray-600 dark:text-gray-400 border-2 border-gray-600 dark:border-gray-400 rounded-full font-semibold hover:bg-gray-600 dark:hover:bg-gray-500 hover:text-white transition-all">
                                            Wyloguj się
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all">Zaloguj się</a>
                                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-full font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all">Zarejestruj się</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main id="main-content" tabindex="-1" role="main">
                {{ $slot }}
            </main>
        </div>

        <!-- Alpine.js Store for Mobile Menu is defined in alpine-components.js -->

        @livewireScripts
    </body>
</html>