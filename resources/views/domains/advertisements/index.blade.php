{{--
    Publiczna strona ogłoszeń

    Wyświetla listę wszystkich aktywnych ogłoszeń w formie przyciągającej uwagę
    z możliwością filtrowania po kategoriach i lokalizacji.
--}}

<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">

        {{-- Header sekcji --}}
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        📢 Ogłoszenia
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Znajdź idealne zwierzę, sprzedaj akcesoria lub znajdź zgubionego pupila
                    </p>
                </div>
            </div>
        </div>

        {{-- Główna treść --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            {{-- Kategorie ogłoszeń --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🐕</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Adopcja</h3>
                    <p class="text-gray-600 dark:text-gray-300">Znajdź nowego przyjaciela</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">💰</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sprzedaż</h3>
                    <p class="text-gray-600 dark:text-gray-300">Kupuj i sprzedawaj</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🔍</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Zaginione</h3>
                    <p class="text-gray-600 dark:text-gray-300">Pomóż znaleźć pupila</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🛍️</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Akcesoria</h3>
                    <p class="text-gray-600 dark:text-gray-300">Wszystko dla pupila</p>
                </div>
            </div>

            {{-- Informacja o potrzebie logowania --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🔐</div>
                <h2 class="text-2xl font-bold text-blue-900 dark:text-blue-300 mb-4">
                    Oglądanie ogłoszeń wymaga logowania
                </h2>
                <p class="text-blue-800 dark:text-blue-200 mb-6 max-w-2xl mx-auto">
                    Aby przeglądać ogłoszenia i kontaktować się z ich autorami, musisz być zalogowany.
                    To pozwala nam zapewnić bezpieczeństwo naszej społeczności.
                </p>

                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-ui.button variant="primary" size="lg" href="{{ route('login') }}">
                            <x-icon name="user" class="w-5 h-5 mr-2" />
                            Zaloguj się
                        </x-ui.button>
                        <x-ui.button variant="outline" size="lg" href="{{ route('register') }}">
                            Zarejestruj się
                        </x-ui.button>
                    </div>
                @else
                    <x-ui.button variant="primary" size="lg" href="{{ route('profile.dashboard') }}">
                        <x-icon name="briefcase" class="w-5 h-5 mr-2" />
                        Przejdź do panelu
                    </x-ui.button>
                @endguest
            </div>

        </div>
    </div>
</x-layouts.app>