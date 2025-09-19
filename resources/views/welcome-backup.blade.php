@extends('layouts.app')

@section('title', 'PetHelp - Znajdź Opiekuna dla Zwierząt | Bezpieczna Opieka nad Pupilami')

@section('meta')
<meta name="description" content="Znajdź zweryfikowanego opiekuna dla swojego psa lub kota w Polsce. ✓ Ubezpieczeni opiekunowie ✓ 24/7 wsparcie ✓ Bezpieczne płatności. Sprawdź dostępność w Twoim mieście!">
<meta name="keywords" content="opiekun dla psa, opieka nad kotem, spacer z psem, hotel dla zwierząt, pet sitter Polska">
<meta name="geo.region" content="PL">
<meta name="geo.placename" content="Polska">

<!-- Open Graph -->
<meta property="og:title" content="PetHelp - Profesjonalna opieka nad zwierzętami w Polsce">
<meta property="og:description" content="Platforma łącząca właścicieli zwierząt z zweryfikowanymi opiekunami. Bezpieczna opieka nad pupilami w Twojej okolicy.">
<meta property="og:image" content="{{ asset('images/og-pethelp-social.jpg') }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:type" content="website">

<!-- Preload critical resources -->
<link rel="preload" href="{{ asset('images/hero-pets.webp') }}" as="image">
<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- Schema.org markup -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "PetHelp",
    "description": "Platforma do znajdowania opiekunów zwierząt w Polsce",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search') }}?location={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endsection

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-blue-900 dark:to-purple-900 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                <defs>
                    <pattern id="pawprints" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <path d="M8 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm4 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm2 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-4 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" fill="currentColor"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#pawprints)"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                        Znajdź <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">idealnego opiekuna</span>
                        <br>dla swojego pupila
                    </h1>

                    <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 max-w-xl">
                        Połącz się z zweryfikowanymi opiekunami zwierząt w Twojej okolicy.
                        Bezpieczna opieka, gdy Ciebie nie ma w domu.
                    </p>

                    <!-- Trust Indicators -->
                    <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <x-icon name="heroicon-s-shield-check" class="w-5 h-5 text-green-500 mr-2" />
                            <span>Zweryfikowani opiekunowie</span>
                        </div>
                        <div class="flex items-center">
                            <x-icon name="heroicon-s-heart" class="w-5 h-5 text-red-500 mr-2" />
                            <span>24/7 wsparcie</span>
                        </div>
                        <div class="flex items-center">
                            <x-icon name="heroicon-s-lock-closed" class="w-5 h-5 text-blue-500 mr-2" />
                            <span>Bezpieczne płatności</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('search') }}"
                           class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 mr-2" />
                            Znajdź opiekuna
                        </a>

                        <a href="{{ route('register') }}?type=sitter"
                           class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border-2 border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition-all duration-300">
                            <x-icon name="heroicon-s-user-plus" class="w-5 h-5 mr-2" />
                            Zostań opiekunem
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-6 text-center lg:text-left">
                        <div>
                            <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">10K+</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Szczęśliwych pupili</div>
                        </div>
                        <div>
                            <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">2.5K+</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Zweryfikowanych opiekunów</div>
                        </div>
                        <div>
                            <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">50+</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Miast w Polsce</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative">
                    <div class="relative z-10">
                        <img src="{{ asset('images/hero-pets.webp') }}"
                             alt="Szczęśliwy pies z opiekunem"
                             class="w-full h-auto rounded-2xl shadow-2xl"
                             loading="eager">
                    </div>

                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-yellow-200 dark:bg-yellow-600 rounded-full opacity-20 animate-pulse"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-purple-200 dark:bg-purple-600 rounded-full opacity-20 animate-pulse delay-1000"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 lg:py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Jak to działa?
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Proste kroki do znalezienia idealnej opieki dla Twojego pupila
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Wyszukaj opiekuna
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Wprowadź swoją lokalizację i znajdź zweryfikowanych opiekunów w okolicy.
                        Przeglądaj profile, opinie i zdjęcia.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Zarezerwuj wizytę
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Skontaktuj się z opiekunem i umów spotkanie.
                        Sprawdź dostępność i zarezerwuj dogodny termin.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Ciesz się spokojem
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Otrzymuj aktualizacje i zdjęcia podczas opieki.
                        Płać bezpiecznie przez platformę.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Usługi dla Twojego pupila
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Profesjonalna opieka dostosowana do potrzeb Twojego zwierzęcia
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Dog Walking -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="heroicon-o-map" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Spacery</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Regularne spacery dopasowane do potrzeb Twojego psa
                    </p>
                </div>

                <!-- Pet Sitting -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="heroicon-o-home" class="w-8 h-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Opieka w domu</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Opiekun przyjedzie do Ciebie lub przyjmie pupila u siebie
                    </p>
                </div>

                <!-- Grooming -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="heroicon-o-sparkles" class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Pielęgnacja</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Profesjonalna pielęgnacja i higiena Twojego pupila
                    </p>
                </div>

                <!-- Training -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="heroicon-o-academic-cap" class="w-8 h-8 text-orange-600 dark:text-orange-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Trening</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Szkolenie i nauka dobrych nawyków dla Twojego psa
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Safety Section -->
    <section class="py-16 lg:py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Bezpieczeństwo na pierwszym miejscu
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                        Każdy opiekun przechodzi dokładną weryfikację, aby zapewnić najwyższy poziom bezpieczeństwa dla Twojego pupila.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Weryfikacja dokumentów</h3>
                                <p class="text-gray-600 dark:text-gray-300">Każdy opiekun potwierdza swoją tożsamość i przechodzi weryfikację.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubezpieczenie OC</h3>
                                <p class="text-gray-600 dark:text-gray-300">Wszyscy opiekunowie są objęci ubezpieczeniem odpowiedzialności cywilnej.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System ocen i opinii</h3>
                                <p class="text-gray-600 dark:text-gray-300">Przeczytaj opinie innych właścicieli i oceń jakość usług.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">24/7 wsparcie</h3>
                                <p class="text-gray-600 dark:text-gray-300">Nasze wsparcie jest dostępne przez całą dobę w przypadku problemów.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <img src="{{ asset('images/safety-pets.webp') }}"
                         alt="Bezpieczna opieka nad zwierzętami"
                         class="w-full h-auto rounded-2xl shadow-lg"
                         loading="lazy">

                    <!-- Trust Badge -->
                    <div class="absolute -bottom-6 left-6 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-lg">
                        <div class="flex items-center">
                            <x-icon name="heroicon-s-shield-check" class="w-8 h-8 text-green-500 mr-3" />
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Zweryfikowani opiekunowie</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">100% sprawdzonych profili</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Co mówią nasi klienci
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Przeczytaj opinie zadowolonych właścicieli zwierząt z całej Polski
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "Fantastyczna platforma! Anna świetnie opiekowała się moim Reksiem podczas weekendu.
                        Otrzymywałam regularne aktualizacje i zdjęcia. Bardzo polecam!"
                    </p>
                    <div class="flex items-center">
                        <img src="{{ asset('images/testimonial-1.webp') }}"
                             alt="Zdjęcie Marii Kowalskiej"
                             class="w-10 h-10 rounded-full mr-3"
                             loading="lazy">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Maria Kowalska</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Warszawa</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "Proces rezerwacji był bardzo prosty. Mikołaj zaopiekował się moim kotem Felixem
                        podczas urlopu. Felix był zadowolony i spokojny po moim powrocie."
                    </p>
                    <div class="flex items-center">
                        <img src="{{ asset('images/testimonial-2.webp') }}"
                             alt="Zdjęcie Piotra Nowaka"
                             class="w-10 h-10 rounded-full mr-3"
                             loading="lazy">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Piotr Nowak</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Kraków</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                            <x-icon name="heroicon-s-star" class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "Jako pracujący rodzic potrzebuję regularnych spacerów dla mojego psa.
                        Kasia jest niezawodna i Luna zawsze wraca szczęśliwa i zmęczona!"
                    </p>
                    <div class="flex items-center">
                        <img src="{{ asset('images/testimonial-3.webp') }}"
                             alt="Zdjęcie Agnieszki Wiśniewskiej"
                             class="w-10 h-10 rounded-full mr-3"
                             loading="lazy">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Agnieszka Wiśniewska</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Gdańsk</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section for Sitters -->
    <section class="py-16 lg:py-24 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                Dołącz do społeczności opiekunów PetHelp
            </h2>
            <p class="text-xl text-purple-100 mb-8 max-w-3xl mx-auto">
                Kochasz zwierzęta i chcesz zarabiać opiekując się nimi?
                Dołącz do tysięcy zweryfikowanych opiekunów w całej Polsce.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">Elastyczny grafik</div>
                    <div class="text-purple-100">Pracuj kiedy chcesz</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">50-150 zł/dzień</div>
                    <div class="text-purple-100">Konkurencyjne stawki</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">Bezpłatne ubezpieczenie</div>
                    <div class="text-purple-100">Pełne pokrycie OC</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}?type=sitter"
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-purple-600 bg-white hover:bg-gray-100 rounded-lg transition-colors duration-300">
                    <x-icon name="heroicon-s-user-plus" class="w-5 h-5 mr-2" />
                    Zostań opiekunem
                </a>

                <a href="#"
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-purple-600 rounded-lg transition-all duration-300">
                    <x-icon name="heroicon-s-information-circle" class="w-5 h-5 mr-2" />
                    Dowiedz się więcej
                </a>
            </div>
        </div>
    </section>

    <!-- Subscription Plans Teaser -->
    @guest
    <section class="py-16 lg:py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Wybierz plan idealny dla Ciebie
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Rozpocznij z planem darmowym lub wybierz premium dla dodatkowych funkcji
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Basic Plan -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic</h3>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Darmowy</div>
                    <div class="text-gray-500 dark:text-gray-400 mb-6">na zawsze</div>
                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Do 3 ogłoszeń</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Podstawowe wyszukiwanie</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">System wiadomości</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                       class="block w-full py-3 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                        Rozpocznij za darmo
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-8 text-center border-2 border-blue-500 relative">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                            Najpopularniejszy
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Pro</h3>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">49 PLN</div>
                    <div class="text-gray-500 dark:text-gray-400 mb-6">/miesiąc</div>
                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Nielimitowane ogłoszenia</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Zaawansowane wyszukiwanie</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Analityka i statystyki</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Badge "Zweryfikowany"</span>
                        </li>
                    </ul>
                    <a href="{{ route('subscription.plans') }}"
                       class="block w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Wybierz Pro
                    </a>
                </div>

                <!-- Premium Plan -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Premium</h3>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">99 PLN</div>
                    <div class="text-gray-500 dark:text-gray-400 mb-6">/miesiąc</div>
                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Wszystko z Pro +</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">AI-powered matching</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Promowane ogłoszenia</span>
                        </li>
                        <li class="flex items-center">
                            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                            <span class="text-gray-700 dark:text-gray-300">Priorytetowe wsparcie</span>
                        </li>
                    </ul>
                    <a href="{{ route('subscription.plans') }}"
                       class="block w-full py-3 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                        Wybierz Premium
                    </a>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('subscription.plans') }}"
                   class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                    Zobacz wszystkie plany i funkcje
                    <x-icon name="heroicon-s-arrow-right" class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </section>
    @endguest

    <!-- FAQ Section -->
    <section class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Często zadawane pytania
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">
                    Znajdź odpowiedzi na najczęściej zadawane pytania
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Jak sprawdzani są opiekunowie?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Każdy opiekun przechodzi dokładną weryfikację dokumentów, wywiad wideo oraz sprawdzenie referencji.
                        Wszyscy opiekunowie mają ubezpieczenie OC i przechodzą regularne szkolenia.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Ile kosztują usługi opiekunów?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Ceny różnią się w zależności od rodzaju usługi i lokalizacji. Spacer z psem kosztuje średnio 25-40 PLN,
                        opieka dzienna 80-150 PLN, a opieka nocna 120-250 PLN. Każdy opiekun ustala własne stawki.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Co jeśli coś pójdzie nie tak podczas opieki?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Wszystkie usługi są objęte ubezpieczeniem OC. W przypadku problemów, nasze wsparcie 24/7
                        natychmiast podejmie działania. Oferujemy także gwarancję zadowolenia - jeśli nie jesteś zadowolony, zwrócimy pieniądze.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Czy mogę anulować rezerwację?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Tak, możesz anulować rezerwację bezpłatnie do 24 godzin przed planowaną usługą.
                        W przypadku późniejszej anulacji może zostać naliczona opłata zgodnie z polityką opiekuna.
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#"
                   class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                    Zobacz wszystkie pytania i odpowiedzi
                    <x-icon name="heroicon-s-arrow-right" class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-16 lg:py-24 bg-gradient-to-r from-blue-600 to-purple-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                Gotowy na spokój o swojego pupila?
            </h2>
            <p class="text-xl text-blue-100 mb-12 max-w-3xl mx-auto">
                Dołącz do tysięcy zadowolonych właścicieli, którzy zaufali PetHelp.
                Znajdź idealnego opiekuna już dziś!
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('search') }}"
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white hover:bg-gray-100 rounded-lg transition-colors duration-300">
                    <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 mr-2" />
                    Znajdź opiekuna teraz
                </a>

                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-blue-600 rounded-lg transition-all duration-300">
                    <x-icon name="heroicon-s-user-plus" class="w-5 h-5 mr-2" />
                    Zarejestruj się za darmo
                </a>
                @endguest
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Intersection Observer for animations
if ('IntersectionObserver' in window) {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);

    // Observe sections for animation
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });
}
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Performance optimizations */
img {
    will-change: transform;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Custom focus styles for accessibility */
a:focus,
button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .text-gray-600 {
        color: #000;
    }
    .text-gray-500 {
        color: #333;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse,
    .group-hover\:scale-110,
    .hover\:scale-105 {
        animation: none;
        transform: none;
    }
}
</style>
@endpush

@endsection
