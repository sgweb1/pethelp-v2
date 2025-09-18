<x-layouts.app>
    <x-slot name="title">PetHelp - Znajdź idealnego opiekuna dla Twojego pupila</x-slot>

    <!-- Hero Section -->
    <section class="relative pt-20 pb-32 overflow-hidden">
        <!-- Background with gradient and pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="2"/%3E%3Ccircle cx="7" cy="27" r="2"/%3E%3Ccircle cx="7" cy="47" r="2"/%3E%3Ccircle cx="27" cy="7" r="2"/%3E%3Ccircle cx="27" cy="27" r="2"/%3E%3Ccircle cx="27" cy="47" r="2"/%3E%3Ccircle cx="47" cy="7" r="2"/%3E%3Ccircle cx="47" cy="27" r="2"/%3E%3Ccircle cx="47" cy="47" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <!-- Main heading -->
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold mb-8 leading-tight animate-slide-up">
                <span class="block">Znajdź idealnego</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-warm-400 to-nature-400">
                    opiekuna
                </span>
                <span class="block">dla swojego pupila</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl text-white/90 mb-12 max-w-3xl mx-auto leading-relaxed animate-slide-up" style="animation-delay: 0.2s">
                Bezpieczna i profesjonalna opieka nad zwierzętami.
                Zweryfikowani opiekunowie w Twojej okolicy czekają na Twojego pupila.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-lg mx-auto mb-16 animate-slide-up" style="animation-delay: 0.4s">
                <x-ui.button
                    variant="secondary"
                    size="lg"
                    fullWidth="true"
                    class="sm:w-auto bg-white text-primary-600 hover:bg-gray-50 shadow-large"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Znajdź opiekuna
                </x-ui.button>

                <x-ui.button
                    variant="warm"
                    size="lg"
                    fullWidth="true"
                    class="sm:w-auto shadow-large"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Zostań opiekunem
                </x-ui.button>
            </div>

            <!-- Trust Indicators -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 opacity-80 animate-slide-up" style="animation-delay: 0.6s">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">1,200+</div>
                    <div class="text-white/80 text-sm">Zweryfikowanych opiekunów</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">15,000+</div>
                    <div class="text-white/80 text-sm">Szczęśliwych pupili</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">98%</div>
                    <div class="text-white/80 text-sm">Zadowolonych klientów</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">24/7</div>
                    <div class="text-white/80 text-sm">Wsparcie</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 sm:mb-12 lg:mb-16 text-gray-900">
                Dlaczego PetHelp?
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">🛡️</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">Bezpieczeństwo</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Wszyscy opiekunowie są weryfikowani i ubezpieczeni. Twój pupil jest w bezpiecznych rękach.</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">📍</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">W Twojej okolicy</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Znajdź opiekunów w pobliżu Twojego domu. Wygodnie i bez stresu dla zwierzęcia.</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">💬</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">Komunikacja real-time</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Bądź w stałym kontakcie z opiekunem. Otrzymuj zdjęcia i aktualizacje na żywo.</p>
                </div>

                <!-- Feature 4 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">💳</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">Bezpieczne płatności</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Płać bezpiecznie online. Środki są blokowane do zakończenia usługi.</p>
                </div>

                <!-- Feature 5 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">⭐</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">System ocen</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Sprawdź opinie innych właścicieli. Wybierz najlepszych opiekunów.</p>
                </div>

                <!-- Feature 6 -->
                <div class="text-center p-6 sm:p-8 rounded-2xl shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 bg-white">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6">📱</div>
                    <h3 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 text-gray-900">Mobilna aplikacja</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">Zarządzaj rezerwacjami z telefonu. Wszystko pod ręką, gdy tego potrzebujesz.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-gray-50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 text-center">
                <div class="p-4 sm:p-6">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-indigo-600 mb-1 sm:mb-2">1,200+</div>
                    <div class="text-sm sm:text-base lg:text-lg text-gray-600">Zweryfikowanych opiekunów</div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-indigo-600 mb-1 sm:mb-2">15,000+</div>
                    <div class="text-sm sm:text-base lg:text-lg text-gray-600">Szczęśliwych pupili</div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-indigo-600 mb-1 sm:mb-2">98%</div>
                    <div class="text-sm sm:text-base lg:text-lg text-gray-600">Zadowolonych klientów</div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-indigo-600 mb-1 sm:mb-2">24/7</div>
                    <div class="text-sm sm:text-base lg:text-lg text-gray-600">Wsparcie</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16 sm:py-20 text-center text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6">Zacznij już dziś!</h2>
            <p class="text-lg sm:text-xl md:text-2xl mb-8 sm:mb-10 opacity-90 max-w-2xl mx-auto">
                Dołącz do tysięcy zadowolonych właścicieli zwierząt
            </p>
            <a href="{{ route('register') }}" class="inline-block w-full sm:w-auto px-8 sm:px-10 py-3 sm:py-4 bg-white text-indigo-600 rounded-full font-semibold text-base sm:text-lg hover:bg-gray-50 hover:transform hover:-translate-y-1 transition-all duration-300 shadow-lg max-w-xs sm:max-w-none mx-auto">
                Utwórz darmowe konto
            </a>
        </div>
    </section>
</x-layouts.app>
