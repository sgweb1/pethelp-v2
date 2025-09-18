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
    <section class="bg-gray-50 py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    Dlaczego PetHelp?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Oferujemy kompletną i bezpieczną platformę do opieki nad zwierzętami
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                <!-- Feature 1: Bezpieczeństwo -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-success-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Bezpieczeństwo</h3>
                    <p class="text-gray-600 leading-relaxed">Wszyscy opiekunowie są weryfikowani i ubezpieczeni. Twój pupil jest w bezpiecznych rękach.</p>
                </div>

                <!-- Feature 2: Lokalizacja -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-primary-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">W Twojej okolicy</h3>
                    <p class="text-gray-600 leading-relaxed">Znajdź opiekunów w pobliżu Twojego domu. Wygodnie i bez stresu dla zwierzęcia.</p>
                </div>

                <!-- Feature 3: Komunikacja -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-info-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Komunikacja real-time</h3>
                    <p class="text-gray-600 leading-relaxed">Bądź w stałym kontakcie z opiekunem. Otrzymuj zdjęcia i aktualizacje na żywo.</p>
                </div>

                <!-- Feature 4: Płatności -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-warm-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Bezpieczne płatności</h3>
                    <p class="text-gray-600 leading-relaxed">Płać bezpiecznie online. Środki są blokowane do zakończenia usługi.</p>
                </div>

                <!-- Feature 5: Oceny -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-warning-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">System ocen</h3>
                    <p class="text-gray-600 leading-relaxed">Sprawdź opinie innych właścicieli. Wybierz najlepszych opiekunów.</p>
                </div>

                <!-- Feature 6: Mobilność -->
                <div class="group text-center p-8 rounded-3xl bg-white shadow-soft hover:shadow-large transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 mx-auto mb-6 bg-nature-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-nature-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Mobilna aplikacja</h3>
                    <p class="text-gray-600 leading-relaxed">Zarządzaj rezerwacjami z telefonu. Wszystko pod ręką, gdy tego potrzebujesz.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 sm:py-24 overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-warm-600">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                Zacznij już dziś!
            </h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto leading-relaxed">
                Dołącz do tysięcy zadowolonych właścicieli zwierząt i znajdź idealnego opiekuna dla swojego pupila
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-lg mx-auto">
                <x-ui.button
                    variant="secondary"
                    size="xl"
                    fullWidth="true"
                    class="sm:w-auto bg-white text-primary-600 hover:bg-gray-50 shadow-large"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Utwórz darmowe konto
                </x-ui.button>

                <x-ui.button
                    variant="outline"
                    size="xl"
                    fullWidth="true"
                    class="sm:w-auto border-white text-white hover:bg-white/10"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Dowiedz się więcej
                </x-ui.button>
            </div>

            <!-- Small trust indicators -->
            <div class="mt-12 pt-8 border-t border-white/20">
                <p class="text-white/70 text-sm mb-4">Zaufali nam już:</p>
                <div class="flex flex-wrap justify-center items-center gap-8 text-white/80">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-nature-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">1,200+ opiekunów</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-warning-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="text-sm">98% zadowolenia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-info-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">24/7 wsparcie</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
