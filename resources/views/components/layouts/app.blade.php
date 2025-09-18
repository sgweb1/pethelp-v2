<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'PetHelp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-700">
            <!-- Navigation Header -->
            <header class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <nav class="flex justify-between items-center py-4">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <span class="text-2xl mr-2">🐾</span>
                            <span class="text-2xl font-bold text-indigo-600">PetHelp</span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex space-x-8">
                            <a href="/" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Strona główna</a>
                            <a href="#jak-to-dziala" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Jak to działa</a>
                            <a href="#opiekunowie" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Zostań opiekunem</a>
                            <a href="#kontakt" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Kontakt</a>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="hidden sm:flex space-x-2 lg:space-x-4">
                            @auth
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-700">Witaj, {{ auth()->user()->name }}!</span>
                                    <a href="{{ route('dashboard') }}" class="px-4 lg:px-6 py-2 text-sm lg:text-base text-indigo-600 border-2 border-indigo-600 rounded-full font-semibold hover:bg-indigo-600 hover:text-white transition-all duration-300">Panel</a>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 lg:px-6 py-2 text-sm lg:text-base text-gray-600 border-2 border-gray-600 rounded-full font-semibold hover:bg-gray-600 hover:text-white transition-all duration-300">
                                            Wyloguj
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="px-3 lg:px-6 py-2 text-sm lg:text-base text-indigo-600 border-2 border-indigo-600 rounded-full font-semibold hover:bg-indigo-600 hover:text-white transition-all duration-300">Zaloguj</a>
                                <a href="{{ route('register') }}" class="px-3 lg:px-6 py-2 text-sm lg:text-base bg-indigo-600 text-white rounded-full font-semibold hover:bg-indigo-700 hover:transform hover:-translate-y-0.5 transition-all duration-300 shadow-md">Zarejestruj</a>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="text-gray-700 hover:text-indigo-600" x-data x-on:click="$store.mobileMenu.toggle()">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </nav>

                    <!-- Mobile Navigation -->
                    <div class="md:hidden" x-data x-show="$store.mobileMenu.open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                        <div class="px-2 pt-2 pb-4 space-y-1 bg-white/95 backdrop-blur-md rounded-lg shadow-lg mb-4">
                            <!-- Navigation Links -->
                            <a href="/" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium rounded-md transition-colors">Strona główna</a>
                            <a href="#jak-to-dziala" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium rounded-md transition-colors">Jak to działa</a>
                            <a href="#opiekunowie" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium rounded-md transition-colors">Zostań opiekunem</a>
                            <a href="#kontakt" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium rounded-md transition-colors">Kontakt</a>

                            <!-- Mobile Auth Buttons -->
                            <div class="pt-4 border-t border-gray-200 space-y-2">
                                @auth
                                    <div class="text-center text-sm text-gray-700 mb-2">Witaj, {{ auth()->user()->name }}!</div>
                                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-2 text-indigo-600 border-2 border-indigo-600 rounded-full font-semibold hover:bg-indigo-600 hover:text-white transition-all">Panel użytkownika</a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="block w-full text-center px-4 py-2 text-gray-600 border-2 border-gray-600 rounded-full font-semibold hover:bg-gray-600 hover:text-white transition-all">
                                            Wyloguj się
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-indigo-600 border-2 border-indigo-600 rounded-full font-semibold hover:bg-indigo-600 hover:text-white transition-all">Zaloguj się</a>
                                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-full font-semibold hover:bg-indigo-700 transition-all">Zarejestruj się</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
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