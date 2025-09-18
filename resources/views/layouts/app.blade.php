<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'PetHelp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="desktop-container min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-warm-600">
            <!-- Navigation Header -->
            <header class="bg-white/95 backdrop-blur-md shadow-soft sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <nav class="flex justify-between items-center py-4">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <div class="w-8 h-8 mr-3 bg-primary-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-primary-700">PetHelp</span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex space-x-8">
                            <a href="/" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">Strona główna</a>
                            <a href="#jak-to-dziala" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">Jak to działa</a>
                            <a href="#opiekunowie" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">Zostań opiekunem</a>
                            <a href="#kontakt" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">Kontakt</a>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="flex space-x-3">
                            @auth
                                <x-ui.button variant="outline" size="sm" href="{{ route('dashboard') }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v14l-4-2-4 2V5z"></path>
                                    </svg>
                                    Panel
                                </x-ui.button>
                            @else
                                <x-ui.button variant="ghost" size="sm">
                                    Zaloguj się
                                </x-ui.button>
                                <x-ui.button variant="primary" size="sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Zarejestruj się
                                </x-ui.button>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="p-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-xl transition-colors duration-200" x-data x-on:click="$store.mobileMenu.toggle()">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </nav>

                    <!-- Mobile Navigation -->
                    <div class="md:hidden" x-data x-show="$store.mobileMenu.open" x-transition>
                        <div class="px-4 py-4 space-y-2 bg-white/95 backdrop-blur-md rounded-2xl shadow-large mb-4">
                            <a href="/" class="block px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl font-medium transition-colors duration-200">Strona główna</a>
                            <a href="#jak-to-dziala" class="block px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl font-medium transition-colors duration-200">Jak to działa</a>
                            <a href="#opiekunowie" class="block px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl font-medium transition-colors duration-200">Zostań opiekunem</a>
                            <a href="#kontakt" class="block px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl font-medium transition-colors duration-200">Kontakt</a>
                            <hr class="my-2 border-gray-200">
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
                {{ $slot }}
            </main>
        </div>

        <!-- Alpine.js Store for Mobile Menu -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('mobileMenu', {
                    open: false,
                    toggle() {
                        this.open = !this.open;
                    }
                });
            });
        </script>

        @livewireScripts
    </body>
</html>
