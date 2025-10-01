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
                                    <a href="{{ route('profile.dashboard') }}" class="px-4 lg:px-6 py-2 text-sm lg:text-base text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all duration-300">Panel</a>
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
                                    <a href="{{ route('profile.dashboard') }}" class="block w-full text-center px-4 py-2 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-full font-semibold hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white transition-all">Panel użytkownika</a>
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

            <!-- Footer -->
            <footer class="bg-gray-900 text-white mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand Section -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center mb-4">
                                <span class="text-2xl mr-2">🐾</span>
                                <span class="text-2xl font-bold text-indigo-400">PetHelp</span>
                            </div>
                            <p class="text-gray-300 mb-4 max-w-md">
                                Łączymy właścicieli zwierząt z najlepszymi opiekunami i usługami w Twojej okolicy.
                                Zaufana platforma dla miłośników zwierząt.
                            </p>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Szybkie linki</h3>
                            <ul class="space-y-2">
                                <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Strona główna</a></li>
                                <li><a href="/search" class="text-gray-300 hover:text-white transition-colors">Szukaj opiekunów</a></li>
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Zostań opiekunem</a></li>
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">O nas</a></li>
                            </ul>
                        </div>

                        <!-- Support -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Wsparcie</h3>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Centrum pomocy</a></li>
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Kontakt</a></li>
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Regulamin</a></li>
                                <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Polityka prywatności</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bottom Bar -->
                    <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-400 text-sm">
                            © {{ date('Y') }} PetHelp. Wszystkie prawa zastrzeżone.
                        </p>
                        <div class="flex items-center space-x-4 mt-4 md:mt-0">
                            <span class="text-gray-400 text-sm">Dostępne na:</span>
                            <div class="flex space-x-2">
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.523 15.3414c-.5665 0-.9311-.0481-1.0775-.1442-.3467-.2277-.9276-.8526-.9276-1.906 0-.5386.2165-1.0775.6518-1.6162.4353-.5386 1.0103-1.0103 1.584-1.3913.2882-.1923.6518-.4353.996-.4353.4353 0 .7235.1731.8696.5195.1442.3467.0962.7716-.1442 1.1183-.2404.3467-.6518.7716-1.2268 1.2268-.5748.4553-1.2267.8696-1.9059 1.1183-.1923.0692-.4353.1052-.7235.1052z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Alpine.js Store for Mobile Menu is defined in alpine-components.js -->

        @livewireScripts
    </body>
</html>