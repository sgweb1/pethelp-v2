{{--
    Publiczna strona usług profesjonalnych

    Wyświetla katalog profesjonalnych usług związanych ze zwierzętami
    takich jak weterynarze, fryzjerzy dla psów, hotele dla zwierząt itp.
--}}

<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">

        {{-- Header sekcji --}}
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        💼 Usługi profesjonalne
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Znajdź sprawdzonych profesjonalistów dla swojego pupila
                    </p>
                </div>
            </div>
        </div>

        {{-- Główna treść --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            {{-- Kategorie usług profesjonalnych --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🏥</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Weterynarze</h3>
                    <p class="text-gray-600 dark:text-gray-300">Opieka zdrowotna dla zwierząt</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">✂️</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Groomerzy</h3>
                    <p class="text-gray-600 dark:text-gray-300">Pielęgnacja i strzyżenie</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🏨</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hotele dla zwierząt</h3>
                    <p class="text-gray-600 dark:text-gray-300">Zakwaterowanie na wakacje</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🎓</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Trenerzy</h3>
                    <p class="text-gray-600 dark:text-gray-300">Szkolenie i tresura</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🚚</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Transport</h3>
                    <p class="text-gray-600 dark:text-gray-300">Bezpieczny przewóz zwierząt</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🛍️</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sklepy zoologiczne</h3>
                    <p class="text-gray-600 dark:text-gray-300">Karma i akcesoria</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">💊</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Apteki weterynaryjne</h3>
                    <p class="text-gray-600 dark:text-gray-300">Leki i suplementy</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">📸</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Fotografia zwierząt</h3>
                    <p class="text-gray-600 dark:text-gray-300">Profesjonalne sesje</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="text-4xl mb-4">🎨</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inne usługi</h3>
                    <p class="text-gray-600 dark:text-gray-300">Specjalistyczna opieka</p>
                </div>
            </div>

            {{-- Informacja o potrzebie logowania --}}
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-300 mb-4">
                    Przeglądanie usług wymaga logowania
                </h2>
                <p class="text-purple-800 dark:text-purple-200 mb-6 max-w-2xl mx-auto">
                    Aby przeglądać profile profesjonalistów, czytać opinie i kontaktować się z nimi,
                    musisz być zalogowany. To gwarantuje bezpieczeństwo i jakość naszych usług.
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